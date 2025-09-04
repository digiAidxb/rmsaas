<?php

namespace App\Services\Import\Parsers;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class JsonParser extends BaseFileParser
{
    protected array $supportedExtensions = ['json'];
    protected array $supportedMimeTypes = [
        'application/json',
        'text/json',
        'application/x-javascript'
    ];

    protected function performParsing(UploadedFile $file, array $options): array
    {
        $content = file_get_contents($file->getPathname());
        
        if (empty($content)) {
            throw new InvalidArgumentException('JSON file is empty');
        }
        
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON format: ' . json_last_error_msg());
        }
        
        // Handle different JSON structures
        $normalizedData = $this->normalizeJsonData($data, $options);
        
        $limit = $options['limit'] ?? null;
        if ($limit) {
            $normalizedData = array_slice($normalizedData, 0, $limit);
        }
        
        return $normalizedData;
    }

    protected function countRows(UploadedFile $file): int
    {
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return 0;
        }
        
        return count($this->normalizeJsonData($data));
    }

    protected function validateSpecificFormat(UploadedFile $file): array
    {
        $errors = [];
        $warnings = [];
        
        try {
            $content = file_get_contents($file->getPathname());
            
            if (empty($content)) {
                $errors[] = 'JSON file is empty';
                return ['errors' => $errors, 'warnings' => $warnings];
            }
            
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = 'Invalid JSON format: ' . json_last_error_msg();
                return ['errors' => $errors, 'warnings' => $warnings];
            }
            
            // Validate JSON structure
            $structure = $this->analyzeJsonStructure($data);
            
            if ($structure['type'] === 'unknown') {
                $warnings[] = 'JSON structure may not be suitable for tabular import';
            }
            
            if ($structure['depth'] > 5) {
                $warnings[] = 'JSON has deep nesting which may complicate import';
            }
            
            if ($structure['total_objects'] > 10000) {
                $warnings[] = "JSON contains {$structure['total_objects']} objects which may cause performance issues";
            }
            
        } catch (\Exception $e) {
            $errors[] = "Unable to validate JSON: " . $e->getMessage();
        }
        
        return ['errors' => $errors, 'warnings' => $warnings];
    }

    /**
     * Normalize JSON data to tabular format
     */
    protected function normalizeJsonData($data, array $options = []): array
    {
        $dataPath = $options['data_path'] ?? null;
        $flattenNested = $options['flatten_nested'] ?? true;
        
        // If data path is specified, extract data from that path
        if ($dataPath) {
            $data = $this->extractDataFromPath($data, $dataPath);
        }
        
        // Handle different JSON structures
        if ($this->isArrayOfObjects($data)) {
            return $this->processArrayOfObjects($data, $flattenNested);
        }
        
        if ($this->isSingleObject($data)) {
            return $this->processSingleObject($data, $flattenNested);
        }
        
        if ($this->isNestedStructure($data)) {
            return $this->processNestedStructure($data, $flattenNested);
        }
        
        // Fallback: try to convert whatever we have to array format
        return $this->convertToArrayFormat($data);
    }

    /**
     * Check if data is array of objects (most common for import)
     */
    protected function isArrayOfObjects($data): bool
    {
        return is_array($data) && 
               !empty($data) && 
               array_keys($data) === range(0, count($data) - 1) &&
               is_array($data[0]);
    }

    /**
     * Check if data is single object
     */
    protected function isSingleObject($data): bool
    {
        return is_array($data) && !$this->isArrayOfObjects($data);
    }

    /**
     * Check if data has nested structure
     */
    protected function isNestedStructure($data): bool
    {
        if (!is_array($data)) {
            return false;
        }
        
        foreach ($data as $value) {
            if (is_array($value)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Process array of objects (standard format)
     */
    protected function processArrayOfObjects(array $data, bool $flattenNested): array
    {
        $result = [];
        
        foreach ($data as $item) {
            if (is_array($item)) {
                $processedItem = $flattenNested ? $this->flattenArray($item) : $item;
                $result[] = $processedItem;
            }
        }
        
        return $result;
    }

    /**
     * Process single object (convert to array with one item)
     */
    protected function processSingleObject(array $data, bool $flattenNested): array
    {
        $processedItem = $flattenNested ? $this->flattenArray($data) : $data;
        return [$processedItem];
    }

    /**
     * Process nested structure
     */
    protected function processNestedStructure(array $data, bool $flattenNested): array
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->isArrayOfObjects($value)) {
                    foreach ($value as $item) {
                        $processedItem = $flattenNested ? $this->flattenArray($item) : $item;
                        $processedItem['_parent_key'] = $key;
                        $result[] = $processedItem;
                    }
                } else {
                    $processedItem = $flattenNested ? $this->flattenArray($value) : $value;
                    $processedItem['_key'] = $key;
                    $result[] = $processedItem;
                }
            } else {
                $result[] = ['_key' => $key, '_value' => $value];
            }
        }
        
        return $result;
    }

    /**
     * Convert any data to array format
     */
    protected function convertToArrayFormat($data): array
    {
        if (is_array($data)) {
            return [$data];
        }
        
        return [['value' => $data]];
    }

    /**
     * Flatten nested arrays using dot notation
     */
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value) && !empty($value)) {
                // Check if it's an indexed array
                if (array_keys($value) === range(0, count($value) - 1)) {
                    // Convert indexed array to comma-separated string
                    $result[$newKey] = implode(', ', $value);
                } else {
                    // Recursively flatten associative arrays
                    $result = array_merge($result, $this->flattenArray($value, $newKey));
                }
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Extract data from specific path in JSON
     */
    protected function extractDataFromPath(array $data, string $path): array
    {
        $keys = explode('.', $path);
        $current = $data;
        
        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                throw new InvalidArgumentException("Path '{$path}' not found in JSON data");
            }
            $current = $current[$key];
        }
        
        return $current;
    }

    /**
     * Analyze JSON structure for validation
     */
    protected function analyzeJsonStructure($data): array
    {
        $analysis = [
            'type' => 'unknown',
            'depth' => 0,
            'total_objects' => 0,
            'total_arrays' => 0,
            'keys' => [],
            'sample_structure' => null
        ];
        
        if ($this->isArrayOfObjects($data)) {
            $analysis['type'] = 'array_of_objects';
            $analysis['total_objects'] = count($data);
            
            if (!empty($data)) {
                $analysis['keys'] = array_keys($data[0]);
                $analysis['sample_structure'] = $data[0];
            }
        } elseif ($this->isSingleObject($data)) {
            $analysis['type'] = 'single_object';
            $analysis['total_objects'] = 1;
            $analysis['keys'] = array_keys($data);
            $analysis['sample_structure'] = $data;
        } elseif (is_array($data)) {
            $analysis['type'] = 'nested_structure';
            $analysis['total_arrays'] = 1;
        }
        
        $analysis['depth'] = $this->calculateMaxDepth($data);
        
        return $analysis;
    }

    /**
     * Calculate maximum depth of nested structure
     */
    protected function calculateMaxDepth($data): int
    {
        if (!is_array($data)) {
            return 0;
        }
        
        $maxDepth = 1;
        
        foreach ($data as $value) {
            if (is_array($value)) {
                $depth = 1 + $this->calculateMaxDepth($value);
                $maxDepth = max($maxDepth, $depth);
            }
        }
        
        return $maxDepth;
    }

    /**
     * Get possible data paths in JSON
     */
    public function getDataPaths(UploadedFile $file): array
    {
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON format');
        }
        
        return $this->extractPaths($data);
    }

    /**
     * Extract all possible paths to arrays in JSON
     */
    protected function extractPaths($data, string $currentPath = ''): array
    {
        $paths = [];
        
        if (is_array($data)) {
            // If this is an array of objects, it's a valid data path
            if ($this->isArrayOfObjects($data)) {
                $paths[] = [
                    'path' => $currentPath ?: 'root',
                    'type' => 'array_of_objects',
                    'count' => count($data),
                    'sample_keys' => !empty($data) ? array_keys($data[0]) : []
                ];
            }
            
            // Recursively check nested structures
            foreach ($data as $key => $value) {
                $newPath = $currentPath ? $currentPath . '.' . $key : $key;
                $nestedPaths = $this->extractPaths($value, $newPath);
                $paths = array_merge($paths, $nestedPaths);
            }
        }
        
        return $paths;
    }

    /**
     * Parse JSON with specific configuration
     */
    public function parseWithConfig(UploadedFile $file, array $config): array
    {
        return $this->performParsing($file, $config);
    }

    /**
     * Get JSON file statistics
     */
    public function getJsonStats(UploadedFile $file): array
    {
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON format');
        }
        
        $structure = $this->analyzeJsonStructure($data);
        $paths = $this->getDataPaths($file);
        
        return [
            'file_size' => strlen($content),
            'structure' => $structure,
            'available_paths' => $paths,
            'encoding' => mb_detect_encoding($content) ?: 'UTF-8',
            'line_count' => substr_count($content, "\n") + 1
        ];
    }

    /**
     * Validate JSON against schema (if provided)
     */
    public function validateJsonSchema(UploadedFile $file, array $schema): array
    {
        // This would integrate with a JSON Schema validator
        // For now, return basic validation
        
        $errors = [];
        $warnings = [];
        
        try {
            $content = file_get_contents($file->getPathname());
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = 'Invalid JSON format: ' . json_last_error_msg();
                return ['errors' => $errors, 'warnings' => $warnings, 'is_valid' => false];
            }
            
            // Basic schema validation (would be expanded with proper JSON Schema library)
            if (isset($schema['required_fields'])) {
                foreach ($schema['required_fields'] as $field) {
                    if (!$this->hasField($data, $field)) {
                        $errors[] = "Required field '{$field}' not found in JSON data";
                    }
                }
            }
            
        } catch (\Exception $e) {
            $errors[] = "Schema validation failed: " . $e->getMessage();
        }
        
        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'is_valid' => empty($errors)
        ];
    }

    /**
     * Check if JSON data has specific field
     */
    protected function hasField($data, string $field): bool
    {
        if ($this->isArrayOfObjects($data)) {
            return !empty($data) && array_key_exists($field, $data[0]);
        }
        
        if ($this->isSingleObject($data)) {
            return array_key_exists($field, $data);
        }
        
        return false;
    }
}