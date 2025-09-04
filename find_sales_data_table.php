<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $filePath = 'rawdata/data 01july to 01 aug.xlsx';
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    echo "🔍 Scanning entire spreadsheet for data tables...\n";
    echo "Total rows: $highestRow, Total columns: $highestColumnIndex\n\n";
    
    $potentialDataStarts = [];
    
    // Scan every 10th row to find dense data sections
    for ($row = 1; $row <= $highestRow; $row += 5) {
        $nonEmptyCount = 0;
        $rowData = [];
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
            $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
            if (!empty($stringValue)) {
                $nonEmptyCount++;
            }
            $rowData[] = $stringValue;
        }
        
        // Look for rows with many columns filled (potential data)
        if ($nonEmptyCount >= 5) {
            echo "Dense row $row [$nonEmptyCount cells]: " . implode(' | ', array_slice($rowData, 0, 10)) . "\n";
            $potentialDataStarts[] = $row;
        }
        
        // Look for specific header patterns
        $rowText = implode(' ', $rowData);
        $rowTextLower = strtolower($rowText);
        
        if (preg_match('/\b(date|time|invoice|bill|order|receipt|item|product|total|amount|customer|table)\b.*\b(date|time|invoice|bill|order|receipt|item|product|total|amount|customer|table)\b/', $rowTextLower)) {
            echo "Potential header row $row: " . implode(' | ', array_slice($rowData, 0, 10)) . "\n";
        }
    }
    
    echo "\n🎯 Potential data start rows: " . implode(', ', $potentialDataStarts) . "\n";
    
    // Look at the most promising sections
    if (!empty($potentialDataStarts)) {
        $startRow = $potentialDataStarts[0];
        echo "\n📊 Analyzing section starting at row $startRow:\n";
        
        for ($row = $startRow; $row <= min($startRow + 10, $highestRow); $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
                $rowData[] = $stringValue;
            }
            echo "Row $row: " . implode(' | ', $rowData) . "\n";
        }
    }
    
    // Search for specific keywords to find transaction data
    echo "\n🔍 Searching for transaction keywords...\n";
    $transactionKeywords = ['Date', 'Time', 'Invoice', 'Bill No', 'Receipt', 'Order', 'Customer', 'Table', 'Amount', 'Total', 'Qty', 'Item'];
    
    for ($row = 50; $row <= min(200, $highestRow); $row++) {
        $rowData = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
            $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
            $rowData[] = $stringValue;
        }
        
        $matchCount = 0;
        foreach ($transactionKeywords as $keyword) {
            foreach ($rowData as $cell) {
                if (stripos($cell, $keyword) !== false) {
                    $matchCount++;
                    break;
                }
            }
        }
        
        if ($matchCount >= 3) {
            echo "Transaction header candidate row $row (matches: $matchCount): " . implode(' | ', array_slice($rowData, 0, 8)) . "\n";
            
            // Show next few rows as sample data
            echo "Sample data:\n";
            for ($dataRow = $row + 1; $dataRow <= min($row + 5, $highestRow); $dataRow++) {
                $dataRowData = [];
                for ($col = 1; $col <= min(8, $highestColumnIndex); $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $dataRow)->getCalculatedValue();
                    $stringValue = $cellValue !== null ? trim((string)$cellValue) : '';
                    $dataRowData[] = $stringValue;
                }
                echo "  Row $dataRow: " . implode(' | ', $dataRowData) . "\n";
            }
            echo "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}