<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Deep investigation of sales data file structure
 * to understand why no data is being detected
 */
function deepAnalyzeSalesData() {
    $filePath = 'rawdata/data 01july to 01 aug.xlsx';
    
    if (!file_exists($filePath)) {
        echo "❌ Sales data file not found: {$filePath}\n";
        return;
    }
    
    echo "🔍 DEEP ANALYSIS: {$filePath}\n";
    echo "File size: " . number_format(filesize($filePath)) . " bytes\n\n";
    
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        echo "📋 RAW WORKSHEET INFO\n";
        echo "====================\n";
        echo "Title: " . $worksheet->getTitle() . "\n";
        echo "Highest row: " . $worksheet->getHighestRow() . "\n";
        echo "Highest column: " . $worksheet->getHighestColumn() . "\n";
        echo "Highest data row: " . $worksheet->getHighestDataRow() . "\n";
        echo "Highest data column: " . $worksheet->getHighestDataColumn() . "\n\n";
        
        // Check if data exists in first 20 rows across all columns
        echo "🔍 RAW CELL INVESTIGATION (First 20 rows, All columns A-W)\n";
        echo "========================================================\n";
        
        $foundData = false;
        $nonEmptyCount = 0;
        
        for ($row = 1; $row <= min(20, $worksheet->getHighestRow()); $row++) {
            $rowHasData = false;
            $rowData = [];
            
            for ($col = 'A'; $col <= 'W'; $col++) {
                $cell = $worksheet->getCell($col . $row);
                $value = $cell->getCalculatedValue();
                $formattedValue = $cell->getFormattedValue();
                $rawValue = $cell->getValue();
                
                if ($value !== null && $value !== '') {
                    $rowHasData = true;
                    $foundData = true;
                    $nonEmptyCount++;
                    $rowData[] = "{$col}: '{$value}'";
                    
                    if ($value !== $formattedValue || $value !== $rawValue) {
                        echo "📍 Row {$row}, Col {$col}:\n";
                        echo "  Raw: " . var_export($rawValue, true) . "\n";
                        echo "  Calculated: " . var_export($value, true) . "\n";
                        echo "  Formatted: " . var_export($formattedValue, true) . "\n";
                    }
                }
            }
            
            if ($rowHasData) {
                echo "Row {$row}: " . implode(' | ', $rowData) . "\n";
            }
        }
        
        if (!$foundData) {
            echo "❌ NO DATA FOUND in first 20 rows!\n\n";
            
            // Check if data exists elsewhere in the file
            echo "🔍 SCANNING ENTIRE FILE FOR DATA...\n";
            echo "===================================\n";
            
            $dataFound = [];
            $totalRows = $worksheet->getHighestRow();
            
            // Sample every 50th row
            for ($row = 1; $row <= $totalRows; $row += 50) {
                for ($col = 'A'; $col <= 'W'; $col++) {
                    $value = $worksheet->getCell($col . $row)->getCalculatedValue();
                    if ($value !== null && $value !== '') {
                        $dataFound[] = "Row {$row}, Col {$col}: '{$value}'";
                        if (count($dataFound) >= 10) break 2; // Stop after finding 10 samples
                    }
                }
            }
            
            if (!empty($dataFound)) {
                echo "📍 FOUND DATA SAMPLES:\n";
                foreach ($dataFound as $sample) {
                    echo "  {$sample}\n";
                }
            } else {
                echo "❌ NO DATA FOUND ANYWHERE IN FILE!\n";
            }
        } else {
            echo "\n✅ Found {$nonEmptyCount} non-empty cells in first 20 rows\n";
        }
        
        // Check worksheet properties
        echo "\n📊 WORKSHEET PROPERTIES\n";
        echo "======================\n";
        
        // Check if there are merged cells
        $mergeCells = $worksheet->getMergeCells();
        if (!empty($mergeCells)) {
            echo "📍 Merged cells found:\n";
            foreach ($mergeCells as $mergeRange) {
                echo "  {$mergeRange}\n";
            }
        } else {
            echo "No merged cells found\n";
        }
        
        // Check protection
        echo "Protection: " . ($worksheet->getProtection()->getSheet() ? "Yes" : "No") . "\n";
        
        // Check dimensions
        $dimension = $worksheet->calculateWorksheetDimension();
        echo "Calculated dimension: {$dimension}\n";
        
        // Try to find the actual data range
        echo "\n🔍 FINDING ACTUAL DATA RANGE\n";
        echo "===========================\n";
        
        $actualDataFound = false;
        $firstDataRow = null;
        $firstDataCol = null;
        
        // Check every cell in a reasonable range
        for ($row = 1; $row <= min(50, $totalRows); $row++) {
            for ($col = 'A'; $col <= 'Z'; $col++) {
                $value = $worksheet->getCell($col . $row)->getCalculatedValue();
                if ($value !== null && trim($value) !== '') {
                    if ($firstDataRow === null) {
                        $firstDataRow = $row;
                        $firstDataCol = $col;
                        echo "📍 FIRST DATA FOUND at {$col}{$row}: '{$value}'\n";
                    }
                    $actualDataFound = true;
                    
                    // Show context around first data
                    if ($row <= $firstDataRow + 5 && $row >= $firstDataRow) {
                        echo "  {$col}{$row}: '{$value}'\n";
                    }
                    
                    if ($row > $firstDataRow + 5) break 2;
                }
            }
        }
        
        if (!$actualDataFound) {
            echo "❌ NO ACTUAL DATA FOUND - FILE MAY BE CORRUPTED OR FORMATTED INCORRECTLY\n";
            
            // Final attempt - check file metadata
            echo "\n🔍 FILE METADATA CHECK\n";
            echo "=====================\n";
            
            $properties = $spreadsheet->getProperties();
            echo "Creator: " . $properties->getCreator() . "\n";
            echo "Last modified by: " . $properties->getLastModifiedBy() . "\n";
            echo "Created: " . $properties->getCreated() . "\n";
            echo "Modified: " . $properties->getModified() . "\n";
            echo "Title: " . $properties->getTitle() . "\n";
            echo "Subject: " . $properties->getSubject() . "\n";
            echo "Description: " . $properties->getDescription() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error analyzing file: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

// Execute deep analysis
echo "🚀 Starting DEEP sales data analysis...\n\n";
deepAnalyzeSalesData();
echo "\n✅ Deep analysis complete!\n";

?>