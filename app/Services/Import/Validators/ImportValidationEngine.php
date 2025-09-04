<?php

namespace App\Services\Import\Validators;

use App\Models\ImportMapping;
use App\Services\Import\Contracts\ValidationEngineInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

class ImportValidationEngine implements ValidationEngineInterface
{
    protected array $validationRules = [];
    protected array $customValidators = [];
    protected array $businessRules = [];

    public function __construct()
    {
        $this->initializeValidationRules();
        $this->initializeCustomValidators();
        $this->initializeBusinessRules();
    }

    public function validateData(array $data, ImportMapping $mapping): array
    {
        $validationResults = [
            'is_valid' => true,
            'has_errors' => false,
            'errors' => [],
            'warnings' => [],
            'row_errors' => [],
            'summary' => [
                'total_rows' => count($data),
                'valid_rows' => 0,
                'rows_with_errors' => 0,
                'rows_with_warnings' => 0,
                'critical_errors' => 0,
                'quality_score' => 0
            ],
            'field_errors' => [],
            'duplicate_analysis' => [],
            'business_rule_violations' => [],
            'data_quality_score' => []
        ];

        if (empty($data)) {
            $validationResults['errors'][] = 'No data to validate';
            $validationResults['has_errors'] = true;
            $validationResults['is_valid'] = false;
            return $validationResults;
        }

        // Validate each row
        foreach ($data as $rowIndex => $row) {
            $rowValidation = $this->validateRow($row, $rowIndex, $mapping);
            
            if (!empty($rowValidation['errors'])) {
                $validationResults['row_errors'][$rowIndex] = $rowValidation['errors'];
                $validationResults['summary']['rows_with_errors']++;
                
                // Count critical errors
                foreach ($rowValidation['errors'] as $error) {
                    if ($error['severity'] === 'critical') {
                        $validationResults['summary']['critical_errors']++;
                    }
                }
            }
            
            if (!empty($rowValidation['warnings'])) {
                $validationResults['warnings'][$rowIndex] = $rowValidation['warnings'];
                $validationResults['summary']['rows_with_warnings']++;
            }
            
            if (empty($rowValidation['errors'])) {
                $validationResults['summary']['valid_rows']++;
            }

            // Collect field-specific errors
            foreach ($rowValidation['field_errors'] ?? [] as $field => $fieldErrors) {
                if (!isset($validationResults['field_errors'][$field])) {
                    $validationResults['field_errors'][$field] = [];
                }
                $validationResults['field_errors'][$field] = array_merge(
                    $validationResults['field_errors'][$field],
                    $fieldErrors
                );
            }
        }

        // Check for duplicates
        $validationResults['duplicate_analysis'] = $this->detectDuplicates($data, $mapping->import_type);

        // Validate business logic
        $validationResults['business_rule_violations'] = $this->validateBusinessLogic($data, $mapping->import_type);

        // Validate data consistency
        $consistencyResults = $this->validateDataConsistency($data);
        $validationResults['consistency_analysis'] = $consistencyResults;

        // Calculate overall validation status
        $validationResults['has_errors'] = !empty($validationResults['row_errors']) || 
                                         !empty($validationResults['business_rule_violations']);
        $validationResults['is_valid'] = !$validationResults['has_errors'];

        // Calculate data quality score
        $validationResults['data_quality_score'] = $this->calculateDataQualityScore($validationResults, $data);
        $validationResults['summary']['quality_score'] = $validationResults['data_quality_score']['overall_score'];

        return $validationResults;
    }

