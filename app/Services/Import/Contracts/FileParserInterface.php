<?php

namespace App\Services\Import\Contracts;

use Illuminate\Http\UploadedFile;

interface FileParserInterface
{
    /**
     * Parse uploaded file and return structured data
     */
    public function parseFile(UploadedFile $file, array $options = []): array;

    /**
     * Detect file format and characteristics
     */
    public function detectFormat(UploadedFile $file): array;

    /**
     * Get sample rows from file for preview
     */
    public function getSampleRows(UploadedFile $file, int $limit = 10, int $offset = 0): array;

    /**
     * Get file headers/column names
     */
    public function getHeaders(UploadedFile $file): array;

    /**
     * Get total row count in file
     */
    public function getRowCount(UploadedFile $file): int;

    /**
     * Validate file format and structure
     */
    public function validateFileFormat(UploadedFile $file): array;

    /**
     * Get supported file extensions
     */
    public function getSupportedExtensions(): array;

    /**
     * Check if parser can handle this file
     */
    public function canParse(UploadedFile $file): bool;

    /**
     * Parse file in chunks for large files
     */
    public function parseInChunks(UploadedFile $file, int $chunkSize = 1000, callable $callback = null): \Generator;
}