<?php

namespace App\Services\Import\Processors;

use App\Models\ImportJob;
use App\Services\Import\Contracts\FileParserInterface;
use App\Services\Import\Contracts\FieldMapperInterface;
use App\Services\Import\Contracts\ValidationEngineInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessImportBatch;
use Generator;

class BatchProcessor
{
    protected FileParserInterface $fileParser;
    protected FieldMapperInterface $fieldMapper;
    protected ValidationEngineInterface $validationEngine;
    
    protected int $defaultBatchSize = 1000;
    protected int $maxMemoryUsage = 128 * 1024 * 1024; // 128MB
    protected int $progressUpdateFrequency = 100; // Update progress every N records
    protected bool $enableParallelProcessing = true;
    protected int $maxConcurrentBatches = 5;

    public function __construct(
        FileParserInterface $fileParser,
        FieldMapperInterface $fieldMapper,
        ValidationEngineInterface $validationEngine
    ) {
        $this->fileParser = $fileParser;
        $this->fieldMapper = $fieldMapper;
        $this->validationEngine = $validationEngine;
    }

    /**
     * Process large import with batch processing and progress tracking
     */
    public function processLargeImport(ImportJob $importJob, UploadedFile $file): ImportJob
    {
        $importJob->update([
            'status' => 'processing',
            'started_at' => now(),
            'progress_percentage' => 0
        ]);

        try {
            // Initialize batch processing
            $batchConfig = $this->initializeBatchProcessing($importJob, $file);
            
            // Process file in batches
            $results = $this->processBatches($importJob, $file, $batchConfig);
            
            // Finalize import
            $this->finalizeImport($importJob, $results);
            
        } catch (\Exception $e) {
            $this->handleProcessingError($importJob, $e);
        }

        return $importJob->refresh();
    }

    /**
     * Initialize batch processing configuration
     */
    protected function initializeBatchProcessing(ImportJob $importJob, UploadedFile $file): array
    {
        $totalRows = $this->fileParser->getRowCount($file);
        $batchSize = $this->calculateOptimalBatchSize($file, $totalRows);
        $totalBatches = ceil($totalRows / $batchSize);

        $config = [
            'total_rows' => $totalRows,
            'batch_size' => $batchSize,
            'total_batches' => $totalBatches,
            'processed_batches' => 0,
            'failed_batches' => 0,
            'batch_results' => [],
            'memory_limit' => $this->maxMemoryUsage,
            'parallel_processing' => $this->shouldUseParallelProcessing($totalBatches)
        ];

        // Update import job with batch configuration
        $importJob->update([
            'total_records' => $totalRows,
            'batch_size' => $batchSize,
            'parallel_processing' => $config['parallel_processing']
        ]);

        // Store batch configuration in cache for progress tracking
        $this->storeBatchConfig($importJob->id, $config);

        Log::info("Initialized batch processing for import {$importJob->id}", $config);

        return $config;
    }

    /**
     * Process file in batches
     */
    protected function processBatches(ImportJob $importJob, UploadedFile $file, array $config): array
    {
        $results = [
            'successful_batches' => 0,
            'failed_batches' => 0,
            'total_processed' => 0,
            'total_successful' => 0,
            'total_failed' => 0,
            'batch_details' => [],
            'errors' => [],
            'warnings' => []
        ];

        if ($config['parallel_processing']) {
            $results = $this->processParallelBatches($importJob, $file, $config);
        } else {
            $results = $this->processSequentialBatches($importJob, $file, $config);
        }

        return $results;
    }