    public function validateRow(array $row, int $rowNumber, ImportMapping $mapping): array
    {
        $rowValidation = [
            'is_valid' => true,
            'errors' => [],
            'warnings' => [],
            'field_errors' => []
        ];

        $importType = $mapping->import_type;
        $fieldMappings = $mapping->field_mappings;

        // Get validation rules for this import type
        $rules = $this->getValidationRules($importType);

        // Validate each mapped field
        foreach ($fieldMappings as $sourceField => $mappingConfig) {
            $targetField = $mappingConfig['target_field'];
            $value = $row[$targetField] ?? null;

            // Apply field-specific validation
            $fieldValidation = $this->validateField($targetField, $value, $rules, $rowNumber);
            
            if (!empty($fieldValidation['errors'])) {
                $rowValidation['field_errors'][$targetField] = $fieldValidation['errors'];
                foreach ($fieldValidation['errors'] as $error) {
                    $rowValidation['errors'][] = [
                        'field' => $targetField,
                        'value' => $value,
                        'message' => $error['message'],
                        'severity' => $error['severity'],
                        'suggestion' => $error['suggestion'] ?? null
                    ];
                }
            }

            if (!empty($fieldValidation['warnings'])) {
                foreach ($fieldValidation['warnings'] as $warning) {
                    $rowValidation['warnings'][] = [
                        'field' => $targetField,
                        'value' => $value,
                        'message' => $warning['message'],
                        'suggestion' => $warning['suggestion'] ?? null
                    ];
                }
            }
        }

        // Validate required fields
        $requiredFieldsValidation = $this->validateRequiredFields($row, $mapping);
        $rowValidation['errors'] = array_merge($rowValidation['errors'], $requiredFieldsValidation['errors']);
        $rowValidation['warnings'] = array_merge($rowValidation['warnings'], $requiredFieldsValidation['warnings']);

        // Validate data formats and types
        $formatValidation = $this->validateDataFormats($row, $mapping);
        $rowValidation['errors'] = array_merge($rowValidation['errors'], $formatValidation['errors']);
        $rowValidation['warnings'] = array_merge($rowValidation['warnings'], $formatValidation['warnings']);

        // Custom business logic validation for the row
        $businessValidation = $this->validateRowBusinessLogic($row, $importType, $rowNumber);
        $rowValidation['errors'] = array_merge($rowValidation['errors'], $businessValidation['errors']);
        $rowValidation['warnings'] = array_merge($rowValidation['warnings'], $businessValidation['warnings']);

        $rowValidation['is_valid'] = empty($rowValidation['errors']);

        return $rowValidation;
    }

    public function getValidationRules(string $importType): array
    {
        return $this->validationRules[$importType] ?? [];
    }

    public function detectDuplicates(array $data, string $importType): array
    {
        $duplicates = [
            'total_duplicates' => 0,
            'duplicate_groups' => [],
            'duplicate_fields' => [],
            'analysis' => []
        ];

        if (empty($data)) {
            return $duplicates;
        }

        // Get key fields for duplicate detection based on import type
        $keyFields = $this->getDuplicateDetectionFields($importType);

        foreach ($keyFields as $fieldSet) {
            $groups = $this->findDuplicateGroups($data, $fieldSet);
            
            if (!empty($groups)) {
                $duplicates['duplicate_groups'][$this->getFieldSetKey($fieldSet)] = $groups;
                $duplicates['total_duplicates'] += count($groups);
                
                foreach ($fieldSet as $field) {
                    if (!in_array($field, $duplicates['duplicate_fields'])) {
                        $duplicates['duplicate_fields'][] = $field;
                    }
                }
            }
        }

        // Analyze duplicate patterns
        $duplicates['analysis'] = $this->analyzeDuplicatePatterns($duplicates['duplicate_groups'], $data);

        return $duplicates;
    }

    public function validateDataConsistency(array $data): array
    {
        $consistency = [
            'is_consistent' => true,
            'inconsistencies' => [],
            'field_consistency' => [],
            'pattern_analysis' => []
        ];

        if (empty($data)) {
            return $consistency;
        }

        // Analyze each field for consistency
        $firstRow = $data[0];
        foreach (array_keys($firstRow) as $field) {
            $fieldValues = array_column($data, $field);
            $fieldConsistency = $this->analyzeFieldConsistency($field, $fieldValues);
            
            $consistency['field_consistency'][$field] = $fieldConsistency;
            
            if (!$fieldConsistency['is_consistent']) {
                $consistency['inconsistencies'][] = [
                    'field' => $field,
                    'type' => $fieldConsistency['inconsistency_type'],
                    'details' => $fieldConsistency['details'],
                    'affected_rows' => $fieldConsistency['affected_rows']
                ];
                $consistency['is_consistent'] = false;
            }
        }

        // Cross-field consistency checks
        $crossFieldConsistency = $this->validateCrossFieldConsistency($data);
        $consistency['cross_field_issues'] = $crossFieldConsistency;

        return $consistency;
    }

