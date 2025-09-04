<?php

namespace App\Services\Import;

use Illuminate\Http\UploadedFile;
use App\Services\Import\Parsers\ExcelParser;
use App\Services\Import\Parsers\CsvParser;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Smart format detection service that automatically identifies
 * whether data is individual transaction records or summary reports
 * and routes to appropriate import handlers
 */
class SmartFormatDetector
{
    protected ExcelParser $excelParser;
    protected CsvParser $csvParser;

    public function __construct(ExcelParser $excelParser, CsvParser $csvParser)
    {
        $this->excelParser = $excelParser;
        $this->csvParser = $csvParser;
    }

    /**
     * Analyze file and detect format type
     */
    public function analyzeFile(UploadedFile $file): array
    {
        $analysis = [
            'file_info' => $this->getFileInfo($file),
            'format_type' => 'unknown',
            'confidence' => 0,
            'data_structure' => [],
            'import_strategy' => 'manual_review',
            'detected_patterns' => [],
            'warnings' => [],
            'recommendations' => []
        ];

        try {
            // Determine parser based on file extension
            $parser = $this->getParser($file);
            if (!$parser) {
                $analysis['warnings'][] = 'Unsupported file format';
                return $analysis;
            }

            // Get file structure analysis
            $structure = $parser->analyzeStructure($file);
            $analysis['data_structure'] = $structure;

            // Detect format type
            $formatDetection = $this->detectFormatType($file, $parser);
            $analysis['format_type'] = $formatDetection['type'];
            $analysis['confidence'] = $formatDetection['confidence'];
            $analysis['detected_patterns'] = $formatDetection['patterns'];

            // Determine import strategy
            $analysis['import_strategy'] = $this->determineImportStrategy($formatDetection);
            
            // Generate recommendations
            $analysis['recommendations'] = $this->generateRecommendations($formatDetection, $structure);

        } catch (\Exception $e) {
            $analysis['warnings'][] = "Analysis failed: " . $e->getMessage();
        }

        return $analysis;
    }

    /**
     * Detect whether file contains transaction data or summary report
     */
    protected function detectFormatType(UploadedFile $file, $parser): array
    {
        $detection = [
            'type' => 'unknown',
            'confidence' => 0,
            'patterns' => [],
            'indicators' => []
        ];

        try {
            if ($parser instanceof ExcelParser) {
                return $this->detectExcelFormat($file, $parser);
            } elseif ($parser instanceof CsvParser) {
                return $this->detectCsvFormat($file, $parser);
            }
        } catch (\Exception $e) {
            $detection['indicators'][] = "Detection error: " . $e->getMessage();
        }

        return $detection;
    }

    /**
     * Detect Excel file format (transaction vs summary)
     */
    protected function detectExcelFormat(UploadedFile $file, ExcelParser $parser): array
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        
        $detection = [
            'type' => 'unknown',
            'confidence' => 0,
            'patterns' => [],
            'indicators' => []
        ];

