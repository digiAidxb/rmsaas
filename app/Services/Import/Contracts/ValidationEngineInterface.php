<?php

namespace App\Services\Import\Contracts;

use App\Models\ImportMapping;

interface ValidationEngineInterface
{
    /**
     * Validate imported data against business rules
     */
    public function validateData(array $data, ImportMapping $mapping): array;

    /**
     * Validate individual row of data
     */
    public function validateRow(array $row, int $rowNumber, ImportMapping $mapping): array;

    /**
     * Get validation rules for import type
     */
    public function getValidationRules(string $importType): array;

    /**
     * Check for duplicate records
     */
    public function detectDuplicates(array $data, string $importType): array;

    /**
     * Validate data consistency across rows
     */
    public function validateDataConsistency(array $data): array;

    /**
     * Check business logic constraints
     */
    public function validateBusinessLogic(array $data, string $importType): array;

    /**
     * Validate required fields presence
     */
    public function validateRequiredFields(array $data, ImportMapping $mapping): array;

    /**
     * Validate data formats and types
     */
    public function validateDataFormats(array $data, ImportMapping $mapping): array;

    /**
     * Validate price and cost calculations
     */
    public function validatePriceCalculations(array $data): array;

    /**
     * Generate validation summary report
     */
    public function generateValidationReport(array $validationResults): array;

    /**
     * Suggest corrections for validation errors
     */
    public function suggestCorrections(array $validationErrors): array;
}