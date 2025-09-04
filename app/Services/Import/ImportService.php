<?php

namespace App\Services\Import;

use App\Models\ImportJob;
use App\Models\ImportMapping;
use App\Services\Import\Contracts\ImportServiceInterface;
use App\Services\Import\Contracts\FileParserInterface;
use App\Services\Import\Contracts\FieldMapperInterface;
use App\Services\Import\Contracts\ValidationEngineInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ImportService implements ImportServiceInterface
{
    protected FileParserInterface $fileParser;
    protected FieldMapperInterface $fieldMapper;
    protected ValidationEngineInterface $validationEngine;
    protected string $importType;

    public function __construct(
        FileParserInterface $fileParser,
        FieldMapperInterface $fieldMapper,
        ValidationEngineInterface $validationEngine,
        string $importType
    ) {
        $this->fileParser = $fileParser;
        $this->fieldMapper = $fieldMapper;
        $this->validationEngine = $validationEngine;
        $this->importType = $importType;
    }

    public function processImport(ImportJob $importJob, UploadedFile $file): ImportJob
    {
        try {
            $importJob->update(['status' => 'parsing', 'started_at' => now()]);

            // Store the uploaded file
            $filePath = $this->storeImportFile($file, $importJob);
            $importJob->update([
                'source_file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size_bytes' => $file->getSize(),
                'file_mime_type' => $file->getMimeType(),
                'file_hash' => hash_file('md5', $file->getPathname()),
            ]);

            // Parse the file
            $data = $this->fileParser->parseFile($file);
            $importJob->update([
                'total_records' => count($data),
                'status' => 'mapping'
            ]);

            // Get or create mapping
            $mapping = $this->getOrCreateMapping($importJob, $file, $data);
            $importJob->update(['field_mapping' => $mapping->field_mappings]);

            // Apply field mappings
            $mappedData = $this->fieldMapper->applyMappings($data, $mapping);
            $importJob->update(['status' => 'validating']);

            // Validate data
            $validationResults = $this->validationEngine->validateData($mappedData, $mapping);
            $importJob->update([
                'validation_errors' => $validationResults['errors'],
                'data_quality_score' => $validationResults['quality_score'],
                'status' => $validationResults['has_errors'] ? 'failed' : 'importing'
            ]);

            if (!$validationResults['has_errors']) {
                // Import the data
                $importResults = $this->performImport($mappedData, $importJob);
                $importJob->update([
                    'successful_imports' => $importResults['successful'],
                    'failed_imports' => $importResults['failed'],
                    'processed_records' => $importResults['processed'],
                    'import_results' => $importResults['details'],
                    'status' => 'completed',
                    'completed_at' => now(),
                    'processing_time_seconds' => now()->diffInSeconds($importJob->started_at)
                ]);
            }

        } catch (Exception $e) {
            Log::error('Import failed: ' . $e->getMessage(), [
                'import_job_id' => $importJob->id,
                'error' => $e->getTraceAsString()
            ]);

            $importJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'error_details' => ['exception' => $e->getTraceAsString()],
                'completed_at' => now()
            ]);
        }

        return $importJob->refresh();
    }

    public function validateData(array $data, ImportMapping $mapping): array
    {
        return $this->validationEngine->validateData($data, $mapping);
    }

    public function previewData(UploadedFile $file, ImportMapping $mapping, int $limit = 10): array
    {
        $sampleData = $this->fileParser->getSampleRows($file, $limit);
        $headers = $this->fileParser->getHeaders($file);
        
        $mappedSample = $this->fieldMapper->applyMappings($sampleData, $mapping);
        $validationPreview = $this->validationEngine->validateData($mappedSample, $mapping);

        return [
            'headers' => $headers,
            'raw_data' => $sampleData,
            'mapped_data' => $mappedSample,
            'validation_preview' => $validationPreview,
            'total_rows' => $this->fileParser->getRowCount($file),
            'confidence_score' => $this->fieldMapper->getMappingConfidence(
                $mapping->field_mappings,
                $headers,
                $sampleData
            )
        ];
    }

    public function getSupportedFormats(): array
    {
        return $this->fileParser->getSupportedExtensions();
    }

    public function getImportType(): string
    {
        return $this->importType;
    }

    public function canHandleFile(UploadedFile $file): bool
    {
        return $this->fileParser->canParse($file);
    }

    public function getConfidenceScore(UploadedFile $file): int
    {
        if (!$this->canHandleFile($file)) {
            return 0;
        }

        $score = 50; // Base score for compatible file

        // Analyze file content for import type indicators
        try {
            $headers = $this->fileParser->getHeaders($file);
            $sampleData = $this->fileParser->getSampleRows($file, 5);
            
            $suggestedMappings = $this->fieldMapper->detectMappings($headers, $sampleData, $this->importType);
            $mappingConfidence = $this->fieldMapper->getMappingConfidence($suggestedMappings, $headers, $sampleData);
            
            $score = min(100, $score + ($mappingConfidence * 0.5));
        } catch (Exception $e) {
            $score = max(10, $score - 20);
        }

        return (int) $score;
    }

    public function rollbackImport(ImportJob $importJob): bool
    {
        if (!$importJob->can_rollback || !$importJob->rollback_data) {
            return false;
        }

        try {
            DB::beginTransaction();

            $rollbackData = $importJob->rollback_data;
            
            // Implement rollback logic based on import type
            $this->performRollback($rollbackData, $importJob);
            
            $importJob->update([
                'status' => 'cancelled',
                'rollback_data' => null,
                'can_rollback' => false
            ]);

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Rollback failed: ' . $e->getMessage(), [
                'import_job_id' => $importJob->id
            ]);
            return false;
        }
    }

    public function getImportSummary(ImportJob $importJob): array
    {
        return [
            'job_info' => [
                'id' => $importJob->id,
                'name' => $importJob->job_name,
                'type' => $importJob->import_type,
                'status' => $importJob->status,
                'started_at' => $importJob->started_at,
                'completed_at' => $importJob->completed_at,
                'processing_time' => $importJob->processing_time_seconds
            ],
            'file_info' => [
                'filename' => $importJob->original_filename,
                'size' => $importJob->file_size_bytes,
                'format' => $importJob->file_mime_type
            ],
            'processing_stats' => [
                'total_records' => $importJob->total_records,
                'processed_records' => $importJob->processed_records,
                'successful_imports' => $importJob->successful_imports,
                'failed_imports' => $importJob->failed_imports,
                'skipped_records' => $importJob->skipped_records,
                'success_rate' => $importJob->total_records > 0 
                    ? round(($importJob->successful_imports / $importJob->total_records) * 100, 2) 
                    : 0
            ],
            'quality_metrics' => [
                'data_quality_score' => $importJob->data_quality_score,
                'validation_errors' => count($importJob->validation_errors ?? []),
                'has_duplicates' => !empty($importJob->duplicate_detection_results)
            ],
            'business_impact' => [
                'estimated_cost_impact' => $importJob->estimated_cost_impact,
                'estimated_time_savings' => $importJob->estimated_time_savings,
                'new_items_created' => $importJob->new_items_created,
                'existing_items_updated' => $importJob->existing_items_updated
            ]
        ];
    }

    protected function storeImportFile(UploadedFile $file, ImportJob $importJob): string
    {
        $filename = Str::uuid() . '_' . $file->getClientOriginalName();
        $path = "imports/{$importJob->id}/{$filename}";
        
        Storage::disk('local')->putFileAs(
            "imports/{$importJob->id}",
            $file,
            $filename
        );
        
        return $path;
    }

    protected function getOrCreateMapping(ImportJob $importJob, UploadedFile $file, array $data): ImportMapping
    {
        // Try to find existing mapping for this POS system and import type
        $existingMapping = ImportMapping::where([
            'import_type' => $this->importType,
            'pos_system' => $importJob->pos_system,
            'is_active' => true
        ])->orderBy('usage_count', 'desc')->first();

        if ($existingMapping) {
            $existingMapping->increment('usage_count');
            $existingMapping->update(['last_used_at' => now()]);
            return $existingMapping;
        }

        // Create new mapping
        $headers = $this->fileParser->getHeaders($file);
        $sampleData = $this->fileParser->getSampleRows($file, 10);
        
        $suggestedMappings = $this->fieldMapper->detectMappings($headers, $sampleData, $this->importType);
        $confidence = $this->fieldMapper->getMappingConfidence($suggestedMappings, $headers, $sampleData);

        return ImportMapping::create([
            'mapping_name' => "Auto-generated for {$importJob->pos_system} {$this->importType}",
            'mapping_uuid' => Str::uuid(),
            'import_type' => $this->importType,
            'pos_system' => $importJob->pos_system,
            'field_mappings' => $suggestedMappings,
            'sample_data' => $sampleData,
            'detected_columns' => $headers,
            'confidence_score' => $confidence,
            'usage_count' => 1,
            'last_used_at' => now()
        ]);
    }

    protected function performImport(array $data, ImportJob $importJob): array
    {
        // This method will be implemented by specific import service classes
        // for different import types (menu, inventory, recipes, etc.)
        throw new Exception('performImport method must be implemented by concrete import service classes');
    }

    protected function performRollback(array $rollbackData, ImportJob $importJob): void
    {
        // This method will be implemented by specific import service classes
        throw new Exception('performRollback method must be implemented by concrete import service classes');
    }
}