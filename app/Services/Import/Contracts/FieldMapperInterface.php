<?php

namespace App\Services\Import\Contracts;

use App\Models\ImportMapping;

interface FieldMapperInterface
{
    /**
     * Auto-detect field mappings based on data analysis
     */
    public function detectMappings(array $headers, array $sampleData, string $importType): array;

    /**
     * Validate field mappings
     */
    public function validateMappings(array $mappings, string $importType): array;

    /**
     * Apply field mappings to transform data
     */
    public function applyMappings(array $data, ImportMapping $mapping): array;

    /**
     * Get confidence score for suggested mappings (0-100)
     */
    public function getMappingConfidence(array $mappings, array $headers, array $sampleData): int;

    /**
     * Get available target fields for import type
     */
    public function getTargetFields(string $importType): array;

    /**
     * Suggest alternative mappings
     */
    public function suggestAlternativeMappings(array $headers, string $importType): array;

    /**
     * Transform field value based on mapping rules
     */
    public function transformValue($value, array $transformationRules): mixed;

    /**
     * Detect data patterns and types in columns
     */
    public function analyzeDataPatterns(array $columnData): array;

    /**
     * Get mapping conflicts and issues
     */
    public function detectMappingConflicts(array $mappings): array;
}