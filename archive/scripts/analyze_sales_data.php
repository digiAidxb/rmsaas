<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Comprehensive analysis of the sales data Excel file
 * to understand its structure for proper import mapping
 */
function analyzeSalesDataStructure() {
    $filePath = 'rawdata/data 01july to 01 aug.xlsx';
    
    if (!file_exists($filePath)) {
        echo "❌ Sales data file not found: {$filePath}\n";
        return;
    }
    
    echo "🔍 Analyzing Sales Data File: {$filePath}\n";
    echo "File size: " . number_format(filesize($filePath)) . " bytes\n\n";
    
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        echo "📋 WORKSHEET INFORMATION\n";
        echo "=======================\n";
        echo "Sheet name: " . $worksheet->getTitle() . "\n";
        echo "Highest row: " . $worksheet->getHighestRow() . "\n";
        echo "Highest column: " . $worksheet->getHighestColumn() . "\n";
        
        // Get all worksheets
        $worksheetNames = $spreadsheet->getSheetNames();
        echo "Total worksheets: " . count($worksheetNames) . "\n";
        foreach ($worksheetNames as $index => $name) {
            echo "  Sheet {$index}: {$name}\n";
        }
        echo "\n";
        
        // Analyze headers (first row)
        echo "📊 HEADER ANALYSIS (Row 1)\n";
        echo "===========================\n";
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $columnIndex = 1;
        
        for ($col = 'A'; $col !== $highestColumn; $col++) {
            $headerValue = $worksheet->getCell($col . '1')->getCalculatedValue();
            if (!empty($headerValue)) {
                $headers[$col] = trim($headerValue);
                echo "Column {$col} ({$columnIndex}): '{$headerValue}'\n";
                $columnIndex++;
            }
        }
        
        // Check last column
        $headerValue = $worksheet->getCell($highestColumn . '1')->getCalculatedValue();
        if (!empty($headerValue)) {
            $headers[$highestColumn] = trim($headerValue);
            echo "Column {$highestColumn} ({$columnIndex}): '{$headerValue}'\n";
        }
        
        echo "\nTotal headers found: " . count($headers) . "\n\n";
        
        // Analyze data types and patterns
        echo "🔍 DATA TYPE ANALYSIS (First 10 rows)\n";
        echo "=====================================\n";
        
        foreach ($headers as $col => $header) {
            echo "\n📋 Column {$col}: '{$header}'\n";
            echo str_repeat('-', strlen($header) + 15) . "\n";
            
            $sampleValues = [];
            $types = [];
            $nullCount = 0;
            
            // Sample first 10 data rows (rows 2-11)
            for ($row = 2; $row <= min(11, $worksheet->getHighestRow()); $row++) {
                $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                
                if ($cellValue === null || $cellValue === '') {
                    $nullCount++;
                    continue;
                }
                
                $sampleValues[] = $cellValue;
                
                // Determine data type
                if (is_numeric($cellValue)) {
                    if (strpos($cellValue, '.') !== false) {
                        $types['decimal'] = ($types['decimal'] ?? 0) + 1;
                    } else {
                        $types['integer'] = ($types['integer'] ?? 0) + 1;
                    }
                } elseif (Date::isDateTime($worksheet->getCell($col . $row))) {
                    $types['date'] = ($types['date'] ?? 0) + 1;
                } else {
                    $types['text'] = ($types['text'] ?? 0) + 1;
                }
            }
            
            // Show sample values
            echo "Sample values: " . implode(', ', array_slice($sampleValues, 0, 5)) . "\n";
            echo "Data types: " . json_encode($types) . "\n";
            echo "Null/Empty: {$nullCount}/10\n";
            
            // Determine dominant type
            $dominantType = 'text';
            $maxCount = 0;
            foreach ($types as $type => $count) {
                if ($count > $maxCount) {
                    $maxCount = $count;
                    $dominantType = $type;
                }
            }
            echo "Dominant type: {$dominantType}\n";
        }
        
        // Deep analysis of entire dataset
        echo "\n\n📈 COMPREHENSIVE DATA ANALYSIS\n";
        echo "==============================\n";
        
        $totalRows = $worksheet->getHighestRow();
        $dataRows = $totalRows - 1; // Excluding header
        
        echo "Total rows: {$totalRows}\n";
        echo "Data rows: {$dataRows}\n\n";
        
        // Sample full rows for context
        echo "📋 SAMPLE DATA ROWS (Rows 2-6):\n";
        echo "===============================\n";
        
        for ($row = 2; $row <= min(6, $totalRows); $row++) {
            echo "\n--- Row {$row} ---\n";
            foreach ($headers as $col => $header) {
                $value = $worksheet->getCell($col . $row)->getCalculatedValue();
                echo "{$header}: " . ($value ?? '[NULL]') . "\n";
            }
        }
        
        // Generate field mapping suggestions
        echo "\n\n🔧 FIELD MAPPING ANALYSIS\n";
        echo "=========================\n";
        
        generateFieldMappingSuggestions($headers);
        
        return [
            'headers' => $headers,
            'total_rows' => $totalRows,
            'data_rows' => $dataRows,
            'worksheet' => $worksheet
        ];
        
    } catch (Exception $e) {
        echo "❌ Error analyzing file: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

function generateFieldMappingSuggestions($headers) {
    echo "Analyzing fields for import mapping...\n\n";
    
    // Standard import fields for sales data
    $standardFields = [
        'transaction_id' => ['transaction', 'id', 'order', 'receipt', 'number', 'ref'],
        'date' => ['date', 'time', 'timestamp', 'created', 'day'],
        'item_name' => ['item', 'product', 'menu', 'name', 'description', 'dish'],
        'quantity' => ['quantity', 'qty', 'amount', 'units', 'count'],
        'unit_price' => ['price', 'cost', 'rate', 'unit_price', 'each'],
        'total_amount' => ['total', 'amount', 'sum', 'grand_total', 'value'],
        'category' => ['category', 'group', 'type', 'class', 'section'],
        'payment_method' => ['payment', 'method', 'pay_type', 'tender'],
        'server' => ['server', 'employee', 'staff', 'waiter', 'user'],
        'table' => ['table', 'seat', 'location', 'station']
    ];
    
    $mappings = [];
    
    foreach ($headers as $col => $header) {
        $headerLower = strtolower(trim($header));
        $bestMatch = null;
        $bestConfidence = 0;
        
        // Try to match against standard fields
        foreach ($standardFields as $standardField => $keywords) {
            foreach ($keywords as $keyword) {
                $confidence = 0;
                
                // Exact match
                if ($headerLower === $keyword) {
                    $confidence = 100;
                } 
                // Contains keyword
                elseif (strpos($headerLower, $keyword) !== false) {
                    $confidence = 80;
                }
                // Keyword contains header (for short headers)
                elseif (strpos($keyword, $headerLower) !== false) {
                    $confidence = 60;
                }
                
                if ($confidence > $bestConfidence) {
                    $bestMatch = $standardField;
                    $bestConfidence = $confidence;
                }
            }
        }
        
        $mappings[] = [
            'source_column' => $col,
            'source_field' => $header,
            'target_field' => $bestMatch ?? 'unmapped',
            'confidence' => $bestConfidence
        ];
        
        $status = $bestConfidence >= 80 ? '✅' : ($bestConfidence >= 60 ? '⚠️' : '❌');
        echo "{$status} Column {$col}: '{$header}' → {$bestMatch} ({$bestConfidence}%)\n";
    }
    
    echo "\n🔧 IMPORT MAPPING JSON:\n";
    echo "=======================\n";
    echo json_encode($mappings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    return $mappings;
}

// Execute analysis
echo "🚀 Starting comprehensive sales data analysis...\n\n";
$result = analyzeSalesDataStructure();
echo "\n✅ Analysis complete!\n";

?>