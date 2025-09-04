<?php

namespace App\Services\Import\Detectors;

use App\Services\Import\Contracts\FileParserInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PosFormatDetector
{
    protected FileParserInterface $fileParser;
    protected array $posDetectors = [];

    public function __construct(FileParserInterface $fileParser)
    {
        $this->fileParser = $fileParser;
        $this->registerPosDetectors();
    }

    /**
     * Detect POS system from uploaded file
     */
    public function detectPosSystem(UploadedFile $file): array
    {
        $results = [];
        
        try {
            // Get file metadata
            $fileInfo = $this->analyzeFileInfo($file);
            
            // Get sample data for analysis
            $sampleData = $this->fileParser->getSampleRows($file, 20);
            $headers = $this->fileParser->getHeaders($file);
            
            // Run all POS detectors
            foreach ($this->posDetectors as $posSystem => $detector) {
                $confidence = $detector($fileInfo, $headers, $sampleData);
                
                if ($confidence > 0) {
                    $results[] = [
                        'pos_system' => $posSystem,
                        'confidence' => $confidence,
                        'detected_features' => $this->getDetectedFeatures($posSystem, $headers, $sampleData),
                        'import_suggestions' => $this->getImportSuggestions($posSystem, $headers)
                    ];
                }
            }
            
        } catch (\Exception $e) {
            // Return generic result if detection fails
            $results[] = [
                'pos_system' => 'generic',
                'confidence' => 30,
                'detected_features' => ['fallback_detection' => true],
                'import_suggestions' => ['type' => 'manual_mapping_required']
            ];
        }
        
        // Sort by confidence
        usort($results, fn($a, $b) => $b['confidence'] <=> $a['confidence']);
        
        return $results;
    }

    /**
     * Get the best match POS system
     */
    public function getBestMatch(UploadedFile $file): array
    {
        $detections = $this->detectPosSystem($file);
        
        return !empty($detections) ? $detections[0] : [
            'pos_system' => 'generic',
            'confidence' => 0,
            'detected_features' => [],
            'import_suggestions' => ['type' => 'unknown_format']
        ];
    }

    /**
     * Check if file matches specific POS system
     */
    public function matchesPosSystem(UploadedFile $file, string $posSystem): int
    {
        if (!isset($this->posDetectors[$posSystem])) {
            return 0;
        }
        
        try {
            $fileInfo = $this->analyzeFileInfo($file);
            $sampleData = $this->fileParser->getSampleRows($file, 10);
            $headers = $this->fileParser->getHeaders($file);
            
            return $this->posDetectors[$posSystem]($fileInfo, $headers, $sampleData);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get detailed analysis of file format
     */
    public function analyzeFormat(UploadedFile $file): array
    {
        $fileInfo = $this->analyzeFileInfo($file);
        $sampleData = $this->fileParser->getSampleRows($file, 50);
        $headers = $this->fileParser->getHeaders($file);
        
        return [
            'file_info' => $fileInfo,
            'headers' => $headers,
            'header_analysis' => $this->analyzeHeaders($headers),
            'data_patterns' => $this->analyzeDataPatterns($sampleData),
            'pos_detections' => $this->detectPosSystem($file),
            'import_readiness' => $this->assessImportReadiness($headers, $sampleData)
        ];
    }

    /**
     * Register POS system detectors
     */
    protected function registerPosDetectors(): void
    {
        $this->posDetectors = [
            'square' => [$this, 'detectSquare'],
            'toast' => [$this, 'detectToast'],
            'clover' => [$this, 'detectClover'],
            'lightspeed' => [$this, 'detectLightspeed'],
            'touchbistro' => [$this, 'detectTouchBistro'],
            'resy' => [$this, 'detectResy'],
            'opentable' => [$this, 'detectOpenTable'],
            'aloha' => [$this, 'detectAloha'],
            'micros' => [$this, 'detectMicros'],
        ];
    }

    /**
     * Detect Square POS format
     */
    protected function detectSquare(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['square', 'Square'])) {
            $confidence += 20;
        }
        
        // Check header patterns specific to Square
        $squareHeaders = [
            'Transaction ID', 'Date', 'Time', 'Time Zone', 'Gross Sales',
            'Discounts', 'Net Sales', 'Gift Card Sales', 'Tax', 'Tip',
            'Partial Refunds', 'Total Collected', 'Source', 'Card',
            'Card Entry Methods', 'Cash', 'Square Gift Card', 'Other Tender',
            'Itemization', 'Notes', 'Details', 'Event Type'
        ];
        
        $matchedHeaders = array_intersect($headers, $squareHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($squareHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        // Check for Square-specific data patterns
        if (!empty($sampleData)) {
            foreach ($sampleData as $row) {
                // Square transaction IDs are typically alphanumeric
                if (isset($row['Transaction ID']) && preg_match('/^[A-Z0-9]{10,}$/', $row['Transaction ID'])) {
                    $confidence += 5;
                }
                
                // Square date format
                if (isset($row['Date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $row['Date'])) {
                    $confidence += 3;
                }
                
                // Square time format
                if (isset($row['Time']) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $row['Time'])) {
                    $confidence += 3;
                }
            }
        }
        
        return min(100, $confidence);
    }

    /**
     * Detect Toast POS format
     */
    protected function detectToast(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['toast', 'Toast', 'TOAST'])) {
            $confidence += 20;
        }
        
        // Check header patterns specific to Toast
        $toastHeaders = [
            'Check ID', 'Order Date', 'Order Time', 'Restaurant', 'Location',
            'Server', 'Table', 'Party Size', 'Menu Item', 'Menu Group',
            'Quantity', 'Price', 'Discount Amount', 'Net Price', 'Tax Amount',
            'Tip Amount', 'Total Amount', 'Payment Method'
        ];
        
        $matchedHeaders = array_intersect($headers, $toastHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($toastHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        // Check for Toast-specific patterns
        if (!empty($sampleData)) {
            foreach ($sampleData as $row) {
                // Toast Check IDs are typically numeric
                if (isset($row['Check ID']) && is_numeric($row['Check ID'])) {
                    $confidence += 3;
                }
                
                // Toast menu group structure
                if (isset($row['Menu Group']) && !empty($row['Menu Group'])) {
                    $confidence += 2;
                }
            }
        }
        
        return min(100, $confidence);
    }

    /**
     * Detect Clover POS format
     */
    protected function detectClover(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['clover', 'Clover', 'CLOVER'])) {
            $confidence += 20;
        }
        
        // Check header patterns specific to Clover
        $cloverHeaders = [
            'Order ID', 'Device', 'Employee', 'Order Type', 'Created Date',
            'Created Time', 'Item Name', 'Item Code', 'Category', 'Price',
            'Quantity', 'Discount', 'Tax', 'Total', 'Payment Method',
            'Card Type', 'Last 4', 'Entry Method'
        ];
        
        $matchedHeaders = array_intersect($headers, $cloverHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($cloverHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        // Check for Clover-specific patterns
        if (!empty($sampleData)) {
            foreach ($sampleData as $row) {
                // Clover Order IDs are typically alphanumeric
                if (isset($row['Order ID']) && preg_match('/^[A-Z0-9]{8,}$/', $row['Order ID'])) {
                    $confidence += 5;
                }
                
                // Clover device names
                if (isset($row['Device']) && Str::startsWith($row['Device'], 'Station')) {
                    $confidence += 3;
                }
            }
        }
        
        return min(100, $confidence);
    }

    /**
     * Detect Lightspeed POS format
     */
    protected function detectLightspeed(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['lightspeed', 'Lightspeed', 'LightSpeed'])) {
            $confidence += 20;
        }
        
        // Check header patterns specific to Lightspeed
        $lightspeedHeaders = [
            'Sale ID', 'Time', 'Register', 'Employee', 'Customer ID',
            'Customer', 'Item', 'Description', 'Category', 'Qty',
            'Unit Price', 'Discount %', 'Discount Value', 'Tax 1',
            'Tax 2', 'Total', 'Cost', 'Profit', 'Margin'
        ];
        
        $matchedHeaders = array_intersect($headers, $lightspeedHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($lightspeedHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        // Check for Lightspeed-specific patterns
        if (!empty($sampleData)) {
            foreach ($sampleData as $row) {
                // Lightspeed Sale IDs are typically numeric
                if (isset($row['Sale ID']) && is_numeric($row['Sale ID'])) {
                    $confidence += 3;
                }
                
                // Lightspeed register format
                if (isset($row['Register']) && preg_match('/^Register \d+$/', $row['Register'])) {
                    $confidence += 5;
                }
            }
        }
        
        return min(100, $confidence);
    }

    /**
     * Detect TouchBistro POS format
     */
    protected function detectTouchBistro(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['touchbistro', 'TouchBistro', 'TB'])) {
            $confidence += 20;
        }
        
        // Check header patterns specific to TouchBistro
        $touchBistroHeaders = [
            'Bill Number', 'Date', 'Time', 'Table', 'Covers', 'Server',
            'Item', 'Category', 'Menu', 'Quantity', 'Price', 'Total',
            'Discount', 'Tax', 'Service Charge', 'Payment Type'
        ];
        
        $matchedHeaders = array_intersect($headers, $touchBistroHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($touchBistroHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        return min(100, $confidence);
    }

    /**
     * Detect Resy POS format
     */
    protected function detectResy(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['resy', 'Resy', 'RESY'])) {
            $confidence += 20;
        }
        
        // Resy is primarily reservations, different headers
        $resyHeaders = [
            'Reservation ID', 'Date', 'Time', 'Party Size', 'Guest Name',
            'Email', 'Phone', 'Table', 'Status', 'Created', 'Modified',
            'Notes', 'Source', 'VIP'
        ];
        
        $matchedHeaders = array_intersect($headers, $resyHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($resyHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        return min(100, $confidence);
    }

    /**
     * Detect OpenTable POS format
     */
    protected function detectOpenTable(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['opentable', 'OpenTable', 'OT'])) {
            $confidence += 20;
        }
        
        // OpenTable headers
        $openTableHeaders = [
            'Reservation ID', 'Confirmation Number', 'Date', 'Time',
            'Party Size', 'Guest Name', 'Phone', 'Email', 'Table Number',
            'Seating Area', 'Reservation Source', 'Status', 'Special Requests'
        ];
        
        $matchedHeaders = array_intersect($headers, $openTableHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($openTableHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        return min(100, $confidence);
    }

    /**
     * Detect Aloha POS format
     */
    protected function detectAloha(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['aloha', 'Aloha', 'ALOHA'])) {
            $confidence += 20;
        }
        
        // Aloha headers
        $alohaHeaders = [
            'Check Number', 'Business Date', 'Open Time', 'Close Time',
            'Table Number', 'Guest Count', 'Server', 'Item Name',
            'Menu Item Number', 'Category', 'Quantity', 'Price',
            'Tax Amount', 'Total Amount'
        ];
        
        $matchedHeaders = array_intersect($headers, $alohaHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($alohaHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        return min(100, $confidence);
    }

    /**
     * Detect Micros POS format
     */
    protected function detectMicros(array $fileInfo, array $headers, array $sampleData): int
    {
        $confidence = 0;
        
        // Check filename patterns
        if (Str::contains($fileInfo['filename'], ['micros', 'Micros', 'MICROS'])) {
            $confidence += 20;
        }
        
        // Micros headers
        $microsHeaders = [
            'Check ID', 'Business Date', 'Revenue Center', 'Workstation',
            'Employee', 'Table', 'Guests', 'Menu Item', 'Major Group',
            'Family Group', 'Quantity', 'Price', 'Discount', 'Tax',
            'Service Charge', 'Total'
        ];
        
        $matchedHeaders = array_intersect($headers, $microsHeaders);
        $headerMatchRatio = count($matchedHeaders) / max(count($microsHeaders), 1);
        $confidence += $headerMatchRatio * 60;
        
        return min(100, $confidence);
    }

    /**
     * Analyze file information
     */
    protected function analyzeFileInfo(UploadedFile $file): array
    {
        return [
            'filename' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'created_time' => filemtime($file->getPathname())
        ];
    }

    /**
     * Analyze headers for patterns
     */
    protected function analyzeHeaders(array $headers): array
    {
        return [
            'count' => count($headers),
            'patterns' => [
                'has_id_fields' => $this->hasPatternInHeaders($headers, ['id', 'ID', 'Id']),
                'has_date_fields' => $this->hasPatternInHeaders($headers, ['date', 'Date', 'DATE', 'time', 'Time']),
                'has_price_fields' => $this->hasPatternInHeaders($headers, ['price', 'Price', 'amount', 'Amount', 'total', 'Total']),
                'has_quantity_fields' => $this->hasPatternInHeaders($headers, ['qty', 'Qty', 'quantity', 'Quantity']),
                'has_customer_fields' => $this->hasPatternInHeaders($headers, ['customer', 'Customer', 'guest', 'Guest']),
                'has_employee_fields' => $this->hasPatternInHeaders($headers, ['server', 'Server', 'employee', 'Employee']),
                'has_table_fields' => $this->hasPatternInHeaders($headers, ['table', 'Table', 'TABLE'])
            ],
            'likely_types' => $this->categorizeHeaders($headers)
        ];
    }

    /**
     * Check if headers contain specific patterns
     */
    protected function hasPatternInHeaders(array $headers, array $patterns): bool
    {
        foreach ($headers as $header) {
            foreach ($patterns as $pattern) {
                if (Str::contains($header, $pattern)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Categorize headers by likely purpose
     */
    protected function categorizeHeaders(array $headers): array
    {
        $categories = [
            'identifiers' => [],
            'dates' => [],
            'financials' => [],
            'quantities' => [],
            'descriptive' => [],
            'operational' => []
        ];
        
        foreach ($headers as $header) {
            $lowerHeader = strtolower($header);
            
            if (Str::contains($lowerHeader, ['id', 'number', 'code'])) {
                $categories['identifiers'][] = $header;
            } elseif (Str::contains($lowerHeader, ['date', 'time', 'created', 'modified'])) {
                $categories['dates'][] = $header;
            } elseif (Str::contains($lowerHeader, ['price', 'amount', 'total', 'cost', 'tax', 'tip', 'discount'])) {
                $categories['financials'][] = $header;
            } elseif (Str::contains($lowerHeader, ['qty', 'quantity', 'count', 'size'])) {
                $categories['quantities'][] = $header;
            } elseif (Str::contains($lowerHeader, ['name', 'description', 'item', 'category', 'type'])) {
                $categories['descriptive'][] = $header;
            } else {
                $categories['operational'][] = $header;
            }
        }
        
        return $categories;
    }

    /**
     * Analyze data patterns in sample
     */
    protected function analyzeDataPatterns(array $sampleData): array
    {
        if (empty($sampleData)) {
            return ['patterns' => [], 'data_types' => []];
        }
        
        $patterns = [];
        $dataTypes = [];
        
        $firstRow = $sampleData[0];
        foreach ($firstRow as $column => $value) {
            $columnData = array_column($sampleData, $column);
            
            $patterns[$column] = [
                'has_nulls' => in_array(null, $columnData),
                'all_numeric' => $this->isAllNumeric($columnData),
                'all_dates' => $this->isAllDates($columnData),
                'all_emails' => $this->isAllEmails($columnData),
                'unique_values' => count(array_unique($columnData)),
                'sample_values' => array_slice(array_unique($columnData), 0, 5)
            ];
            
            $dataTypes[$column] = $this->detectColumnDataType($columnData);
        }
        
        return ['patterns' => $patterns, 'data_types' => $dataTypes];
    }

    /**
     * Check if all values in array are numeric
     */
    protected function isAllNumeric(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && !is_numeric($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if all values are dates
     */
    protected function isAllDates(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && strtotime($value) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if all values are emails
     */
    protected function isAllEmails(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Detect column data type
     */
    protected function detectColumnDataType(array $values): string
    {
        if ($this->isAllNumeric($values)) {
            return 'numeric';
        } elseif ($this->isAllDates($values)) {
            return 'date';
        } elseif ($this->isAllEmails($values)) {
            return 'email';
        } else {
            return 'text';
        }
    }

    /**
     * Get detected features for POS system
     */
    protected function getDetectedFeatures(string $posSystem, array $headers, array $sampleData): array
    {
        $features = [
            'pos_system' => $posSystem,
            'detected_headers' => array_intersect($headers, $this->getPosSystemHeaders($posSystem)),
            'data_structure' => $this->analyzeDataStructure($sampleData),
            'confidence_factors' => $this->getConfidenceFactors($posSystem, $headers, $sampleData)
        ];
        
        return $features;
    }

    /**
     * Get expected headers for POS system
     */
    protected function getPosSystemHeaders(string $posSystem): array
    {
        $headerMappings = [
            'square' => ['Transaction ID', 'Date', 'Time', 'Gross Sales', 'Net Sales'],
            'toast' => ['Check ID', 'Order Date', 'Menu Item', 'Quantity', 'Price'],
            'clover' => ['Order ID', 'Device', 'Item Name', 'Category', 'Total'],
            'lightspeed' => ['Sale ID', 'Time', 'Item', 'Description', 'Qty'],
            'touchbistro' => ['Bill Number', 'Date', 'Item', 'Category', 'Price'],
            'resy' => ['Reservation ID', 'Date', 'Party Size', 'Guest Name'],
            'opentable' => ['Confirmation Number', 'Date', 'Time', 'Guest Name'],
            'aloha' => ['Check Number', 'Business Date', 'Item Name', 'Quantity'],
            'micros' => ['Check ID', 'Business Date', 'Menu Item', 'Total']
        ];
        
        return $headerMappings[$posSystem] ?? [];
    }

    /**
     * Analyze data structure
     */
    protected function analyzeDataStructure(array $sampleData): array
    {
        return [
            'row_count' => count($sampleData),
            'column_count' => !empty($sampleData) ? count($sampleData[0]) : 0,
            'has_consistent_structure' => $this->hasConsistentStructure($sampleData),
            'data_density' => $this->calculateDataDensity($sampleData)
        ];
    }

    /**
     * Check if data has consistent structure
     */
    protected function hasConsistentStructure(array $sampleData): bool
    {
        if (empty($sampleData)) {
            return false;
        }
        
        $columnCount = count($sampleData[0]);
        foreach ($sampleData as $row) {
            if (count($row) !== $columnCount) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Calculate data density (non-empty values)
     */
    protected function calculateDataDensity(array $sampleData): float
    {
        if (empty($sampleData)) {
            return 0;
        }
        
        $totalCells = count($sampleData) * count($sampleData[0]);
        $filledCells = 0;
        
        foreach ($sampleData as $row) {
            foreach ($row as $cell) {
                if (!empty($cell) && $cell !== null) {
                    $filledCells++;
                }
            }
        }
        
        return $totalCells > 0 ? ($filledCells / $totalCells) * 100 : 0;
    }

    /**
     * Get confidence factors for detection
     */
    protected function getConfidenceFactors(string $posSystem, array $headers, array $sampleData): array
    {
        return [
            'header_match' => count(array_intersect($headers, $this->getPosSystemHeaders($posSystem))),
            'filename_match' => Str::contains(strtolower($posSystem), strtolower($posSystem)),
            'data_pattern_match' => $this->checkDataPatterns($posSystem, $sampleData),
            'structure_match' => $this->checkStructureMatch($posSystem, $sampleData)
        ];
    }

    /**
     * Check data patterns for POS system
     */
    protected function checkDataPatterns(string $posSystem, array $sampleData): int
    {
        // Implementation would check for system-specific data patterns
        return 50; // Placeholder
    }

    /**
     * Check structure match for POS system
     */
    protected function checkStructureMatch(string $posSystem, array $sampleData): int
    {
        // Implementation would check for system-specific structure patterns
        return 50; // Placeholder
    }

    /**
     * Get import suggestions for POS system
     */
    protected function getImportSuggestions(string $posSystem, array $headers): array
    {
        $suggestions = [
            'recommended_import_type' => $this->suggestImportType($headers),
            'suggested_mappings' => $this->suggestFieldMappings($posSystem, $headers),
            'preprocessing_steps' => $this->getPreprocessingSteps($posSystem),
            'validation_rules' => $this->getValidationRules($posSystem)
        ];
        
        return $suggestions;
    }

    /**
     * Suggest import type based on headers
     */
    protected function suggestImportType(array $headers): string
    {
        $headerString = implode(' ', $headers);
        
        if (Str::contains($headerString, ['Menu', 'Item', 'Category', 'Price'])) {
            return 'menu';
        } elseif (Str::contains($headerString, ['Inventory', 'Stock', 'Quantity'])) {
            return 'inventory';
        } elseif (Str::contains($headerString, ['Sale', 'Transaction', 'Order'])) {
            return 'sales';
        } elseif (Str::contains($headerString, ['Recipe', 'Ingredient'])) {
            return 'recipes';
        } elseif (Str::contains($headerString, ['Customer', 'Guest'])) {
            return 'customers';
        } else {
            return 'mixed';
        }
    }

    /**
     * Suggest field mappings for POS system
     */
    protected function suggestFieldMappings(string $posSystem, array $headers): array
    {
        // This would return suggested field mappings based on POS system
        return [];
    }

    /**
     * Get preprocessing steps for POS system
     */
    protected function getPreprocessingSteps(string $posSystem): array
    {
        $steps = [
            'square' => ['normalize_dates', 'clean_currency', 'handle_timezone'],
            'toast' => ['merge_line_items', 'calculate_totals', 'standardize_categories'],
            'clover' => ['parse_device_info', 'handle_modifiers', 'clean_payment_data'],
            // Add more POS-specific steps
        ];
        
        return $steps[$posSystem] ?? ['standard_cleanup', 'validate_required_fields'];
    }

    /**
     * Get validation rules for POS system
     */
    protected function getValidationRules(string $posSystem): array
    {
        return [
            'required_fields' => $this->getRequiredFields($posSystem),
            'data_types' => $this->getExpectedDataTypes($posSystem),
            'constraints' => $this->getDataConstraints($posSystem)
        ];
    }

    /**
     * Get required fields for POS system
     */
    protected function getRequiredFields(string $posSystem): array
    {
        $requiredFields = [
            'square' => ['Transaction ID', 'Date', 'Net Sales'],
            'toast' => ['Check ID', 'Order Date', 'Menu Item'],
            'clover' => ['Order ID', 'Item Name', 'Total'],
            // Add more POS-specific required fields
        ];
        
        return $requiredFields[$posSystem] ?? [];
    }

    /**
     * Get expected data types for POS system
     */
    protected function getExpectedDataTypes(string $posSystem): array
    {
        return [
            'id_fields' => 'string',
            'date_fields' => 'date',
            'price_fields' => 'decimal',
            'quantity_fields' => 'integer'
        ];
    }

    /**
     * Get data constraints for POS system
     */
    protected function getDataConstraints(string $posSystem): array
    {
        return [
            'price_min' => 0,
            'quantity_min' => 0,
            'date_format' => 'Y-m-d',
            'required_precision' => 2
        ];
    }

    /**
     * Assess import readiness
     */
    protected function assessImportReadiness(array $headers, array $sampleData): array
    {
        $readiness = [
            'score' => 0,
            'issues' => [],
            'recommendations' => []
        ];
        
        // Check for basic requirements
        if (!empty($headers)) {
            $readiness['score'] += 30;
        } else {
            $readiness['issues'][] = 'No headers detected';
            $readiness['recommendations'][] = 'Ensure file has header row';
        }
        
        if (!empty($sampleData)) {
            $readiness['score'] += 30;
        } else {
            $readiness['issues'][] = 'No data rows found';
        }
        
        if ($this->hasConsistentStructure($sampleData)) {
            $readiness['score'] += 20;
        } else {
            $readiness['issues'][] = 'Inconsistent row structure';
            $readiness['recommendations'][] = 'Clean up data structure before import';
        }
        
        $density = $this->calculateDataDensity($sampleData);
        if ($density > 70) {
            $readiness['score'] += 20;
        } elseif ($density > 50) {
            $readiness['score'] += 10;
            $readiness['recommendations'][] = 'Consider filling missing data fields';
        } else {
            $readiness['issues'][] = 'High percentage of empty cells';
            $readiness['recommendations'][] = 'Review and clean data before import';
        }
        
        return $readiness;
    }
}