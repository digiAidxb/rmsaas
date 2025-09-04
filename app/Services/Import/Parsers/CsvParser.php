<?php

namespace App\Services\Import\Parsers;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class CsvParser extends BaseFileParser
{
    protected array $supportedExtensions = ['csv', 'txt'];
    protected array $supportedMimeTypes = [
        'text/csv',
        'text/plain',
        'application/csv',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel'
    ];

    protected function performParsing(UploadedFile $file, array $options): array
    {
        $delimiter = $options['delimiter'] ?? $this->detectDelimiter($file);
        $quoteCharacter = $options['quote_character'] ?? $this->detectQuoteCharacter($file);
        $encoding = $options['encoding'] ?? $this->detectEncoding($file);
        $hasHeaders = $options['has_headers'] ?? $this->detectHeaders($file);
        $limit = $options['limit'] ?? null;
        
        $handle = $this->getFileHandle($file);
        $data = [];
        $headers = [];
        $rowCount = 0;
        
        try {
            // Read headers if present
            if ($hasHeaders) {
                $headerRow = fgetcsv($handle, 0, $delimiter, $quoteCharacter);
                if ($headerRow === false) {
                    throw new InvalidArgumentException('Unable to read header row');
                }
                $headers = array_map([$this, 'cleanCellValue'], $headerRow);
            }
            
            // Read data rows
            while (($row = fgetcsv($handle, 0, $delimiter, $quoteCharacter)) !== false) {
                if ($limit && $rowCount >= $limit) {
                    break;
                }
                
                // Clean and process row data
                $cleanRow = array_map([$this, 'cleanCellValue'], $row);
                
                // Create associative array if we have headers
                if (!empty($headers)) {
                    $cleanRow = $this->combineHeadersWithRow($headers, $cleanRow);
                }
                
                $data[] = $cleanRow;
                $rowCount++;
            }
            
        } finally {
            fclose($handle);
        }
        
        return $data;
    }

    protected function countRows(UploadedFile $file): int
    {
        $handle = $this->getFileHandle($file);
        $count = 0;
        
        try {
            while (fgets($handle) !== false) {
                $count++;
            }
        } finally {
            fclose($handle);
        }
        
        // Subtract header row if present
        if ($this->detectHeaders($file)) {
            $count = max(0, $count - 1);
        }
        
        return $count;
    }

    protected function validateSpecificFormat(UploadedFile $file): array
    {
        $errors = [];
        $warnings = [];
        
        try {
            $handle = $this->getFileHandle($file);
            
            // Read first few lines to validate CSV structure
            $lineCount = 0;
            $columnCounts = [];
            $delimiter = $this->detectDelimiter($file);
            $quoteCharacter = $this->detectQuoteCharacter($file);
            
            while (($row = fgetcsv($handle, 0, $delimiter, $quoteCharacter)) !== false && $lineCount < 10) {
                $columnCounts[] = count($row);
                $lineCount++;
            }
            
            fclose($handle);
            
            // Check for inconsistent column counts
            if (count(array_unique($columnCounts)) > 1) {
                $warnings[] = 'Inconsistent number of columns detected across rows';
            }
            
            // Check if we have at least one row
            if ($lineCount === 0) {
                $errors[] = 'CSV file appears to be empty or unreadable';
            }
            
            // Check for very wide files (potential parsing issues)
            $maxColumns = max($columnCounts);
            if ($maxColumns > 100) {
                $warnings[] = "File has {$maxColumns} columns which may cause performance issues";
            }
            
        } catch (\Exception $e) {
            $errors[] = "Unable to validate CSV structure: " . $e->getMessage();
        }
        
        return ['errors' => $errors, 'warnings' => $warnings];
    }

