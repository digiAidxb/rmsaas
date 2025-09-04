<?php

namespace App\Services\Import;

use App\Models\ImportJob;
use App\Services\Import\Contracts\ImportServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ImportServiceManager
{
    protected array $importServices = [];
    protected array $serviceInstances = [];

    public function __construct()
    {
        $this->registerDefaultServices();
    }

    /**
     * Register an import service for a specific type
     */
    public function registerService(string $importType, string $serviceClass): void
    {
        $this->importServices[$importType] = $serviceClass;
    }

    /**
     * Get import service for specific type
     */
    public function getService(string $importType): ImportServiceInterface
    {
        if (!isset($this->importServices[$importType])) {
            throw new InvalidArgumentException("No import service registered for type: {$importType}");
        }

        if (!isset($this->serviceInstances[$importType])) {
            $serviceClass = $this->importServices[$importType];
            $this->serviceInstances[$importType] = app($serviceClass);
        }

        return $this->serviceInstances[$importType];
    }

    /**
     * Get import service for specific type (alias for getService)
     */
    public function getServiceForType(string $importType): ?ImportServiceInterface
    {
        try {
            return $this->getService($importType);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Detect the best import service for uploaded file
     */
    public function detectBestService(UploadedFile $file): ?ImportServiceInterface
    {
        $bestService = null;
        $highestScore = 0;

        foreach ($this->importServices as $importType => $serviceClass) {
            $service = $this->getService($importType);
            
            if ($service->canHandleFile($file)) {
                $confidence = $service->getConfidenceScore($file);
                
                if ($confidence > $highestScore) {
                    $highestScore = $confidence;
                    $bestService = $service;
                }
            }
        }

        return $bestService;
    }

    /**
     * Get all services that can handle the file with their confidence scores
     */
    public function getCompatibleServices(UploadedFile $file): Collection
    {
        $compatibleServices = collect();

        foreach ($this->importServices as $importType => $serviceClass) {
            $service = $this->getService($importType);
            
            if ($service->canHandleFile($file)) {
                $compatibleServices->push([
                    'import_type' => $importType,
                    'service' => $service,
                    'confidence' => $service->getConfidenceScore($file),
                    'supported_formats' => $service->getSupportedFormats()
                ]);
            }
        }

        return $compatibleServices->sortByDesc('confidence');
    }

    /**
     * Create import job with auto-detected service
     */
    public function createImportJob(
        UploadedFile $file,
        string $jobName,
        ?string $importType = null,
        array $metadata = []
    ): ImportJob {
        
        $service = $importType 
            ? $this->getService($importType)
            : $this->detectBestService($file);

        if (!$service) {
            throw new InvalidArgumentException('No compatible import service found for this file');
        }

        $posSystem = $this->detectPosSystem($file);

        return ImportJob::create([
            'job_uuid' => \Illuminate\Support\Str::uuid(),
            'job_name' => $jobName,
            'description' => $metadata['description'] ?? "Import from {$file->getClientOriginalName()}",
            'import_type' => $service->getImportType(),
            'source_type' => 'file_upload',
            'pos_system' => $posSystem,
            'pos_metadata' => $metadata['pos_metadata'] ?? null,
            'original_filename' => $file->getClientOriginalName(),
            'file_size_bytes' => $file->getSize(),
            'file_mime_type' => $file->getMimeType(),
            'file_hash' => hash_file('md5', $file->getPathname()),
            'created_by_user_id' => auth()->id(),
            'import_context' => $metadata['context'] ?? 'onboarding',
            'is_test_import' => $metadata['is_test'] ?? false,
            'auto_approve' => $metadata['auto_approve'] ?? false,
        ]);
    }

    /**
     * Process import job using appropriate service
     */
    public function processImportJob(ImportJob $importJob, UploadedFile $file): ImportJob
    {
        $service = $this->getService($importJob->import_type);
        return $service->processImport($importJob, $file);
    }

    /**
     * Get import preview using appropriate service
     */
    public function getImportPreview(ImportJob $importJob, UploadedFile $file, int $limit = 10): array
    {
        $service = $this->getService($importJob->import_type);
        
        // Get or create a basic mapping for preview
        $mapping = $this->createBasicMapping($importJob, $file, $service);
        
        return $service->previewData($file, $mapping, $limit);
    }

    /**
     * Get all registered import services
     */
    public function getRegisteredServices(): array
    {
        return array_keys($this->importServices);
    }

    /**
     * Check if import type is supported
     */
    public function supportsImportType(string $importType): bool
    {
        return isset($this->importServices[$importType]);
    }

    /**
     * Get supported file formats across all services
     */
    public function getAllSupportedFormats(): array
    {
        $formats = [];
        
        foreach ($this->importServices as $importType => $serviceClass) {
            $service = $this->getService($importType);
            $formats[$importType] = $service->getSupportedFormats();
        }
        
        return $formats;
    }

    /**
     * Rollback import using appropriate service
     */
    public function rollbackImport(ImportJob $importJob): bool
    {
        $service = $this->getService($importJob->import_type);
        return $service->rollbackImport($importJob);
    }

    /**
     * Get import summary using appropriate service
     */
    public function getImportSummary(ImportJob $importJob): array
    {
        $service = $this->getService($importJob->import_type);
        return $service->getImportSummary($importJob);
    }

    protected function registerDefaultServices(): void
    {
        // Register default import services using service container keys
        $this->registerService('menu', 'import.menu');
        $this->registerService('inventory', 'import.inventory');
        $this->registerService('recipes', 'import.recipes');
        $this->registerService('sales', 'import.sales');
        $this->registerService('customers', 'import.customers');
        $this->registerService('employees', 'import.employees');
    }

    protected function detectPosSystem(UploadedFile $file): ?string
    {
        // Analyze file content to detect POS system
        // This would be implemented with pattern matching logic
        // For now, return 'generic'
        return 'generic';
    }

    protected function createBasicMapping(ImportJob $importJob, UploadedFile $file, ImportServiceInterface $service): \App\Models\ImportMapping
    {
        // Create a basic mapping for preview purposes
        // This would typically involve the field mapper service
        return new \App\Models\ImportMapping([
            'mapping_name' => 'Preview Mapping',
            'mapping_uuid' => \Illuminate\Support\Str::uuid(),
            'import_type' => $service->getImportType(),
            'pos_system' => $importJob->pos_system,
            'field_mappings' => [], // Basic empty mapping for now
            'is_template' => false,
            'is_active' => false
        ]);
    }
}