    public function validateBusinessLogic(array $data, string $importType): array
    {
        $violations = [];

        $businessRules = $this->businessRules[$importType] ?? [];

        foreach ($businessRules as $ruleName => $rule) {
            $ruleViolations = $this->applyBusinessRule($data, $rule, $ruleName);
            if (!empty($ruleViolations)) {
                $violations[$ruleName] = $ruleViolations;
            }
        }

        return $violations;
    }

    public function validateRequiredFields(array $data, ImportMapping $mapping): array
    {
        $validation = ['errors' => [], 'warnings' => []];
        
        $requiredFields = $this->getRequiredFieldsForImport($mapping->import_type);
        $fieldMappings = $mapping->field_mappings;
        $mappedTargetFields = array_column($fieldMappings, 'target_field');

        foreach ($requiredFields as $requiredField) {
            if (!in_array($requiredField, $mappedTargetFields)) {
                $validation['errors'][] = [
                    'field' => $requiredField,
                    'message' => "Required field '{$requiredField}' is not mapped",
                    'severity' => 'critical',
                    'suggestion' => "Map a source field to '{$requiredField}' or provide a default value"
                ];
                continue;
            }

            // Check if the field has a value in the data
            $value = $data[$requiredField] ?? null;
            if ($this->isEmpty($value)) {
                $validation['errors'][] = [
                    'field' => $requiredField,
                    'message' => "Required field '{$requiredField}' is empty",
                    'severity' => 'critical',
                    'suggestion' => "Provide a value for '{$requiredField}' or set a default value"
                ];
            }
        }

        return $validation;
    }

    public function validateDataFormats(array $data, ImportMapping $mapping): array
    {
        $validation = ['errors' => [], 'warnings' => []];
        
        $fieldMappings = $mapping->field_mappings;

        foreach ($fieldMappings as $sourceField => $mappingConfig) {
            $targetField = $mappingConfig['target_field'];
            $expectedDataType = $mappingConfig['data_type'] ?? 'string';
            $value = $data[$targetField] ?? null;

            if ($value === null || $value === '') {
                continue; // Skip empty values
            }

            $formatValidation = $this->validateFieldFormat($targetField, $value, $expectedDataType);
            
            $validation['errors'] = array_merge($validation['errors'], $formatValidation['errors']);
            $validation['warnings'] = array_merge($validation['warnings'], $formatValidation['warnings']);
        }

        return $validation;
    }

    public function validatePriceCalculations(array $data): array
    {
        $validation = ['errors' => [], 'warnings' => []];

        foreach ($data as $rowIndex => $row) {
            // Validate price consistency
            if (isset($row['unit_price']) && isset($row['quantity']) && isset($row['total_amount'])) {
                $calculatedTotal = $row['unit_price'] * $row['quantity'];
                $actualTotal = $row['total_amount'];
                
                $difference = abs($calculatedTotal - $actualTotal);
                $tolerance = max($actualTotal * 0.01, 0.01); // 1% tolerance or 1 cent
                
                if ($difference > $tolerance) {
                    $validation['errors'][] = [
                        'row' => $rowIndex + 1,
                        'message' => "Price calculation mismatch: {$row['unit_price']} × {$row['quantity']} = {$calculatedTotal}, but total_amount is {$actualTotal}",
                        'severity' => 'medium',
                        'fields' => ['unit_price', 'quantity', 'total_amount'],
                        'suggestion' => 'Verify the calculation or check for discounts/taxes not accounted for'
                    ];
                }
            }

            // Validate price reasonableness
            if (isset($row['price'])) {
                $price = (float) $row['price'];
                if ($price < 0) {
                    $validation['errors'][] = [
                        'row' => $rowIndex + 1,
                        'field' => 'price',
                        'message' => 'Price cannot be negative',
                        'severity' => 'high',
                        'suggestion' => 'Check the price value and correct if necessary'
                    ];
                } elseif ($price > 1000) {
                    $validation['warnings'][] = [
                        'row' => $rowIndex + 1,
                        'field' => 'price',
                        'message' => 'Price seems unusually high: $' . $price,
                        'suggestion' => 'Verify this is the correct price'
                    ];
                } elseif ($price > 0 && $price < 0.50) {
                    $validation['warnings'][] = [
                        'row' => $rowIndex + 1,
                        'field' => 'price',
                        'message' => 'Price seems unusually low: $' . $price,
                        'suggestion' => 'Verify this is the correct price'
                    ];
                }
            }
        }

        return $validation;
    }

