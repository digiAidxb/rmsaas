<?php

require_once 'vendor/autoload.php';

use App\Services\Import\SmartFormatDetector;
use App\Services\Import\Parsers\ExcelParser;
use App\Services\Import\Parsers\CsvParser;
use App\Services\Import\Parsers\SummaryReportParser;
use Illuminate\Http\UploadedFile;

/**
 * Test script to demonstrate smart format detection capabilities
 * Tests both summary reports and transaction data formats
 */

echo "🧠 TESTING SMART FORMAT DETECTION SYSTEM\n";
echo "========================================\n\n";

// Initialize services
$excelParser = new ExcelParser();
$csvParser = new CsvParser();
$detector = new SmartFormatDetector($excelParser, $csvParser);
$summaryParser = new SummaryReportParser();

// Test files to analyze
$testFiles = [
    'rawdata/data 01july to 01 aug.xlsx' => 'Restaurant Summary Report',
    'rawdata/enhanced_recipes_master.xlsx' => 'Enhanced Recipes (Transaction-like)',
    'rawdata/enhanced_inventory_master.xlsx' => 'Enhanced Inventory (Transaction-like)'
];

foreach ($testFiles as $filePath => $description) {
    echo "📁 ANALYZING: {$description}\n";
    echo "File: {$filePath}\n";
    echo str_repeat('=', 60) . "\n";
    
    if (!file_exists($filePath)) {
        echo "❌ File not found: {$filePath}\n\n";
        continue;
    }
    
    try {
        // Create a mock UploadedFile for testing
        $uploadedFile = createMockUploadedFile($filePath);
        
        // Run smart format detection
        $analysis = $detector->analyzeFile($uploadedFile);
        
        // Display results
        displayAnalysisResults($analysis);
        
        // Test appropriate parser based on detection
        if ($analysis['format_type'] === 'summary_report' && $analysis['confidence'] > 70) {
            echo "\n🎯 TESTING SUMMARY REPORT PARSER:\n";
            echo "================================\n";
            
            $summaryData = $summaryParser->parse($uploadedFile);
            displaySummaryResults($summaryData);
        } elseif ($analysis['format_type'] === 'transaction_data' && $analysis['confidence'] > 70) {
            echo "\n🎯 TESTING TRANSACTION DATA PARSER:\n";
            echo "==================================\n";
            
            $transactionSample = $excelParser->parse($uploadedFile, ['limit' => 5]);
            displayTransactionResults($transactionSample);
        }
        
    } catch (Exception $e) {
        echo "❌ Error analyzing file: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat('-', 80) . "\n\n";
}

// Summary of capabilities
echo "🎉 SMART FORMAT DETECTION CAPABILITIES SUMMARY\n";
echo "=============================================\n";
echo "✅ Automatic format type detection (Transaction vs Summary)\n";
echo "✅ Confidence scoring for import strategy decisions\n";
echo "✅ Detailed pattern analysis and scoring\n";
echo "✅ Intelligent recommendations based on file structure\n";
echo "✅ Support for both Excel and CSV formats\n";
echo "✅ Specialized parsing for restaurant summary reports\n";
echo "✅ Business intelligence extraction from complex reports\n";
echo "✅ Contextual field mapping suggestions\n\n";

echo "🔧 INTEGRATION RECOMMENDATIONS:\n";
echo "==============================\n";
echo "1. Use SmartFormatDetector before any import operation\n";
echo "2. Route files to appropriate parsers based on detection results\n";
echo "3. Show confidence scores and recommendations to users\n";
echo "4. Implement fallback strategies for low-confidence detections\n";
echo "5. Log detection patterns for continuous system improvement\n\n";

function createMockUploadedFile($filePath) {
    // Create a temporary copy to simulate uploaded file
    $tempPath = tempnam(sys_get_temp_dir(), 'test_import_');
    copy($filePath, $tempPath);
    
    $pathInfo = pathinfo($filePath);
    
    return new UploadedFile(
        $tempPath,
        $pathInfo['basename'],
        mime_content_type($filePath),
        null,
        true // test mode
    );
}

function displayAnalysisResults($analysis) {
    echo "📊 DETECTION RESULTS:\n";
    echo "====================\n";
    echo "Format Type: " . strtoupper($analysis['format_type']) . "\n";
    echo "Confidence: " . $analysis['confidence'] . "%\n";
    echo "Import Strategy: " . $analysis['import_strategy'] . "\n";
    
    if (!empty($analysis['detected_patterns'])) {
        echo "\n🔍 PATTERN ANALYSIS:\n";
        foreach ($analysis['detected_patterns'] as $key => $value) {
            if (is_array($value)) {
                echo "  {$key}:\n";
                foreach ($value as $subKey => $subValue) {
                    echo "    {$subKey}: {$subValue}\n";
                }
            } else {
                echo "  {$key}: {$value}\n";
            }
        }
    }
    
    echo "\n💡 RECOMMENDATIONS:\n";
    foreach ($analysis['recommendations'] as $recommendation) {
        echo "  • {$recommendation}\n";
    }
    
    if (!empty($analysis['warnings'])) {
        echo "\n⚠️ WARNINGS:\n";
        foreach ($analysis['warnings'] as $warning) {
            echo "  • {$warning}\n";
        }
    }
    
    echo "\n📋 FILE INFO:\n";
    echo "  Name: " . $analysis['file_info']['name'] . "\n";
    echo "  Size: " . $analysis['file_info']['size_formatted'] . "\n";
    echo "  Type: " . $analysis['file_info']['mime_type'] . "\n";
}

function displaySummaryResults($summaryData) {
    if (empty($summaryData)) {
        echo "No summary data extracted.\n";
        return;
    }
    
    $report = $summaryData[0]; // First (and only) report
    
    echo "🏪 RESTAURANT INFO:\n";
    if (isset($report['restaurant_info'])) {
        foreach ($report['restaurant_info'] as $key => $value) {
            if ($value) {
                echo "  " . ucwords(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
        }
    }
    
    echo "\n📅 PERIOD INFO:\n";
    if (isset($report['period_info'])) {
        foreach ($report['period_info'] as $key => $value) {
            if ($value) {
                echo "  " . ucwords(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
        }
    }
    
    echo "\n📈 SUMMARY METRICS:\n";
    if (isset($report['summary_metrics'])) {
        foreach ($report['summary_metrics'] as $key => $value) {
            if ($value !== null) {
                $formatted = is_numeric($value) ? number_format($value, 2) : $value;
                echo "  " . ucwords(str_replace('_', ' ', $key)) . ": {$formatted}\n";
            }
        }
    }
    
    echo "\n💳 PAYMENT BREAKDOWN:\n";
    if (isset($report['payment_breakdown'])) {
        foreach ($report['payment_breakdown'] as $method => $amount) {
            if ($amount !== null) {
                echo "  " . ucwords($method) . ": AED " . number_format($amount, 2) . "\n";
            }
        }
    }
}

function displayTransactionResults($transactionData) {
    if (empty($transactionData)) {
        echo "No transaction data extracted.\n";
        return;
    }
    
    echo "📊 SAMPLE TRANSACTION DATA:\n";
    echo "Total records: " . count($transactionData) . "\n\n";
    
    // Show first few records
    foreach (array_slice($transactionData, 0, 3) as $i => $record) {
        echo "Record " . ($i + 1) . ":\n";
        foreach ($record as $field => $value) {
            if (!empty($value)) {
                echo "  {$field}: {$value}\n";
            }
        }
        echo "\n";
    }
    
    if (count($transactionData) > 3) {
        echo "... and " . (count($transactionData) - 3) . " more records\n";
    }
}

?>