        // Get basic file metrics
        $totalRows = $worksheet->getHighestRow();
        $totalCols = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestColumn());
        $mergedCells = count($worksheet->getMergeCells());

        $detection['indicators'][] = "Rows: {$totalRows}, Columns: {$totalCols}, Merged cells: {$mergedCells}";

        // Check for transaction data patterns
        $transactionScore = $this->scoreTransactionPatterns($worksheet, $totalRows, $totalCols);
        $summaryScore = $this->scoreSummaryPatterns($worksheet, $totalRows, $totalCols, $mergedCells);

        $detection['patterns']['transaction_score'] = $transactionScore;
        $detection['patterns']['summary_score'] = $summaryScore;

        // Determine format type based on scores
        if ($transactionScore['total'] > $summaryScore['total'] && $transactionScore['total'] > 60) {
            $detection['type'] = 'transaction_data';
            $detection['confidence'] = min(95, $transactionScore['total']);
            $detection['indicators'][] = "Strong transaction data indicators found";
        } elseif ($summaryScore['total'] > $transactionScore['total'] && $summaryScore['total'] > 60) {
            $detection['type'] = 'summary_report';
            $detection['confidence'] = min(95, $summaryScore['total']);
            $detection['indicators'][] = "Strong summary report indicators found";
        } elseif ($transactionScore['total'] > 40 || $summaryScore['total'] > 40) {
            $detection['type'] = $transactionScore['total'] > $summaryScore['total'] ? 'transaction_data' : 'summary_report';
            $detection['confidence'] = max($transactionScore['total'], $summaryScore['total']);
            $detection['indicators'][] = "Moderate confidence in format detection";
        } else {
            $detection['type'] = 'hybrid_or_complex';
            $detection['confidence'] = 30;
            $detection['indicators'][] = "Mixed or complex format detected";
        }

        return $detection;
    }

    /**
     * Score transaction data patterns
     */
    protected function scoreTransactionPatterns($worksheet, $totalRows, $totalCols): array
    {
        $score = [
            'headers' => 0,
            'data_consistency' => 0,
            'field_types' => 0,
            'row_uniformity' => 0,
            'total' => 0
        ];

        // Check for clear headers in row 1
        $headers = [];
        $headerScore = 0;
        
        for ($col = 1; $col <= min($totalCols, 20); $col++) {
            $headerValue = $worksheet->getCellByColumnAndRow($col, 1)->getCalculatedValue();
            if (!empty(trim($headerValue))) {
                $headers[] = strtolower(trim($headerValue));
            }
        }

        // Score header quality
        $transactionHeaders = [
            'id', 'transaction', 'order', 'receipt', 'invoice',
            'date', 'time', 'timestamp', 
            'item', 'product', 'name', 'description',
            'quantity', 'qty', 'amount', 'count',
            'price', 'cost', 'total', 'value',
            'payment', 'method', 'customer', 'table', 'server'
        ];

        $matchingHeaders = 0;
        foreach ($headers as $header) {
            foreach ($transactionHeaders as $txnHeader) {
                if (strpos($header, $txnHeader) !== false) {
                    $matchingHeaders++;
                    break;
                }
            }
        }

        if (count($headers) > 0) {
            $score['headers'] = min(30, ($matchingHeaders / count($headers)) * 30);
        }

        // Check data consistency (sample first 20 rows)
        $sampleRows = min($totalRows, 20);
        $consistentRows = 0;
        $nonEmptyRows = 0;

        for ($row = 2; $row <= $sampleRows; $row++) {
            $rowData = [];
            $nonEmptyCount = 0;
            
            for ($col = 1; $col <= min($totalCols, 15); $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                if (!empty($value)) {
                    $nonEmptyCount++;
                }
                $rowData[] = $value;
            }
            
            if ($nonEmptyCount > 0) {
                $nonEmptyRows++;
                
                // Check if row has consistent structure (similar number of fields)
                if ($nonEmptyCount >= count($headers) * 0.5) {
                    $consistentRows++;
                }
            }
        }

        if ($nonEmptyRows > 0) {
            $score['data_consistency'] = min(25, ($consistentRows / $nonEmptyRows) * 25);
        }

        // Check for appropriate field types in data
        $fieldTypeScore = $this->analyzeFieldTypes($worksheet, $headers, min($totalRows, 10));
        $score['field_types'] = $fieldTypeScore;

        // Check row uniformity (transaction data should have uniform row structure)
        if ($totalRows > 10 && $mergedCells < ($totalRows * 0.1)) {
            $score['row_uniformity'] = 15;
        }

        $score['total'] = array_sum($score);
        return $score;
    }

    /**
     * Score summary report patterns  
     */
    protected function scoreSummaryPatterns($worksheet, $totalRows, $totalCols, $mergedCells): array
    {
        $score = [
            'merged_cells' => 0,
            'header_structure' => 0,
            'aggregated_data' => 0,
            'report_sections' => 0,
            'total' => 0
        ];

        // High number of merged cells indicates formatting/summary report
        if ($mergedCells > 0) {
            $mergeRatio = $mergedCells / ($totalRows * $totalCols);
            $score['merged_cells'] = min(30, $mergeRatio * 100);
        }

        // Check for report-style headers and sections
        $reportIndicators = 0;
        $summaryKeywords = [
            'restaurant', 'report', 'summary', 'total', 'period',
            'sales', 'revenue', 'payment', 'cash', 'card',
            'first order', 'last order', 'invoice', 'reading'
        ];

        // Check first 20 rows for report indicators
        for ($row = 1; $row <= min($totalRows, 20); $row++) {
            for ($col = 1; $col <= min($totalCols, 10); $col++) {
                $value = strtolower($worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue() ?? '');
                
                foreach ($summaryKeywords as $keyword) {
                    if (strpos($value, $keyword) !== false) {
                        $reportIndicators++;
                        break 2; // Break both loops for this row
                    }
                }
            }
        }

        $score['header_structure'] = min(25, $reportIndicators * 3);

        // Look for aggregated data patterns (totals, percentages, etc.)
        $aggregatePatterns = 0;
        for ($row = 1; $row <= min($totalRows, 30); $row++) {
            for ($col = 1; $col <= min($totalCols, 10); $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                
                // Check for large numbers (likely totals)
                if (is_numeric($value) && $value > 1000) {
                    $aggregatePatterns++;
                }
                
                // Check for percentage patterns
                if (is_string($value) && (strpos($value, '%') !== false || strpos($value, 'total') !== false)) {
                    $aggregatePatterns++;
                }
            }
        }

        $score['aggregated_data'] = min(25, $aggregatePatterns * 2);

        // Check for distinct sections (common in reports)
        $emptySections = 0;
        $lastRowEmpty = false;
        
        for ($row = 1; $row <= $totalRows; $row++) {
            $rowEmpty = true;
            for ($col = 1; $col <= min($totalCols, 5); $col++) {
                if (!empty($worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue())) {
                    $rowEmpty = false;
                    break;
                }
            }
            
            if ($rowEmpty && !$lastRowEmpty) {
                $emptySections++;
            }
            $lastRowEmpty = $rowEmpty;
        }

        if ($emptySections > 2) {
            $score['report_sections'] = 20;
        }

        $score['total'] = array_sum($score);
        return $score;
    }

    /**
     * Analyze field types in sample data
     */
    protected function analyzeFieldTypes($worksheet, $headers, $sampleRows): int
    {
        $score = 0;
        $expectedTypes = [
            'id' => 'string', 'transaction' => 'string', 'order' => 'string',
            'date' => 'date', 'time' => 'time',
            'quantity' => 'numeric', 'price' => 'numeric', 'total' => 'numeric',
            'item' => 'string', 'product' => 'string', 'name' => 'string'
        ];

        foreach ($headers as $colIndex => $header) {
            $typeMatches = 0;
            $totalValues = 0;
            
            for ($row = 2; $row <= $sampleRows; $row++) {
                $value = $worksheet->getCellByColumnAndRow($colIndex + 1, $row)->getCalculatedValue();
                if (empty($value)) continue;
                
                $totalValues++;
                
                // Check if value matches expected type for this header
                foreach ($expectedTypes as $keyword => $expectedType) {
                    if (strpos(strtolower($header), $keyword) !== false) {
                        if ($this->valueMatchesType($value, $expectedType)) {
                            $typeMatches++;
                        }
                        break;
                    }
                }
            }
            
            if ($totalValues > 0 && ($typeMatches / $totalValues) > 0.7) {
                $score += 2;
            }
        }

        return min(20, $score);
    }

    /**
     * Check if value matches expected type
     */
    protected function valueMatchesType($value, $expectedType): bool
    {
        switch ($expectedType) {
            case 'numeric':
                return is_numeric($value);
            case 'date':
                return strtotime($value) !== false || Date::isDateTime(null);
            case 'time':
                return preg_match('/^\d{1,2}:\d{2}/', $value);
            case 'string':
                return is_string($value) && !is_numeric($value);
            default:
                return true;
        }
    }

    /**
     * Detect CSV format
     */
    protected function detectCsvFormat(UploadedFile $file, CsvParser $parser): array
    {
        $detection = [
            'type' => 'transaction_data', // CSV typically contains transaction data
            'confidence' => 75,
            'patterns' => [],
            'indicators' => ['CSV files typically contain structured transaction data']
        ];

        try {
            // Sample first few rows to analyze structure
            $handle = fopen($file->getPathname(), 'r');
            $sampleRows = [];
            $rowCount = 0;
            
            while (($row = fgetcsv($handle)) !== false && $rowCount < 20) {
                $sampleRows[] = $row;
                $rowCount++;
            }
            fclose($handle);

            if (!empty($sampleRows)) {
                $headers = $sampleRows[0] ?? [];
                $headerScore = $this->scoreCsvHeaders($headers);
                $dataScore = $this->scoreCsvData(array_slice($sampleRows, 1));

                $detection['patterns']['header_score'] = $headerScore;
                $detection['patterns']['data_score'] = $dataScore;
                
                $totalScore = $headerScore + $dataScore;
                $detection['confidence'] = min(95, max(60, $totalScore));
                
                if ($totalScore < 40) {
                    $detection['type'] = 'unknown';
                    $detection['indicators'][] = 'CSV structure unclear';
                }
            }

        } catch (\Exception $e) {
            $detection['confidence'] = 50;
            $detection['indicators'][] = "CSV analysis limited: " . $e->getMessage();
        }

        return $detection;
    }

    /**
     * Score CSV headers for transaction data patterns
     */
    protected function scoreCsvHeaders($headers): int
    {
        $transactionHeaders = [
            'id', 'transaction', 'order', 'receipt', 'invoice',
            'date', 'time', 'item', 'product', 'name',
            'quantity', 'price', 'total', 'payment', 'customer'
        ];

        $matches = 0;
        foreach ($headers as $header) {
            $headerLower = strtolower(trim($header));
            foreach ($transactionHeaders as $txnHeader) {
                if (strpos($headerLower, $txnHeader) !== false) {
                    $matches++;
                    break;
                }
            }
        }

        return count($headers) > 0 ? min(40, ($matches / count($headers)) * 40) : 0;
    }

    /**
     * Score CSV data consistency
     */
    protected function scoreCsvData($dataRows): int
    {
        if (empty($dataRows)) return 0;

        $consistentRows = 0;
        $totalRows = count($dataRows);
        $expectedFields = count($dataRows[0] ?? []);

        foreach ($dataRows as $row) {
            $nonEmptyFields = count(array_filter($row));
            if ($nonEmptyFields >= $expectedFields * 0.5) {
                $consistentRows++;
            }
        }

        return min(35, ($consistentRows / $totalRows) * 35);
    }

    /**
     * Determine import strategy based on detection results
     */
    protected function determineImportStrategy($formatDetection): string
    {
        $type = $formatDetection['type'];
        $confidence = $formatDetection['confidence'];

        if ($confidence < 50) {
            return 'manual_review';
        }

        switch ($type) {
            case 'transaction_data':
                return $confidence > 80 ? 'auto_import_transaction' : 'guided_import_transaction';
            
            case 'summary_report':
                return $confidence > 80 ? 'auto_parse_summary' : 'guided_parse_summary';
            
            case 'hybrid_or_complex':
                return 'expert_review';
            
            default:
                return 'manual_review';
        }
    }

    /**
     * Generate recommendations based on analysis
     */
    protected function generateRecommendations($formatDetection, $structure): array
    {
        $recommendations = [];
        $type = $formatDetection['type'];
        $confidence = $formatDetection['confidence'];

        switch ($type) {
            case 'transaction_data':
                if ($confidence > 80) {
                    $recommendations[] = "✅ Ready for automatic import - high confidence transaction data detected";
                    $recommendations[] = "🎯 AI field mapping should work excellently with this format";
                } else {
                    $recommendations[] = "⚠️ Transaction data detected but may need field mapping review";
                    $recommendations[] = "📋 Verify column headers match your system requirements";
                }
                $recommendations[] = "🔍 Expected fields: transaction_id, date, item_name, quantity, price";
                break;

            case 'summary_report':
                $recommendations[] = "📊 Summary report detected - extract business intelligence data";
                $recommendations[] = "💡 Consider requesting individual transaction data for detailed analysis";
                $recommendations[] = "📈 Use summary parser to extract totals, periods, and key metrics";
                if ($confidence < 70) {
                    $recommendations[] = "⚠️ Complex report structure - may need custom parsing rules";
                }
                break;

            case 'hybrid_or_complex':
                $recommendations[] = "🔬 Complex data structure detected";
                $recommendations[] = "👥 Recommend expert review for optimal import strategy";
                $recommendations[] = "🛠️ May require custom import handler";
                break;

            default:
                $recommendations[] = "❓ Unable to determine optimal import method";
                $recommendations[] = "📞 Contact support for assistance with this file format";
                $recommendations[] = "📋 Consider converting to standard CSV format";
        }

        // Add technical recommendations
        if (isset($structure['file_info']['sheet_count']) && $structure['file_info']['sheet_count'] > 1) {
            $recommendations[] = "📑 Multiple sheets detected - specify which sheet to import";
        }

        return $recommendations;
    }

    /**
     * Get appropriate parser for file
     */
    protected function getParser(UploadedFile $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        switch ($extension) {
            case 'xlsx':
            case 'xls':
            case 'ods':
                return $this->excelParser;
            
            case 'csv':
                return $this->csvParser;
            
            default:
                return null;
        }
    }

    /**
     * Get basic file information
     */
    protected function getFileInfo(UploadedFile $file): array
    {
        return [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size_formatted' => $this->formatBytes($file->getSize())
        ];
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}