    public function generateValidationReport(array $validationResults): array
    {
        $report = [
            'executive_summary' => [
                'overall_status' => $validationResults['is_valid'] ? 'PASSED' : 'FAILED',
                'data_quality_score' => $validationResults['data_quality_score']['overall_score'] ?? 0,
                'total_rows' => $validationResults['summary']['total_rows'] ?? 0,
                'valid_rows' => $validationResults['summary']['valid_rows'] ?? 0,
                'rows_with_errors' => $validationResults['summary']['rows_with_errors'] ?? 0,
                'critical_issues' => $validationResults['summary']['critical_errors'] ?? 0
            ],
            'error_breakdown' => [
                'field_errors' => $this->summarizeFieldErrors($validationResults['field_errors'] ?? []),
                'business_rule_violations' => count($validationResults['business_rule_violations'] ?? []),
                'duplicate_records' => $validationResults['duplicate_analysis']['total_duplicates'] ?? 0,
                'consistency_issues' => count($validationResults['consistency_analysis']['inconsistencies'] ?? [])
            ],
            'recommendations' => $this->generateRecommendations($validationResults),
            'data_quality_metrics' => $validationResults['data_quality_score'] ?? [],
            'next_steps' => $this->generateNextSteps($validationResults)
        ];

        return $report;
    }

    public function suggestCorrections(array $validationErrors): array
    {
        $corrections = [
            'automatic_fixes' => [],
            'manual_reviews' => [],
            'data_transformations' => [],
            'mapping_adjustments' => []
        ];

        foreach ($validationErrors as $category => $errors) {
            switch ($category) {
                case 'field_errors':
                    $corrections = array_merge_recursive($corrections, $this->suggestFieldCorrections($errors));
                    break;
                case 'duplicate_analysis':
                    $corrections = array_merge_recursive($corrections, $this->suggestDuplicateCorrections($errors));
                    break;
                case 'business_rule_violations':
                    $corrections = array_merge_recursive($corrections, $this->suggestBusinessRuleCorrections($errors));
                    break;
                case 'consistency_analysis':
                    $corrections = array_merge_recursive($corrections, $this->suggestConsistencyCorrections($errors));
                    break;
            }
        }

        return $corrections;
    }

    // Protected helper methods

