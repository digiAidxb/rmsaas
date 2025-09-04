<?php

namespace App\Services\Import\Contracts;

use App\Models\ImportJob;
use App\Models\ImportMapping;
use Illuminate\Http\UploadedFile;

interface ImportServiceInterface
{
    /**
     * Process an import job from uploaded file
     */
    public function processImport(ImportJob $importJob, UploadedFile $file): ImportJob;

    /**
     * Validate imported data before processing
     */
    public function validateData(array $data, ImportMapping $mapping): array;

    /**
     * Preview import data with sample rows
     */
    public function previewData(UploadedFile $file, ImportMapping $mapping, int $limit = 10): array;

    /**
     * Get supported file formats for this import service
     */
    public function getSupportedFormats(): array;

    /**
     * Get import type this service handles
     */
    public function getImportType(): string;

    /**
     * Detect if this service can handle the given file
     */
    public function canHandleFile(UploadedFile $file): bool;

    /**
     * Get confidence score for handling this file (0-100)
     */
    public function getConfidenceScore(UploadedFile $file): int;

    /**
     * Rollback import changes
     */
    public function rollbackImport(ImportJob $importJob): bool;

    /**
     * Get import statistics and summary
     */
    public function getImportSummary(ImportJob $importJob): array;
}