    /**
     * Parse CSV with custom dialect detection
     */
    public function parseWithDialectDetection(UploadedFile $file): array
    {
        $dialects = [
            ['delimiter' => ',', 'quote' => '"'],
            ['delimiter' => ';', 'quote' => '"'],
            ['delimiter' => "\t", 'quote' => '"'],
            ['delimiter' => '|', 'quote' => '"'],
            ['delimiter' => ',', 'quote' => "'"],
        ];
        
        $bestDialect = null;
        $bestScore = 0;
        
        foreach ($dialects as $dialect) {
            try {
                $score = $this->testDialect($file, $dialect);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestDialect = $dialect;
                }
            } catch (\Exception $e) {
                // Continue with next dialect
            }
        }
        
        if (!$bestDialect) {
            throw new InvalidArgumentException('Unable to detect CSV dialect');
        }
        
        return $this->performParsing($file, [
            'delimiter' => $bestDialect['delimiter'],
            'quote_character' => $bestDialect['quote']
        ]);
    }

    /**
     * Parse CSV with streaming for large files
     */
    public function parseStream(UploadedFile $file, callable $callback, int $batchSize = 1000): void
    {
        $delimiter = $this->detectDelimiter($file);
        $quoteCharacter = $this->detectQuoteCharacter($file);
        $hasHeaders = $this->detectHeaders($file);
        
        $handle = $this->getFileHandle($file);
        $batch = [];
        $headers = [];
        $rowCount = 0;
        
        try {
            // Read headers
            if ($hasHeaders) {
                $headerRow = fgetcsv($handle, 0, $delimiter, $quoteCharacter);
                $headers = array_map([$this, 'cleanCellValue'], $headerRow);
            }
            
            while (($row = fgetcsv($handle, 0, $delimiter, $quoteCharacter)) !== false) {
                $cleanRow = array_map([$this, 'cleanCellValue'], $row);
                
                if (!empty($headers)) {
                    $cleanRow = $this->combineHeadersWithRow($headers, $cleanRow);
                }
                
                $batch[] = $cleanRow;
                $rowCount++;
                
                if (count($batch) >= $batchSize) {
                    $callback($batch, $rowCount - count($batch), $rowCount);
                    $batch = [];
                }
            }
            
            // Process remaining rows
            if (!empty($batch)) {
                $callback($batch, $rowCount - count($batch), $rowCount);
            }
            
        } finally {
            fclose($handle);
        }
    }

    /**
     * Validate CSV structure with detailed analysis
     */
    public function analyzeStructure(UploadedFile $file): array
    {
        $delimiter = $this->detectDelimiter($file);
        $quoteCharacter = $this->detectQuoteCharacter($file);
        $encoding = $this->detectEncoding($file);
        $hasHeaders = $this->detectHeaders($file);
        
        $handle = $this->getFileHandle($file);
        $analysis = [
            'format' => [
                'delimiter' => $delimiter,
                'quote_character' => $quoteCharacter,
                'encoding' => $encoding,
                'has_headers' => $hasHeaders
            ],
            'structure' => [
                'total_rows' => 0,
                'total_columns' => 0,
                'consistent_columns' => true,
                'column_counts' => [],
                'empty_rows' => 0
            ],
            'data_quality' => [
                'null_values' => 0,
                'empty_values' => 0,
                'inconsistent_types' => []
            ],
            'columns' => []
        ];
        
        try {
            $headers = [];
            $rowCount = 0;
            $columnCounts = [];
            
            // Read headers
            if ($hasHeaders) {
                $headerRow = fgetcsv($handle, 0, $delimiter, $quoteCharacter);
                $headers = array_map([$this, 'cleanCellValue'], $headerRow);
                $analysis['structure']['total_columns'] = count($headers);
            }
            
            // Analyze data rows (sample first 1000 rows for performance)
            $sampleSize = 1000;
            $columnStats = [];
            
            while (($row = fgetcsv($handle, 0, $delimiter, $quoteCharacter)) !== false && $rowCount < $sampleSize) {
                $cleanRow = array_map([$this, 'cleanCellValue'], $row);
                $columnCounts[] = count($cleanRow);
                
                if (empty(array_filter($cleanRow))) {
                    $analysis['data_quality']['empty_rows']++;
                }
                
                // Analyze each column
                foreach ($cleanRow as $colIndex => $value) {
                    if (!isset($columnStats[$colIndex])) {
                        $columnStats[$colIndex] = [
                            'name' => $headers[$colIndex] ?? "Column " . ($colIndex + 1),
                            'types' => [],
                            'null_count' => 0,
                            'empty_count' => 0,
                            'sample_values' => []
                        ];
                    }
                    
                    $colStats = &$columnStats[$colIndex];
                    
                    if ($value === null) {
                        $colStats['null_count']++;
                        $analysis['data_quality']['null_values']++;
                    } elseif ($value === '') {
                        $colStats['empty_count']++;
                        $analysis['data_quality']['empty_values']++;
                    } else {
                        // Determine data type
                        $type = $this->getDataType($value);
                        $colStats['types'][$type] = ($colStats['types'][$type] ?? 0) + 1;
                        
                        // Store sample values
                        if (count($colStats['sample_values']) < 5) {
                            $colStats['sample_values'][] = $value;
                        }
                    }
                }
                
                $rowCount++;
            }
            
            $analysis['structure']['total_rows'] = $rowCount;
            $analysis['structure']['column_counts'] = array_count_values($columnCounts);
            $analysis['structure']['consistent_columns'] = count(array_unique($columnCounts)) === 1;
            
            if (empty($headers)) {
                $analysis['structure']['total_columns'] = max($columnCounts);
            }
            
            // Process column statistics
            foreach ($columnStats as $colIndex => $stats) {
                $dominantType = array_keys($stats['types'], max($stats['types']))[0] ?? 'string';
                $typeConsistency = max($stats['types']) / $rowCount;
                
                $analysis['columns'][] = [
                    'index' => $colIndex,
                    'name' => $stats['name'],
                    'dominant_type' => $dominantType,
                    'type_consistency' => $typeConsistency,
                    'null_percentage' => ($stats['null_count'] / $rowCount) * 100,
                    'empty_percentage' => ($stats['empty_count'] / $rowCount) * 100,
                    'sample_values' => $stats['sample_values'],
                    'type_distribution' => $stats['types']
                ];
            }
            
        } finally {
            fclose($handle);
        }
        
        return $analysis;
    }

    /**
     * Combine headers with row data to create associative array
     */
    protected function combineHeadersWithRow(array $headers, array $row): array
    {
        $result = [];
        $headerCount = count($headers);
        $rowCount = count($row);
        
        // Handle cases where row has different number of columns than headers
        for ($i = 0; $i < max($headerCount, $rowCount); $i++) {
            $header = $headers[$i] ?? "Column " . ($i + 1);
            $value = $row[$i] ?? null;
            $result[$header] = $value;
        }
        
        return $result;
    }

    /**
     * Test CSV dialect by parsing sample
     */
    protected function testDialect(UploadedFile $file, array $dialect): int
    {
        $handle = $this->getFileHandle($file);
        $score = 0;
        $rowCount = 0;
        
        try {
            while (($row = fgetcsv($handle, 0, $dialect['delimiter'], $dialect['quote'])) !== false && $rowCount < 5) {
                if (count($row) > 1) {
                    $score += 10; // Points for successful parsing
                }
                
                // Points for consistent column count
                if ($rowCount > 0 && count($row) === $previousColumnCount) {
                    $score += 5;
                }
                
                $previousColumnCount = count($row);
                $rowCount++;
            }
        } finally {
            fclose($handle);
        }
        
        return $score;
    }

    /**
     * Determine data type of value
     */
    protected function getDataType($value): string
    {
        if (is_int($value)) {
            return 'integer';
        }
        
        if (is_float($value)) {
            return 'float';
        }
        
        if (is_bool($value)) {
            return 'boolean';
        }
        
        if (is_string($value)) {
            // Try to detect date
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) || strtotime($value) !== false) {
                return 'date';
            }
            
            // Try to detect email
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return 'email';
            }
            
            // Try to detect URL
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return 'url';
            }
            
            return 'string';
        }
        
        return 'unknown';
    }
}