    protected function initializeValidationRules(): void
    {
        $this->validationRules = [
            'menu' => [
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0', 'max:999.99'],
                'category' => ['string', 'max:100'],
                'description' => ['string', 'max:1000'],
                'calories' => ['integer', 'min:0', 'max:5000'],
                'preparation_time' => ['integer', 'min:0', 'max:480'],
                'spice_level' => ['in:none,mild,medium,hot,extra_hot']
            ],
            'inventory' => [
                'name' => ['required', 'string', 'max:255'],
                'unit' => ['required', 'string', 'max:50'],
                'current_stock' => ['numeric', 'min:0'],
                'minimum_stock' => ['numeric', 'min:0'],
                'cost_per_unit' => ['numeric', 'min:0'],
                'expiry_date' => ['date', 'after:today']
            ],
            'sales' => [
                'transaction_id' => ['required', 'string', 'max:100'],
                'date' => ['required', 'date'],
                'item_name' => ['required', 'string', 'max:255'],
                'quantity' => ['required', 'integer', 'min:1'],
                'unit_price' => ['required', 'numeric', 'min:0'],
                'total_amount' => ['required', 'numeric', 'min:0']
            ],
            'customers' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['email', 'max:255'],
                'phone' => ['regex:/^[\+]?[1-9][\d]{0,15}$/'],
                'date_of_birth' => ['date', 'before:today']
            ]
        ];
    }

    protected function initializeCustomValidators(): void
    {
        $this->customValidators = [
            'price_format' => function ($value) {
                return is_numeric($value) && $value >= 0;
            },
            'phone_format' => function ($value) {
                return preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[^0-9+]/', '', $value));
            },
            'allergen_list' => function ($value) {
                $validAllergens = ['gluten', 'dairy', 'nuts', 'shellfish', 'fish', 'eggs', 'soy', 'sesame'];
                if (is_array($value)) {
                    return empty(array_diff($value, $validAllergens));
                }
                return true;
            }
        ];
    }

    protected function initializeBusinessRules(): void
    {
        $this->businessRules = [
            'menu' => [
                'price_cost_ratio' => [
                    'description' => 'Price should be at least 2x the cost for reasonable margin',
                    'rule' => function ($row) {
                        if (isset($row['price']) && isset($row['cost'])) {
                            return $row['price'] >= ($row['cost'] * 2);
                        }
                        return true;
                    },
                    'severity' => 'warning'
                ]
            ],
            'inventory' => [
                'stock_levels' => [
                    'description' => 'Current stock should not be negative',
                    'rule' => function ($row) {
                        return !isset($row['current_stock']) || $row['current_stock'] >= 0;
                    },
                    'severity' => 'error'
                ]
            ]
        ];
    }

    // Additional helper methods would be implemented here...
    protected function validateField(string $field, $value, array $rules, int $rowNumber): array
    {
        // Placeholder implementation
        return ['errors' => [], 'warnings' => []];
    }

    protected function isEmpty($value): bool
    {
        return $value === null || $value === '' || (is_array($value) && empty($value));
    }

    protected function getDuplicateDetectionFields(string $importType): array
    {
        $fields = [
            'menu' => [['name'], ['name', 'category'], ['pos_id']],
            'inventory' => [['name'], ['sku'], ['name', 'supplier']],
            'sales' => [['transaction_id'], ['transaction_id', 'item_name']],
            'customers' => [['email'], ['phone'], ['name', 'email']]
        ];

        return $fields[$importType] ?? [];
    }

    protected function findDuplicateGroups(array $data, array $fields): array
    {
        // Placeholder implementation
        return [];
    }

    protected function getFieldSetKey(array $fields): string
    {
        return implode('_', $fields);
    }

    protected function analyzeDuplicatePatterns(array $duplicateGroups, array $data): array
    {
        // Placeholder implementation
        return [];
    }

    protected function analyzeFieldConsistency(string $field, array $values): array
    {
        // Placeholder implementation
        return ['is_consistent' => true];
    }

    protected function validateCrossFieldConsistency(array $data): array
    {
        // Placeholder implementation
        return [];
    }

    protected function applyBusinessRule(array $data, array $rule, string $ruleName): array
    {
        // Placeholder implementation
        return [];
    }

    protected function getRequiredFieldsForImport(string $importType): array
    {
        $requiredFields = [
            'menu' => ['name', 'price'],
            'inventory' => ['name', 'unit'],
            'sales' => ['transaction_id', 'date', 'item_name', 'quantity', 'unit_price', 'total_amount'],
            'customers' => ['name']
        ];

        return $requiredFields[$importType] ?? [];
    }

    protected function validateFieldFormat(string $field, $value, string $expectedType): array
    {
        // Placeholder implementation
        return ['errors' => [], 'warnings' => []];
    }

    protected function calculateDataQualityScore(array $validationResults, array $data): array
    {
        // Placeholder implementation
        return ['overall_score' => 85];
    }

    protected function validateRowBusinessLogic(array $row, string $importType, int $rowNumber): array
    {
        // Placeholder implementation
        return ['errors' => [], 'warnings' => []];
    }

    protected function summarizeFieldErrors(array $fieldErrors): array
    {
        // Placeholder implementation
        return [];
    }

    protected function generateRecommendations(array $validationResults): array
    {
        // Placeholder implementation
        return [];
    }

    protected function generateNextSteps(array $validationResults): array
    {
        // Placeholder implementation
        return [];
    }

    protected function suggestFieldCorrections(array $errors): array
    {
        // Placeholder implementation
        return ['automatic_fixes' => [], 'manual_reviews' => []];
    }

    protected function suggestDuplicateCorrections(array $errors): array
    {
        // Placeholder implementation
        return ['automatic_fixes' => [], 'manual_reviews' => []];
    }

    protected function suggestBusinessRuleCorrections(array $errors): array
    {
        // Placeholder implementation
        return ['automatic_fixes' => [], 'manual_reviews' => []];
    }

    protected function suggestConsistencyCorrections(array $errors): array
    {
        // Placeholder implementation
        return ['automatic_fixes' => [], 'manual_reviews' => []];
    }
}