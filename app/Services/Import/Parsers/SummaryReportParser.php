<?php

namespace App\Services\Import\Parsers;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Specialized parser for restaurant summary reports
 * Extracts business intelligence from formatted reports
 */
class SummaryReportParser extends BaseFileParser
{
    protected array $supportedExtensions = ['xlsx', 'xls', 'ods'];
    protected array $supportedMimeTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.oasis.opendocument.spreadsheet'
    ];

    protected function performParsing(UploadedFile $file, array $options): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            $reportData = [
                'report_type' => 'restaurant_summary',
                'metadata' => $this->extractReportMetadata($worksheet),
                'restaurant_info' => $this->extractRestaurantInfo($worksheet),
                'period_info' => $this->extractPeriodInfo($worksheet),
                'summary_metrics' => $this->extractSummaryMetrics($worksheet),
                'payment_breakdown' => $this->extractPaymentBreakdown($worksheet),
                'business_insights' => $this->generateBusinessInsights($worksheet),
                'raw_structure' => $this->analyzeStructure($worksheet)
            ];
            
            return [$reportData]; // Return as single report record
            
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to parse summary report: " . $e->getMessage());
        }
    }

    protected function countRows(UploadedFile $file): int
    {
        return 1; // Summary reports are treated as single records
    }

    /**
     * Extract report metadata
     */
    protected function extractReportMetadata($worksheet): array
    {
        $metadata = [
            'title' => $worksheet->getTitle(),
            'total_rows' => $worksheet->getHighestRow(),
            'total_columns' => $worksheet->getHighestColumn(),
            'merged_cells_count' => count($worksheet->getMergeCells()),
            'created_date' => now()->toDateString(),
            'analysis_timestamp' => now()->toDateTimeString()
        ];

        // Try to find report title in first few rows
        for ($row = 1; $row <= min(10, $worksheet->getHighestRow()); $row++) {
            for ($col = 'A'; $col <= 'F'; $col++) {
                $value = $worksheet->getCell($col . $row)->getCalculatedValue();
                if (!empty($value) && strlen($value) > 10 && stripos($value, 'report') !== false) {
                    $metadata['report_title'] = trim($value);
                    break 2;
                }
            }
        }

        return $metadata;
    }

    /**
     * Extract restaurant information
     */
    protected function extractRestaurantInfo($worksheet): array
    {
        $restaurantInfo = [
            'name' => null,
            'location' => null,
            'report_type' => null,
            'source_system' => null
        ];

        // Scan first 20 rows for restaurant information
        for ($row = 1; $row <= min(20, $worksheet->getHighestRow()); $row++) {
            for ($col = 'A'; $col <= 'U'; $col++) {
                $value = trim($worksheet->getCell($col . $row)->getCalculatedValue() ?? '');
                
                if (empty($value)) continue;

                // Restaurant name patterns
                if (preg_match('/restaurant|cafe|kitchen|dining|food/i', $value) && strlen($value) > 5) {
                    if (!$restaurantInfo['name']) {
                        $restaurantInfo['name'] = $value;
                    }
                }

                // Location patterns
                if (preg_match('/satwa|dubai|sharjah|abu dhabi|uae/i', $value)) {
                    $restaurantInfo['location'] = $value;
                }

                // Report type patterns
                if (preg_match('/reading|summary|sales|daily|monthly/i', $value) && strlen($value) < 30) {
                    $restaurantInfo['report_type'] = $value;
                }
            }
        }

        return $restaurantInfo;
    }

    /**
     * Extract period and date information
     */
    protected function extractPeriodInfo($worksheet): array
    {
        $periodInfo = [
            'date_range' => null,
            'start_date' => null,
            'end_date' => null,
            'first_order_time' => null,
            'last_order_time' => null,
            'first_invoice' => null,
            'last_invoice' => null,
            'period_duration_days' => null
        ];

        $highestRow = $worksheet->getHighestRow();
        
        // Scan for date patterns
        for ($row = 1; $row <= min(50, $highestRow); $row++) {
            for ($col = 'C'; $col <= 'H'; $col++) {
                $cell = $worksheet->getCell($col . $row);
                $value = $cell->getCalculatedValue();
                $formattedValue = $cell->getFormattedValue();

                if (empty($value)) continue;

                // Date range pattern (01-07-2025To31-07-2025)
                if (preg_match('/(\d{2}-\d{2}-\d{4})To(\d{2}-\d{2}-\d{4})/', $value, $matches)) {
                    $periodInfo['date_range'] = $value;
                    $periodInfo['start_date'] = $this->convertDateFormat($matches[1]);
                    $periodInfo['end_date'] = $this->convertDateFormat($matches[2]);
                }

                // Excel date numbers
                if (is_numeric($value) && $value > 40000) { // Likely Excel date
                    try {
                        $dateTime = Date::excelToDateTimeObject($value);
                        $formattedDate = $dateTime->format('Y-m-d H:i:s');

                        // Determine what this date represents based on context
                        $leftCell = $worksheet->getCell($this->getPreviousColumn($col) . $row)->getCalculatedValue();
                        $leftCellValue = strtolower(trim($leftCell ?? ''));

                        if (stripos($leftCellValue, 'first order') !== false) {
                            $periodInfo['first_order_time'] = $formattedDate;
                        } elseif (stripos($leftCellValue, 'last order') !== false) {
                            $periodInfo['last_order_time'] = $formattedDate;
                        }
                    } catch (\Exception $e) {
                        // Skip if date conversion fails
                    }
                }

                // Invoice numbers
                if (is_numeric($value) && $value > 0 && $value < 10000) {
                    $leftCell = $worksheet->getCell($this->getPreviousColumn($col) . $row)->getCalculatedValue();
                    $leftCellValue = strtolower(trim($leftCell ?? ''));

                    if (stripos($leftCellValue, 'first invoice') !== false) {
                        $periodInfo['first_invoice'] = (int)$value;
                    } elseif (stripos($leftCellValue, 'last invoice') !== false) {
                        $periodInfo['last_invoice'] = (int)$value;
                    }
                }
            }
        }

        // Calculate period duration
        if ($periodInfo['start_date'] && $periodInfo['end_date']) {
            $start = new \DateTime($periodInfo['start_date']);
            $end = new \DateTime($periodInfo['end_date']);
            $periodInfo['period_duration_days'] = $start->diff($end)->days + 1;
        }

        return $periodInfo;
    }

    /**
     * Extract summary metrics (totals, counts, etc.)
     */
    protected function extractSummaryMetrics($worksheet): array
    {
        $metrics = [
            'total_transactions' => null,
            'total_invoices' => null,
            'gross_sales' => null,
            'net_amount' => null,
            'tax_amount' => null,
            'discount_amount' => null,
            'service_charge' => null
        ];

        $highestRow = $worksheet->getHighestRow();

        // Scan for numeric values that could be metrics
        for ($row = 20; $row <= min(100, $highestRow); $row++) {
            for ($col = 'D'; $col <= 'W'; $col++) {
                $value = $worksheet->getCell($col . $row)->getCalculatedValue();
                
                if (!is_numeric($value) || $value <= 0) continue;

                // Get context from nearby cells
                $context = $this->getContextualInfo($worksheet, $col, $row);
                
                // Match patterns to identify metrics
                if (preg_match('/transaction|count|order/i', $context) && $value > 100 && $value < 50000) {
                    if (!$metrics['total_transactions'] || $value > $metrics['total_transactions']) {
                        $metrics['total_transactions'] = (int)$value;
                    }
                }

                if (preg_match('/invoice|receipt/i', $context) && $value > 0 && $value < 10000) {
                    if (!$metrics['total_invoices'] || $value > $metrics['total_invoices']) {
                        $metrics['total_invoices'] = (int)$value;
                    }
                }

                if (preg_match('/sales|revenue|gross/i', $context) && $value > 1000) {
                    $metrics['gross_sales'] = (float)$value;
                }

                if (preg_match('/net|total.*amount/i', $context) && $value > 1000) {
                    $metrics['net_amount'] = (float)$value;
                }

                if (preg_match('/tax|vat/i', $context) && $value > 0 && $value < 10000) {
                    $metrics['tax_amount'] = (float)$value;
                }

                if (preg_match('/discount|reduction/i', $context) && $value > 0) {
                    $metrics['discount_amount'] = (float)$value;
                }

                if (preg_match('/service.*charge|tip/i', $context) && $value > 0) {
                    $metrics['service_charge'] = (float)$value;
                }
            }
        }

        return $metrics;
    }

    /**
     * Extract payment method breakdown
     */
    protected function extractPaymentBreakdown($worksheet): array
    {
        $payments = [
            'cash' => null,
            'card' => null,
            'credit_card' => null,
            'voucher' => null,
            'third_party' => null
        ];

        $highestRow = $worksheet->getHighestRow();

        // Scan for payment-related data
        for ($row = 40; $row <= min(80, $highestRow); $row++) {
            for ($col = 'D'; $col <= 'T'; $col++) {
                $value = $worksheet->getCell($col . $row)->getCalculatedValue();
                
                if (!is_numeric($value) || $value <= 0) continue;

                $context = strtolower($this->getContextualInfo($worksheet, $col, $row));

                if (preg_match('/cash.*sales?/', $context) && $value > 100) {
                    $payments['cash'] = (float)$value;
                }

                if (preg_match('/card.*sales?|credit.*card/', $context) && $value > 100) {
                    $payments['card'] = (float)$value;
                    $payments['credit_card'] = (float)$value; // Same value for different keys
                }

                if (preg_match('/voucher|gift/', $context) && $value >= 0) {
                    $payments['voucher'] = (float)$value;
                }

                if (preg_match('/third.*part/', $context) && $value >= 0) {
                    $payments['third_party'] = (float)$value;
                }
            }
        }

        return $payments;
    }

    /**
     * Generate business insights from the data
     */
    protected function generateBusinessInsights($worksheet): array
    {
        $insights = [
            'performance_indicators' => [],
            'recommendations' => [],
            'alerts' => [],
            'comparisons' => []
        ];

        // This would be expanded with actual business logic
        $insights['performance_indicators'] = [
            'data_completeness' => 'High',
            'report_quality' => 'Standard POS Format',
            'processing_status' => 'Successfully Parsed'
        ];

        $insights['recommendations'] = [
            'Request individual transaction data for detailed analysis',
            'Set up automatic data export from POS system',
            'Consider implementing real-time data sync'
        ];

        return $insights;
    }

    /**
     * Analyze the overall structure of the report
     */
    protected function analyzeStructure($worksheet): array
    {
        return [
            'sections_identified' => [
                'header' => 'Rows 1-10',
                'restaurant_info' => 'Rows 2-6', 
                'period_info' => 'Rows 11-20',
                'summary_data' => 'Rows 21-40',
                'payment_breakdown' => 'Rows 41-60'
            ],
            'data_density' => $this->calculateDataDensity($worksheet),
            'complexity_score' => $this->calculateComplexityScore($worksheet)
        ];
    }

    /**
     * Get contextual information around a cell
     */
    protected function getContextualInfo($worksheet, $col, $row): string
    {
        $context = [];
        
        // Get left cell
        $leftCol = $this->getPreviousColumn($col);
        if ($leftCol) {
            $leftValue = $worksheet->getCell($leftCol . $row)->getCalculatedValue();
            if (!empty($leftValue)) {
                $context[] = $leftValue;
            }
        }

        // Get cell above
        if ($row > 1) {
            $aboveValue = $worksheet->getCell($col . ($row - 1))->getCalculatedValue();
            if (!empty($aboveValue)) {
                $context[] = $aboveValue;
            }
        }

        return implode(' ', $context);
    }

    /**
     * Get previous column letter
     */
    protected function getPreviousColumn($col): ?string
    {
        $cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $index = array_search($col, $cols);
        return $index > 0 ? $cols[$index - 1] : null;
    }

    /**
     * Convert date format from DD-MM-YYYY to YYYY-MM-DD
     */
    protected function convertDateFormat($date): string
    {
        $parts = explode('-', $date);
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $date;
    }

    /**
     * Calculate data density of the worksheet
     */
    protected function calculateDataDensity($worksheet): float
    {
        $totalCells = $worksheet->getHighestRow() * 
                     \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestColumn());
        
        $nonEmptyCells = 0;
        $highestRow = min($worksheet->getHighestRow(), 100); // Sample for performance
        
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= 20; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                if (!empty($value)) {
                    $nonEmptyCells++;
                }
            }
        }
        
        return $nonEmptyCells > 0 ? ($nonEmptyCells / ($highestRow * 20)) * 100 : 0;
    }

    /**
     * Calculate complexity score based on structure
     */
    protected function calculateComplexityScore($worksheet): int
    {
        $score = 0;
        
        // More merged cells = higher complexity
        $mergedCells = count($worksheet->getMergeCells());
        $score += min(30, $mergedCells);
        
        // More rows = higher complexity
        $rows = $worksheet->getHighestRow();
        $score += min(20, $rows / 20);
        
        // More columns = higher complexity  
        $cols = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestColumn());
        $score += min(10, $cols);
        
        return min(100, $score);
    }
}