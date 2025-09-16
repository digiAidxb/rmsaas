<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $filePath = 'rawdata/data 01july to 01 aug.xlsx';
    $spreadsheet = IOFactory::load($filePath);
    
    echo "📊 COMPREHENSIVE SALES DATA ANALYSIS\n";
    echo "====================================\n\n";
    
    // Check all sheets
    $worksheetCount = $spreadsheet->getSheetCount();
    echo "📋 Total worksheets: $worksheetCount\n";
    
    for ($sheetIndex = 0; $sheetIndex < $worksheetCount; $sheetIndex++) {
        $worksheet = $spreadsheet->getSheet($sheetIndex);
        $sheetName = $worksheet->getTitle();
        
        echo "\n🔍 SHEET: '$sheetName' (Index: $sheetIndex)\n";
        echo str_repeat("-", 50) . "\n";
        
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        echo "📐 Dimensions: $highestRow rows x $highestColumnIndex columns\n";
        
        // Extract key information from the summary
        echo "📋 Document Information:\n";
        $restaurantInfo = [];
        
        for ($row = 1; $row <= min(50, $highestRow); $row++) {
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
                
                if (!empty($stringValue)) {
                    // Look for key information
                    if (stripos($stringValue, 'restaurant') !== false || 
                        stripos($stringValue, 'ghorka') !== false ||
                        stripos($stringValue, 'gorkha') !== false) {
                        $restaurantInfo['name'] = $stringValue;
                    }
                    
                    if (preg_match('/\d{2}-\d{2}-\d{4}.*\d{2}-\d{2}-\d{4}/', $stringValue)) {
                        $restaurantInfo['date_range'] = $stringValue;
                    }
                    
                    if (stripos($stringValue, 'satwa') !== false || 
                        stripos($stringValue, 'location') !== false) {
                        $restaurantInfo['location'] = $stringValue;
                    }
                }
            }
        }
        
        foreach ($restaurantInfo as $key => $value) {
            echo "  " . ucfirst($key) . ": $value\n";
        }
        
        // Look for sales summary data
        echo "\n💰 Sales Summary Data:\n";
        $salesData = [];
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowText = '';
            $values = [];
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
                
                if (!empty($stringValue)) {
                    $rowText .= $stringValue . ' ';
                    
                    // Check if it's a number
                    if (is_numeric($stringValue) && $stringValue > 0) {
                        $values[] = $stringValue;
                    }
                }
            }
            
            // Look for specific sales metrics
            $patterns = [
                'Grand Total Sales' => '/grand.*total.*sales/i',
                'Net Sale' => '/net.*sale/i',
                'Tax Amount' => '/tax.*amount/i',
                'Net Amount' => '/net.*amount/i',
                'Cash Sales' => '/cash.*sales/i',
                'First Order' => '/first.*order/i',
                'Last Order' => '/last.*order/i',
                'First Invoice' => '/first.*invoice/i',
                'Last Invoice' => '/last.*invoice/i'
            ];
            
            foreach ($patterns as $label => $pattern) {
                if (preg_match($pattern, $rowText) && !empty($values)) {
                    $salesData[$label] = end($values);
                }
            }
        }
        
        foreach ($salesData as $metric => $value) {
            echo "  $metric: " . number_format($value, 2) . "\n";
        }
        
        // Look for detailed transaction data
        echo "\n🔍 Looking for transaction details...\n";
        
        // Search for table-like structures
        $potentialTables = [];
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $nonEmptyCount = 0;
            $consecutiveEmptyRows = 0;
            $rowData = [];
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
                
                if (!empty($stringValue)) {
                    $nonEmptyCount++;
                }
                $rowData[] = $stringValue;
            }
            
            // If this row has many filled cells, check if it's part of a table
            if ($nonEmptyCount >= 4) {
                // Check next few rows for consistency
                $tableRows = [$row => $rowData];
                $isTable = false;
                
                for ($checkRow = $row + 1; $checkRow <= min($row + 5, $highestRow); $checkRow++) {
                    $checkData = [];
                    $checkNonEmpty = 0;
                    
                    for ($col = 1; $col <= $highestColumnIndex; $col++) {
                        $cellValue = $worksheet->getCellByColumnAndRow($col, $checkRow)->getCalculatedValue();
                        $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
                        
                        if (!empty($stringValue)) {
                            $checkNonEmpty++;
                        }
                        $checkData[] = $stringValue;
                    }
                    
                    if ($checkNonEmpty >= 3) {
                        $tableRows[$checkRow] = $checkData;
                        $isTable = true;
                    }
                }
                
                if ($isTable && count($tableRows) >= 3) {
                    echo "Potential table starting at row $row:\n";
                    foreach (array_slice($tableRows, 0, 3, true) as $tableRow => $tableData) {
                        echo "  Row $tableRow: " . implode(' | ', array_slice($tableData, 0, 8)) . "\n";
                    }
                    echo "\n";
                    
                    $potentialTables[] = $row;
                }
            }
        }
        
        if (empty($potentialTables)) {
            echo "No detailed transaction tables found. This appears to be a summary report.\n";
        }
    }
    
    echo "\n✅ Analysis complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}