    /**
     * Process batches sequentially
     */
    protected function processSequentialBatches(ImportJob $importJob, UploadedFile $file, array $config): array
    {
        $results = [
            'successful_batches' => 0,
            'failed_batches' => 0,
            'total_processed' => 0,
            'total_successful' => 0,
            'total_failed' => 0,
            'batch_details' => [],
            'errors' => [],
            'warnings' => []
        ];

        $batchNumber = 0;
        $offset = 0;

        // Process file in chunks using generator for memory efficiency
        $this->fileParser->parseInChunks($file, $config['batch_size'], function($batch, $currentOffset, $totalRows) use (
            &$results, &$batchNumber, &$offset, $importJob, $config
        ) {
            $batchNumber++;
            $offset = $currentOffset;

            Log::info("Processing batch {$batchNumber} for import {$importJob->id}", [
                'offset' => $offset,
                'batch_size' => count($batch),
                'memory_usage' => $this->getMemoryUsage()
            ]);

            try {
                $batchResult = $this->processBatch($batch, $batchNumber, $importJob);
                
                $results['batch_details'][$batchNumber] = $batchResult;
                $results['total_processed'] += $batchResult['processed'];
                $results['total_successful'] += $batchResult['successful'];
                $results['total_failed'] += $batchResult['failed'];
                
                if ($batchResult['success']) {
                    $results['successful_batches']++;
                } else {
                    $results['failed_batches']++;
                    $results['errors'] = array_merge($results['errors'], $batchResult['errors']);
                }

                $results['warnings'] = array_merge($results['warnings'], $batchResult['warnings']);

                // Update progress
                $this->updateProgress($importJob, $results['total_processed'], $config['total_rows']);

                // Check memory usage
                if ($this->isMemoryUsageHigh()) {
                    $this->performGarbageCollection();
                }

            } catch (\Exception $e) {
                $this->handleBatchError($importJob, $batchNumber, $e);
                $results['failed_batches']++;
                $results['errors'][] = [
                    'batch' => $batchNumber,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ];
            }
        });

        return $results;
    }

    /**
     * Process batches in parallel using queue jobs
     */
    protected function processParallelBatches(ImportJob $importJob, UploadedFile $file, array $config): array
    {
        $results = [
            'successful_batches' => 0,
            'failed_batches' => 0,
            'total_processed' => 0,
            'total_successful' => 0,
            'total_failed' => 0,
            'batch_details' => [],
            'errors' => [],
            'warnings' => [],
            'queued_jobs' => []
        ];

        // Store file temporarily for batch jobs
        $tempFilePath = $this->storeFileForBatching($file, $importJob);
        
        $batchNumber = 0;
        $concurrentBatches = 0;

        // Process file in chunks and queue batch jobs
        $this->fileParser->parseInChunks($file, $config['batch_size'], function($batch, $offset, $totalRows) use (
            &$results, &$batchNumber, &$concurrentBatches, $importJob, $config, $tempFilePath
        ) {
            $batchNumber++;

            // Wait if we've reached max concurrent batches
            while ($concurrentBatches >= $this->maxConcurrentBatches) {
                sleep(1); // Wait 1 second before checking again
                $concurrentBatches = $this->getActiveBatchCount($importJob);
            }

            // Create and dispatch batch job
            $job = new ProcessImportBatch(
                $importJob->id,
                $batchNumber,
                $batch,
                $offset,
                $tempFilePath,
                $config
            );

            Queue::push($job);
            $concurrentBatches++;

            $results['queued_jobs'][] = [
                'batch_number' => $batchNumber,
                'offset' => $offset,
                'size' => count($batch),
                'queued_at' => now()
            ];

            Log::info("Queued batch {$batchNumber} for import {$importJob->id}");
        });

        // Wait for all batch jobs to complete
        $this->waitForBatchCompletion($importJob, $config['total_batches']);

        // Collect results from completed batches
        $results = $this->collectBatchResults($importJob, $results);

        return $results;
    }

