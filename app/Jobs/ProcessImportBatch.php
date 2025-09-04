<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Services\Import\Processors\BatchProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProcessImportBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $importJobId;
    public int $batchNumber;
    public array $batchData;
    public int $offset;
    public string $tempFilePath;
    public array $config;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $importJobId,
        int $batchNumber,
        array $batchData,
        int $offset,
        string $tempFilePath,
        array $config
    ) {
        $this->importJobId = $importJobId;
        $this->batchNumber = $batchNumber;
        $this->batchData = $batchData;
        $this->offset = $offset;
        $this->tempFilePath = $tempFilePath;
        $this->config = $config;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $startMemory = memory_get_peak_usage(true);

        try {
            // Get import job
            $importJob = ImportJob::find($this->importJobId);
            
            if (!$importJob) {
                Log::error("Import job not found: {$this->importJobId}");
                return;
            }

            // Check if import has been cancelled
            if ($this->isImportCancelled()) {
                Log::info("Batch {$this->batchNumber} cancelled for import {$this->importJobId}");
                return;
            }

            Log::info("Processing batch {$this->batchNumber} for import {$this->importJobId}", [
                'offset' => $this->offset,
                'batch_size' => count($this->batchData),
                'attempt' => $this->attempts()
            ]);

            // Process the batch
            $result = $this->processBatchData($importJob);

            // Store batch result
            $this->storeBatchResult($result);

            // Update overall progress
            $this->updateOverallProgress($result);

            $processingTime = microtime(true) - $startTime;
            $memoryUsage = memory_get_peak_usage(true) - $startMemory;

            Log::info("Completed batch {$this->batchNumber} for import {$this->importJobId}", [
                'processed' => $result['processed'],
                'successful' => $result['successful'],
                'failed' => $result['failed'],
                'processing_time' => round($processingTime, 2) . 's',
                'memory_usage' => $this->formatBytes($memoryUsage)
            ]);

        } catch (\Exception $e) {
            $this->handleBatchFailure($e);
            throw $e; // Re-throw to trigger job retry mechanism
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Batch {$this->batchNumber} failed permanently for import {$this->importJobId}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'attempts' => $this->attempts()
        ]);

        // Store failure result
        $this->storeBatchResult([
            'batch_number' => $this->batchNumber,
            'success' => false,
            'processed' => 0,
            'successful' => 0,
            'failed' => count($this->batchData),
            'error' => $exception->getMessage(),
            'completed_at' => now()
        ]);

        // Update import job status if this was the last batch
        $this->checkAndUpdateImportStatus();
    }

    /**
     * Process the batch data
     */
    protected function processBatchData(ImportJob $importJob): array
    {
        $result = [
            'batch_number' => $this->batchNumber,
            'success' => true,
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
            'warnings' => [],
            'completed_at' => now()
        ];

        try {
            DB::beginTransaction();

            foreach ($this->batchData as $rowIndex => $row) {
                // Check for cancellation during processing
                if ($this->isImportCancelled()) {
                    Log::info("Batch processing cancelled during row processing");
                    break;
                }

                try {
                    $rowResult = $this->processRow($row, $rowIndex, $importJob);
                    
                    $result['processed']++;
                    
                    if ($rowResult['success']) {
                        $result['successful']++;
                    } else {
                        $result['failed']++;
                        $result['errors'][] = $rowResult['error'];
                    }

                    if (!empty($rowResult['warnings'])) {
                        $result['warnings'] = array_merge($result['warnings'], $rowResult['warnings']);
                    }

                } catch (\Exception $e) {
                    $result['failed']++;
                    $result['errors'][] = [
                        'row' => $this->offset + $rowIndex,
                        'error' => $e->getMessage(),
                        'data' => $this->sanitizeRowData($row)
                    ];

                    Log::warning("Row processing error in batch {$this->batchNumber}", [
                        'import_id' => $this->importJobId,
                        'row_index' => $this->offset + $rowIndex,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $result['success'] = false;
            $result['errors'][] = [
                'batch' => $this->batchNumber,
                'error' => 'Batch transaction failed: ' . $e->getMessage()
            ];
            throw $e;
        }

        return $result;
    }

    /**
     * Process individual row
     */
    protected function processRow(array $row, int $rowIndex, ImportJob $importJob): array
    {
        $result = [
            'success' => true,
            'error' => null,
            'warnings' => []
        ];

        try {
            // Get the appropriate processor based on import type
            $processor = $this->getImportProcessor($importJob->import_type);
            
            // Process the row using the specific import processor
            $processed = $processor->processRow($row, $this->offset + $rowIndex, $importJob);
            
            if (!$processed['success']) {
                $result['success'] = false;
                $result['error'] = $processed['error'];
            }

            if (!empty($processed['warnings'])) {
                $result['warnings'] = $processed['warnings'];
            }

        } catch (\Exception $e) {
            $result['success'] = false;
            $result['error'] = [
                'row' => $this->offset + $rowIndex,
                'message' => $e->getMessage(),
                'data' => $this->sanitizeRowData($row)
            ];
        }

        return $result;
    }

    /**
     * Store batch processing result
     */
    protected function storeBatchResult(array $result): void
    {
        $cacheKey = "batch_result_{$this->importJobId}_{$this->batchNumber}";
        Cache::put($cacheKey, $result, 7200); // 2 hours

        // Also store in a summary cache for quick access
        $summaryKey = "batch_summary_{$this->importJobId}";
        $summary = Cache::get($summaryKey, [
            'completed_batches' => 0,
            'total_processed' => 0,
            'total_successful' => 0,
            'total_failed' => 0,
            'last_updated' => now()
        ]);

        $summary['completed_batches']++;
        $summary['total_processed'] += $result['processed'];
        $summary['total_successful'] += $result['successful'];
        $summary['total_failed'] += $result['failed'];
        $summary['last_updated'] = now();

        Cache::put($summaryKey, $summary, 7200);
    }

    /**
     * Update overall import progress
     */
    protected function updateOverallProgress(array $result): void
    {
        // Get current progress
        $progressKey = "import_progress_{$this->importJobId}";
        $progress = Cache::get($progressKey, [
            'processed_records' => 0,
            'total_records' => $this->config['total_rows'],
            'progress_percentage' => 0,
            'updated_at' => now()
        ]);

        // Update progress
        $progress['processed_records'] += $result['processed'];
        $progress['progress_percentage'] = $progress['total_records'] > 0 
            ? (int) (($progress['processed_records'] / $progress['total_records']) * 100)
            : 0;
        $progress['updated_at'] = now();

        Cache::put($progressKey, $progress, 3600);

        // Also update the database periodically (every 5%)
        if ($progress['progress_percentage'] % 5 === 0) {
            ImportJob::where('id', $this->importJobId)->update([
                'processed_records' => $progress['processed_records'],
                'progress_percentage' => $progress['progress_percentage']
            ]);
        }
    }

    /**
     * Check if import has been cancelled
     */
    protected function isImportCancelled(): bool
    {
        return Cache::has("import_cancelled_{$this->importJobId}");
    }

    /**
     * Handle batch failure
     */
    protected function handleBatchFailure(\Exception $e): void
    {
        Log::error("Batch {$this->batchNumber} failed for import {$this->importJobId}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'attempt' => $this->attempts()
        ]);

        // Increment failure count
        $failureKey = "batch_failures_{$this->importJobId}";
        $failures = Cache::get($failureKey, 0);
        Cache::put($failureKey, $failures + 1, 7200);
    }

    /**
     * Check and update import status if this is the last batch
     */
    protected function checkAndUpdateImportStatus(): void
    {
        $summaryKey = "batch_summary_{$this->importJobId}";
        $summary = Cache::get($summaryKey);

        if (!$summary) {
            return;
        }

        // Check if all batches are complete
        if ($summary['completed_batches'] >= $this->config['total_batches']) {
            $this->finalizeImportJob($summary);
        }
    }

    /**
     * Finalize import job when all batches are complete
     */
    protected function finalizeImportJob(array $summary): void
    {
        $importJob = ImportJob::find($this->importJobId);
        
        if (!$importJob) {
            return;
        }

        $status = $summary['total_failed'] > 0 ? 'completed_with_errors' : 'completed';

        $importJob->update([
            'status' => $status,
            'processed_records' => $summary['total_processed'],
            'successful_imports' => $summary['total_successful'],
            'failed_imports' => $summary['total_failed'],
            'progress_percentage' => 100,
            'completed_at' => now(),
            'processing_time_seconds' => now()->diffInSeconds($importJob->started_at)
        ]);

        Log::info("Finalized import {$this->importJobId}", [
            'status' => $status,
            'total_processed' => $summary['total_processed'],
            'successful' => $summary['total_successful'],
            'failed' => $summary['total_failed']
        ]);

        // Clean up cache
        $this->cleanupBatchCache();
    }

    /**
     * Clean up batch processing cache
     */
    protected function cleanupBatchCache(): void
    {
        // Remove batch results
        for ($i = 1; $i <= $this->config['total_batches']; $i++) {
            Cache::forget("batch_result_{$this->importJobId}_{$i}");
        }

        // Remove summary and progress
        Cache::forget("batch_summary_{$this->importJobId}");
        Cache::forget("import_progress_{$this->importJobId}");
        Cache::forget("batch_config_{$this->importJobId}");
    }

    /**
     * Get import processor for specific import type
     */
    protected function getImportProcessor(string $importType): object
    {
        // This would return the appropriate processor based on import type
        // For now, return a generic processor
        return new class {
            public function processRow(array $row, int $rowIndex, ImportJob $importJob): array
            {
                // Simulate row processing
                return [
                    'success' => true,
                    'error' => null,
                    'warnings' => []
                ];
            }
        };
    }

    /**
     * Sanitize row data for logging (remove sensitive information)
     */
    protected function sanitizeRowData(array $row): array
    {
        $sanitized = [];
        foreach ($row as $key => $value) {
            if (in_array(strtolower($key), ['password', 'token', 'secret', 'key'])) {
                $sanitized[$key] = '[REDACTED]';
            } else {
                $sanitized[$key] = is_string($value) ? substr($value, 0, 100) : $value;
            }
        }
        return $sanitized;
    }

    /**
     * Format bytes for logging
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Determine the queue name for this job
     */
    public function viaQueue(): string
    {
        return 'import-batches'; // Use dedicated queue for import batches
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [
            // Add rate limiting or other middleware as needed
        ];
    }
}