<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $filePath = 'rawdata/data 01july to 01 aug.xlsx';
    $spreadsheet = IOFactory::load($filePath);
    
    echo "🔍 DETAILED FIELD-BY-FIELD ANALYSIS\n";
    echo "===================================\n\n";
    
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    echo "📐 File Dimensions: $highestRow rows x $highestColumnIndex columns ($highestColumn)\n\n";
    
    // Show every single row with all columns to understand the complete structure
    echo "📋 COMPLETE ROW-BY-ROW ANALYSIS (showing all non-empty data):\n";
    echo str_repeat("=", 80) . "\n";
    
    for ($row = 1; $row <= $highestRow; $row++) {
        $rowData = [];
        $hasData = false;
        
        // Get all column data for this row
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
            $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
            
            if (!empty($stringValue)) {
                $hasData = true;
                $rowData[$col] = $stringValue;
            }
        }
        
        // Only show rows that have data
        if ($hasData) {
            echo "Row $row:\n";
            foreach ($rowData as $colNum => $value) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colNum);
                echo "  $colLetter$colNum: $value\n";
            }
            echo "\n";
        }
        
        // Stop if we've gone through too many empty rows
        if ($row > 50 && !$hasData) {
            $consecutiveEmpty = 0;
            for ($checkRow = $row - 10; $checkRow < $row; $checkRow++) {
                $checkHasData = false;
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $checkRow)->getCalculatedValue();
                    if (!empty(trim((string)$cellValue))) {
                        $checkHasData = true;
                        break;
                    }
                }
                if (!$checkHasData) {
                    $consecutiveEmpty++;
                }
            }
            
            if ($consecutiveEmpty > 8) {
                echo "... (skipping consecutive empty rows) ...\n";
                // Jump to check if there's more data later
                $foundMoreData = false;
                for ($jumpRow = $row + 20; $jumpRow <= $highestRow; $jumpRow += 20) {
                    for ($col = 1; $col <= $highestColumnIndex; $col++) {
                        $cellValue = $worksheet->getCellByColumnAndRow($col, $jumpRow)->getCalculatedValue();
                        if (!empty(trim((string)$cellValue))) {
                            $foundMoreData = true;
                            $row = $jumpRow - 1; // Will be incremented by for loop
                            break 2;
                        }
                    }
                }
                
                if (!$foundMoreData) {
                    break;
                }
            }
        }
    }
    
    echo "\n🔍 PATTERN ANALYSIS:\n";
    echo str_repeat("=", 40) . "\n";
    
    // Look for specific data patterns
    $patterns = [
        'headers' => [],
        'dates' => [],
        'amounts' => [],
        'invoice_numbers' => [],
        'items' => [],
        'tables' => []
    ];
    
    for ($row = 1; $row <= $highestRow; $row++) {
        $rowText = '';
        $rowValues = [];
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
            $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
            
            if (!empty($stringValue)) {
                $rowText .= $stringValue . ' ';
                $rowValues[] = $stringValue;
            }
        }
        
        if (!empty($rowValues)) {
            // Check for date patterns
            foreach ($rowValues as $value) {
                if (preg_match('/\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4}/', $value) ||
                    preg_match('/\d{4}-\d{2}-\d{2}/', $value) ||
                    is_numeric($value) && $value > 40000 && $value < 50000) { // Excel date range
                    $patterns['dates'][] = "Row $row: $value";
                }
                
                // Check for amount patterns (numbers with 2+ digits)
                if (is_numeric($value) && abs($value) >= 10) {
                    $patterns['amounts'][] = "Row $row: " . number_format($value, 2);
                }
                
                // Check for invoice/bill numbers
                if (preg_match('/\b(invoice|bill|receipt|order)\s*#?\s*(\d+)/i', $value, $matches) ||
                    (is_numeric($value) && $value >= 1 && $value <= 1000 && strlen((string)$value) <= 4)) {
                    $patterns['invoice_numbers'][] = "Row $row: $value";
                }
            }
            
            // Check for table-like structures (rows with multiple numeric/structured values)
            if (count($rowValues) >= 4) {
                $numericCount = 0;
                foreach ($rowValues as $val) {
                    if (is_numeric($val)) {
                        $numericCount++;
                    }
                }
                
                if ($numericCount >= 2) {
                    $patterns['tables'][] = "Row $row: " . implode(' | ', $rowValues);
                }
            }
            
            // Check for header patterns
            $headerKeywords = ['date', 'time', 'invoice', 'bill', 'amount', 'total', 'item', 'quantity', 'customer', 'table', 'order'];
            $headerMatches = 0;
            
            foreach ($headerKeywords as $keyword) {
                if (stripos($rowText, $keyword) !== false) {
                    $headerMatches++;
                }
            }
            
            if ($headerMatches >= 2) {
                $patterns['headers'][] = "Row $row: $rowText";
            }
        }
    }
    
    // Display patterns found
    foreach ($patterns as $patternType => $matches) {
        if (!empty($matches)) {
            echo "\n" . strtoupper($patternType) . " FOUND:\n";
            foreach (array_slice($matches, 0, 10) as $match) { // Show first 10 matches
                echo "  $match\n";
            }
            if (count($matches) > 10) {
                echo "  ... and " . (count($matches) - 10) . " more\n";
            }
        }
    }
    
    echo "\n✅ Detailed analysis complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}