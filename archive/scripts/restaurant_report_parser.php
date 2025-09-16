<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Specialized parser for restaurant report format
 * This handles the GHORKA RESTAURANT report structure with merged cells
 */
function parseRestaurantReport() {
    $filePath = 'rawdata/data 01july to 01 aug.xlsx';
    
    if (!file_exists($filePath)) {
        echo "❌ Sales data file not found: {$filePath}\n";
        return;
    }
    
    echo "🍽️ PARSING RESTAURANT REPORT: {$filePath}\n";
    echo "============================================\n\n";
    
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Extract restaurant metadata
        $restaurantInfo = [
            'name' => trim($worksheet->getCell('C2')->getCalculatedValue() ?? ''),
            'location' => trim($worksheet->getCell('C3')->getCalculatedValue() ?? ''),
            'report_type' => trim($worksheet->getCell('C5')->getCalculatedValue() ?? ''),
            'date_range' => trim($worksheet->getCell('C6')->getCalculatedValue() ?? ''),
            'first_order' => $worksheet->getCell('H11')->getCalculatedValue(),
            'last_order' => $worksheet->getCell('H14')->getCalculatedValue(),
            'first_invoice' => $worksheet->getCell('H17')->getCalculatedValue(),
            'last_invoice' => $worksheet->getCell('H20')->getCalculatedValue()
        ];
        
        echo "🏪 RESTAURANT INFORMATION\n";
        echo "========================\n";
        echo "Name: {$restaurantInfo['name']}\n";
        echo "Location: {$restaurantInfo['location']}\n";
        echo "Report Type: {$restaurantInfo['report_type']}\n";
        echo "Date Range: {$restaurantInfo['date_range']}\n";
        
        // Convert Excel dates to readable format
        if (is_numeric($restaurantInfo['first_order'])) {
            $firstOrderDate = Date::excelToDateTimeObject($restaurantInfo['first_order']);
            echo "First Order: " . $firstOrderDate->format('Y-m-d H:i:s') . "\n";
        }
        
        if (is_numeric($restaurantInfo['last_order'])) {
            $lastOrderDate = Date::excelToDateTimeObject($restaurantInfo['last_order']);
            echo "Last Order: " . $lastOrderDate->format('Y-m-d H:i:s') . "\n";
        }
        
        echo "Invoice Range: {$restaurantInfo['first_invoice']} to {$restaurantInfo['last_invoice']}\n\n";
        
        // Now let's find where the actual transaction data starts
        echo "🔍 SCANNING FOR TRANSACTION DATA\n";
        echo "===============================\n";
        
        $dataStartRow = null;
        $headers = [];
        $transactions = [];
        
        // Look for data starting around row 40+ where the report structure usually puts tables
        for ($row = 40; $row <= $worksheet->getHighestRow(); $row++) {
            $rowData = [];
            $hasData = false;
            
            // Check columns D through W for data
            for ($col = 'D'; $col <= 'W'; $col++) {
                $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                if ($cellValue !== null && trim($cellValue) !== '') {
                    $rowData[$col] = trim($cellValue);
                    $hasData = true;
                }
            }
            
            if ($hasData) {
                if ($dataStartRow === null) {
                    $dataStartRow = $row;
                    echo "📍 First data found at row: {$row}\n";
                    
                    // Check if this looks like headers
                    $potentialHeaders = array_values($rowData);
                    $headerIndicators = ['item', 'name', 'product', 'menu', 'quantity', 'price', 'total', 'date', 'time', 'invoice', 'order'];
                    
                    $headerScore = 0;
                    foreach ($potentialHeaders as $header) {
                        $headerLower = strtolower($header);
                        foreach ($headerIndicators as $indicator) {
                            if (strpos($headerLower, $indicator) !== false) {
                                $headerScore++;
                                break;
                            }
                        }
                    }
                    
                    if ($headerScore >= 2) {
                        echo "✅ Row {$row} appears to be headers (score: {$headerScore})\n";
                        $headers = $rowData;
                        continue;
                    }
                }
                
                // If we have headers, this is transaction data
                if (!empty($headers) && $row > $dataStartRow) {
                    $transaction = [];
                    foreach ($headers as $colKey => $headerName) {
                        $transaction[$headerName] = $rowData[$colKey] ?? '';
                    }
                    $transactions[] = $transaction;
                }
                
                // Show first few rows for analysis
                if ($row <= $dataStartRow + 10) {
                    echo "Row {$row}: " . json_encode($rowData) . "\n";
                }
            }
        }
        
        if (!empty($headers)) {
            echo "\n📊 DETECTED HEADERS:\n";
            echo "==================\n";
            foreach ($headers as $col => $header) {
                echo "Column {$col}: '{$header}'\n";
            }
            
            echo "\n📈 TRANSACTION DATA SUMMARY:\n";
            echo "===========================\n";
            echo "Total transactions found: " . count($transactions) . "\n";
            
            if (!empty($transactions)) {
                echo "\n🔍 SAMPLE TRANSACTIONS (First 5):\n";
                echo "=================================\n";
                foreach (array_slice($transactions, 0, 5) as $i => $transaction) {
                    echo "Transaction " . ($i + 1) . ":\n";
                    foreach ($transaction as $field => $value) {
                        echo "  {$field}: {$value}\n";
                    }
                    echo "\n";
                }
                
                // Generate mapping for import system
                echo "🔧 IMPORT MAPPING SUGGESTIONS:\n";
                echo "==============================\n";
                generateMappingForRestaurantReport($headers);
            }
        } else {
            echo "\n❌ No clear transaction data headers found\n";
            echo "This appears to be a summary report rather than raw transaction data\n";
            
            // Let's check what type of data we actually have
            echo "\n📊 REPORT STRUCTURE ANALYSIS:\n";
            echo "============================\n";
            
            $sections = [];
            for ($row = 20; $row <= min(100, $worksheet->getHighestRow()); $row++) {
                $rowData = [];
                for ($col = 'D'; $col <= 'W'; $col++) {
                    $value = $worksheet->getCell($col . $row)->getCalculatedValue();
                    if ($value !== null && trim($value) !== '') {
                        $rowData[] = trim($value);
                    }
                }
                
                if (!empty($rowData)) {
                    $sections[$row] = $rowData;
                }
            }
            
            // Show structure
            foreach (array_slice($sections, 0, 20, true) as $row => $data) {
                echo "Row {$row}: " . implode(' | ', $data) . "\n";
            }
        }
        
        return [
            'restaurant_info' => $restaurantInfo,
            'headers' => $headers,
            'transactions' => $transactions,
            'data_start_row' => $dataStartRow
        ];
        
    } catch (Exception $e) {
        echo "❌ Error parsing restaurant report: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

function generateMappingForRestaurantReport($headers) {
    $standardFields = [
        'transaction_id' => ['invoice', 'receipt', 'order', 'id', 'number'],
        'date' => ['date', 'time', 'timestamp'],
        'item_name' => ['item', 'product', 'menu', 'name', 'description'],
        'quantity' => ['quantity', 'qty', 'amount', 'units'],
        'unit_price' => ['price', 'cost', 'rate', 'unit'],
        'total_amount' => ['total', 'amount', 'sum', 'value'],
        'category' => ['category', 'group', 'type'],
        'server' => ['server', 'employee', 'staff'],
        'table' => ['table', 'seat', 'location']
    ];
    
    $mappings = [];
    
    foreach ($headers as $col => $header) {
        $headerLower = strtolower(trim($header));
        $bestMatch = null;
        $bestConfidence = 0;
        
        foreach ($standardFields as $field => $keywords) {
            foreach ($keywords as $keyword) {
                $confidence = 0;
                
                if ($headerLower === $keyword) {
                    $confidence = 100;
                } elseif (strpos($headerLower, $keyword) !== false) {
                    $confidence = 80;
                } elseif (strpos($keyword, $headerLower) !== false) {
                    $confidence = 60;
                }
                
                if ($confidence > $bestConfidence) {
                    $bestMatch = $field;
                    $bestConfidence = $confidence;
                }
            }
        }
        
        $mappings[] = [
            'source_column' => $col,
            'source_field' => $header,
            'target_field' => $bestMatch ?? 'custom_field',
            'confidence' => $bestConfidence
        ];
        
        $status = $bestConfidence >= 80 ? '✅' : ($bestConfidence >= 60 ? '⚠️' : '❓');
        echo "{$status} {$header} → {$bestMatch} ({$bestConfidence}%)\n";
    }
    
    echo "\n📝 MAPPING JSON:\n";
    echo json_encode($mappings, JSON_PRETTY_PRINT);
    
    return $mappings;
}

// Execute parsing
echo "🚀 Starting restaurant report parsing...\n\n";
$result = parseRestaurantReport();
echo "\n✅ Parsing complete!\n";

?>