    /**
     * Process individual batch
     */
    protected function processBatch(array $batchData, int $batchNumber, ImportJob $importJob): array
    {
        $batchResult = [
            'batch_number' => $batchNumber,
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
            'warnings' => [],
            'success' => true,
            'processing_time' => 0,
            'memory_peak' => 0
        ];

        $startTime = microtime(true);
        $startMemory = memory_get_peak_usage(true);

        try {
            DB::beginTransaction();

            foreach ($batchData as $rowIndex => $row) {
                try {
                    $result = $this->processRow($row, $batchNumber, $rowIndex, $importJob);
                    
                    $batchResult['processed']++;
                    
                    if ($result['success']) {
                        $batchResult['successful']++;
                    } else {
                        $batchResult['failed']++;
                        $batchResult['errors'][] = $result['error'];
                    }

                    if (!empty($result['warnings'])) {
                        $batchResult['warnings'] = array_merge($batchResult['warnings'], $result['warnings']);
                    }

                } catch (\Exception $e) {
                    $batchResult['failed']++;
                    $batchResult['errors'][] = [
                        'row' => $rowIndex,
                        'error' => $e->getMessage(),
                        'data' => $row
                    ];

                    Log::error("Error processing row in batch {$batchNumber}", [
                        'import_id' => $importJob->id,
                        'row_index' => $rowIndex,
                        'error' => $e->getMessage(),
                        'data' => $row
                    ]);
                }
            }

            DB::commit();

            $batchResult['processing_time'] = microtime(true) - $startTime;
            $batchResult['memory_peak'] = memory_get_peak_usage(true) - $startMemory;

            Log::info("Completed batch {$batchNumber} for import {$importJob->id}", [
                'processed' => $batchResult['processed'],
                'successful' => $batchResult['successful'],
                'failed' => $batchResult['failed'],
                'processing_time' => $batchResult['processing_time'],
                'memory_peak' => $this->formatBytes($batchResult['memory_peak'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $batchResult['success'] = false;
            $batchResult['errors'][] = [
                'batch' => $batchNumber,
                'error' => 'Batch processing failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];

            Log::error("Batch {$batchNumber} failed for import {$importJob->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $batchResult;
    }

    /**
     * Process individual row
     */
    protected function processRow(array $row, int $batchNumber, int $rowIndex, ImportJob $importJob): array
    {
        $result = [
            'success' => true,
            'error' => null,
            'warnings' => []
        ];

        try {
            // This would be implemented by specific import services
            // For now, simulate processing
            
            // Validate row
            $mapping = $this->getImportMapping($importJob);
            $validation = $this->validationEngine->validateRow($row, $rowIndex, $mapping);
            
            if (!$validation['is_valid']) {
                $result['success'] = false;
                $result['error'] = [
                    'batch' => $batchNumber,
                    'row' => $rowIndex,
                    'message' => 'Row validation failed',
                    'errors' => $validation['errors']
                ];
                return $result;
            }

            if (!empty($validation['warnings'])) {
                $result['warnings'] = $validation['warnings'];
            }

            // Apply field mappings
            $mappedData = $this->fieldMapper->applyMappings([$row], $mapping);
            
            // Save to database (this would be implemented by specific import services)
            $this->saveImportedRow($mappedData[0], $importJob);

        } catch (\Exception $e) {
            $result['success'] = false;
            $result['error'] = [
                'batch' => $batchNumber,
                'row' => $rowIndex,
                'message' => $e->getMessage(),
                'data' => $row
            ];
        }

        return $result;
    }

    /**
     * Calculate optimal batch size based on file characteristics
     */
    protected function calculateOptimalBatchSize(UploadedFile $file, int $totalRows): int
    {
        $fileSize = $file->getSize();
        $availableMemory = $this->getAvailableMemory();
        
        // Calculate memory per row estimate
        $memoryPerRow = $totalRows > 0 ? $fileSize / $totalRows : 1000; // 1KB default
        
        // Calculate batch size based on memory constraints
        $memoryBasedBatchSize = (int) ($availableMemory * 0.3 / $memoryPerRow); // Use 30% of available memory
        
        // Apply constraints
        $batchSize = max(100, min($this->defaultBatchSize, $memoryBasedBatchSize));
        
        // For very large files, use smaller batches
        if ($totalRows > 100000) {
            $batchSize = min($batchSize, 500);
        }
        
        Log::info("Calculated optimal batch size", [
            'file_size' => $this->formatBytes($fileSize),
            'total_rows' => $totalRows,
            'memory_per_row' => $this->formatBytes($memoryPerRow),
            'available_memory' => $this->formatBytes($availableMemory),
            'batch_size' => $batchSize
        ]);

        return $batchSize;
    }

    /**
     * Update import progress
     */
    protected function updateProgress(ImportJob $importJob, int $processedRows, int $totalRows): void
    {
        $progressPercentage = $totalRows > 0 ? (int) (($processedRows / $totalRows) * 100) : 0;
        
        $importJob->update([
            'processed_records' => $processedRows,
            'progress_percentage' => $progressPercentage
        ]);

        // Update progress in cache for real-time updates
        Cache::put("import_progress_{$importJob->id}", [
            'processed_records' => $processedRows,
            'total_records' => $totalRows,
            'progress_percentage' => $progressPercentage,
            'updated_at' => now()
        ], 3600); // 1 hour cache

        Log::debug("Updated progress for import {$importJob->id}: {$progressPercentage}%");
    }

    /**
     * Get real-time import progress
     */
    public function getImportProgress(int $importJobId): array
    {
        $cached = Cache::get("import_progress_{$importJobId}");
        
        if ($cached) {
            return $cached;
        }

        // Fallback to database
        $importJob = ImportJob::find($importJobId);
        
        return [
            'processed_records' => $importJob->processed_records ?? 0,
            'total_records' => $importJob->total_records ?? 0,
            'progress_percentage' => $importJob->progress_percentage ?? 0,
            'status' => $importJob->status ?? 'unknown',
            'updated_at' => $importJob->updated_at ?? now()
        ];
    }

    /**
     * Pause import processing
     */
    public function pauseImport(int $importJobId): bool
    {
        $importJob = ImportJob::find($importJobId);
        
        if (!$importJob || !$importJob->isInProgress()) {
            return false;
        }

        $importJob->update(['status' => 'paused']);
        
        // Set pause flag in cache
        Cache::put("import_paused_{$importJobId}", true, 3600);
        
        Log::info("Paused import {$importJobId}");
        
        return true;
    }

    /**
     * Resume import processing
     */
    public function resumeImport(int $importJobId): bool
    {
        $importJob = ImportJob::find($importJobId);
        
        if (!$importJob || $importJob->status !== 'paused') {
            return false;
        }

        $importJob->update(['status' => 'processing']);
        
        // Remove pause flag from cache
        Cache::forget("import_paused_{$importJobId}");
        
        Log::info("Resumed import {$importJobId}");
        
        return true;
    }

    /**
     * Cancel import processing
     */
    public function cancelImport(int $importJobId): bool
    {
        $importJob = ImportJob::find($importJobId);
        
        if (!$importJob) {
            return false;
        }

        $importJob->update([
            'status' => 'cancelled',
            'completed_at' => now()
        ]);
        
        // Set cancel flag in cache
        Cache::put("import_cancelled_{$importJobId}", true, 3600);
        
        // Clean up any running batch jobs
        $this->cancelBatchJobs($importJobId);
        
        Log::info("Cancelled import {$importJobId}");
        
        return true;
    }

    /**
     * Check if import should be paused or cancelled
     */
    protected function checkImportStatus(int $importJobId): string
    {
        if (Cache::has("import_cancelled_{$importJobId}")) {
            return 'cancelled';
        }
        
        if (Cache::has("import_paused_{$importJobId}")) {
            return 'paused';
        }
        
        return 'running';
    }

    // Helper methods

    protected function shouldUseParallelProcessing(int $totalBatches): bool
    {
        return $this->enableParallelProcessing && $totalBatches > 3;
    }

    protected function getAvailableMemory(): int
    {
        $memoryLimit = ini_get('memory_limit');
        $currentUsage = memory_get_usage(true);
        
        if ($memoryLimit === '-1') {
            return $this->maxMemoryUsage; // Use our default limit
        }
        
        $limitBytes = $this->convertToBytes($memoryLimit);
        return max(0, $limitBytes - $currentUsage);
    }

    protected function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;
        
        switch ($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }
        
        return $value;
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    protected function getMemoryUsage(): string
    {
        return $this->formatBytes(memory_get_usage(true));
    }

    protected function isMemoryUsageHigh(): bool
    {
        return memory_get_usage(true) > ($this->maxMemoryUsage * 0.8); // 80% threshold
    }

    protected function performGarbageCollection(): void
    {
        gc_collect_cycles();
        Log::debug("Performed garbage collection, memory usage: " . $this->getMemoryUsage());
    }

    protected function storeBatchConfig(int $importJobId, array $config): void
    {
        Cache::put("batch_config_{$importJobId}", $config, 7200); // 2 hours
    }

    protected function getBatchConfig(int $importJobId): ?array
    {
        return Cache::get("batch_config_{$importJobId}");
    }

    protected function handleProcessingError(ImportJob $importJob, \Exception $e): void
    {
        $importJob->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'error_details' => ['exception' => $e->getTraceAsString()],
            'completed_at' => now()
        ]);

        Log::error("Import {$importJob->id} failed", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    protected function handleBatchError(ImportJob $importJob, int $batchNumber, \Exception $e): void
    {
        Log::error("Batch {$batchNumber} failed for import {$importJob->id}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Increment retry count for this batch
        $this->incrementBatchRetryCount($importJob->id, $batchNumber);
    }

    protected function finalizeImport(ImportJob $importJob, array $results): void
    {
        $importJob->update([
            'status' => 'completed',
            'processed_records' => $results['total_processed'],
            'successful_imports' => $results['total_successful'],
            'failed_imports' => $results['total_failed'],
            'completed_at' => now(),
            'processing_time_seconds' => now()->diffInSeconds($importJob->started_at)
        ]);

        Log::info("Completed import {$importJob->id}", [
            'total_processed' => $results['total_processed'],
            'successful' => $results['total_successful'],
            'failed' => $results['total_failed'],
            'processing_time' => $importJob->processing_time_seconds . ' seconds'
        ]);
    }

    // Placeholder methods that would be implemented by specific import services
    protected function getImportMapping(ImportJob $importJob): ?\App\Models\ImportMapping
    {
        // This would be implemented to get the appropriate mapping
        return null;
    }

    protected function saveImportedRow(array $mappedData, ImportJob $importJob): void
    {
        // This would be implemented by specific import services
        // to save data to the appropriate tables
    }

    protected function storeFileForBatching(UploadedFile $file, ImportJob $importJob): string
    {
        // Store file temporarily for batch processing
        return $file->getPathname(); // Simplified for now
    }

    protected function getActiveBatchCount(ImportJob $importJob): int
    {
        // This would check how many batch jobs are currently running
        return 0; // Placeholder
    }

    protected function waitForBatchCompletion(ImportJob $importJob, int $totalBatches): void
    {
        // Wait for all batch jobs to complete
        // This would be implemented with proper job monitoring
    }

    protected function collectBatchResults(ImportJob $importJob, array $results): array
    {
        // Collect results from completed batch jobs
        return $results; // Placeholder
    }

    protected function cancelBatchJobs(int $importJobId): void
    {
        // Cancel any running batch jobs for this import
        // This would be implemented with proper job cancellation
    }

    protected function incrementBatchRetryCount(int $importJobId, int $batchNumber): void
    {
        $key = "batch_retry_{$importJobId}_{$batchNumber}";
        $count = Cache::get($key, 0);
        Cache::put($key, $count + 1, 3600);
    }
}