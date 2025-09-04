<?php

namespace App\Services\Import\Parsers;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelParser extends BaseFileParser
{
    protected array $supportedExtensions = ['xlsx', 'xls', 'ods'];
    protected array $supportedMimeTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/excel',
        'application/vnd.msexcel'
    ];

    protected function performParsing(UploadedFile $file, array $options): array
    {
        $worksheetIndex = $options['worksheet'] ?? 0;
        $hasHeaders = $options['has_headers'] ?? $this->detectHeaders($file);
        $limit = $options['limit'] ?? null;
        $startRow = $options['start_row'] ?? ($hasHeaders ? 2 : 1);
        
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $this->getWorksheet($spreadsheet, $worksheetIndex);
            
            $data = [];
            $headers = [];
            
            // Get headers if present
            if ($hasHeaders) {
                $headers = $this->extractHeaders($worksheet);
            }
            
            // Get highest row and column
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Extract data
            $rowCount = 0;
            for ($row = $startRow; $row <= $highestRow; $row++) {
                if ($limit && $rowCount >= $limit) {
                    break;
                }
                
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    $rowData[] = $this->cleanCellValue($cellValue);
                }
                
                // Skip completely empty rows
                if (!empty(array_filter($rowData))) {
                    // Create associative array if we have headers
                    if (!empty($headers)) {
                        $rowData = $this->combineHeadersWithRow($headers, $rowData);
                    }
                    
                    $data[] = $rowData;
                    $rowCount++;
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to parse Excel file: " . $e->getMessage());
        }
    }

    protected function countRows(UploadedFile $file): int
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            $highestRow = $worksheet->getHighestRow();
            
            // Subtract header row if present
            if ($this->detectHeaders($file)) {
                $highestRow = max(0, $highestRow - 1);
            }
            
            return $highestRow;
            
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to count rows in Excel file: " . $e->getMessage());
        }
    }

    protected function validateSpecificFormat(UploadedFile $file): array
    {
        $errors = [];
        $warnings = [];
        
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            
            // Check if file has worksheets
            if ($spreadsheet->getSheetCount() === 0) {
                $errors[] = 'Excel file contains no worksheets';
                return ['errors' => $errors, 'warnings' => $warnings];
            }
            
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Check if worksheet is empty
            if ($worksheet->getHighestRow() === 1 && $worksheet->getHighestColumn() === 'A') {
                $firstCell = $worksheet->getCell('A1')->getValue();
                if (empty($firstCell)) {
                    $errors[] = 'Excel worksheet appears to be empty';
                }
            }
            
            // Check for very large worksheets
            $rowCount = $worksheet->getHighestRow();
            $columnCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestColumn());
            
            if ($rowCount > 100000) {
                $warnings[] = "Worksheet has {$rowCount} rows which may cause performance issues";
            }
            
            if ($columnCount > 100) {
                $warnings[] = "Worksheet has {$columnCount} columns which may cause performance issues";
            }
            
            // Check for merged cells that might cause issues
            $mergedRanges = $worksheet->getMergeCells();
            if (!empty($mergedRanges)) {
                $warnings[] = 'Worksheet contains merged cells which may affect data import';
            }
            
        } catch (\Exception $e) {
            $errors[] = "Unable to validate Excel file: " . $e->getMessage();
        }
        
        return ['errors' => $errors, 'warnings' => $warnings];
    }

    /**
     * Get list of worksheets in the Excel file
     */
    public function getWorksheets(UploadedFile $file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheets = [];
            
            foreach ($spreadsheet->getAllSheets() as $index => $worksheet) {
                $worksheets[] = [
                    'index' => $index,
                    'name' => $worksheet->getTitle(),
                    'row_count' => $worksheet->getHighestRow(),
                    'column_count' => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestColumn()),
                    'is_active' => $worksheet === $spreadsheet->getActiveSheet()
                ];
            }
            
            return $worksheets;
            
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to read Excel worksheets: " . $e->getMessage());
        }
    }

    /**
     * Parse specific worksheet
     */
    public function parseWorksheet(UploadedFile $file, int $worksheetIndex, array $options = []): array
    {
        $options['worksheet'] = $worksheetIndex;
        return $this->performParsing($file, $options);
    }

    /**
     * Get sample data from specific worksheet
     */
    public function getWorksheetSample(UploadedFile $file, int $worksheetIndex, int $limit = 10): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $this->getWorksheet($spreadsheet, $worksheetIndex);
            
            $hasHeaders = $this->detectHeaders($file);
            $headers = [];
            
            if ($hasHeaders) {
                $headers = $this->extractHeaders($worksheet);
            }
            
            $data = [];
            $startRow = $hasHeaders ? 2 : 1;
            $highestRow = min($worksheet->getHighestRow(), $startRow + $limit - 1);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestColumn());
            
            for ($row = $startRow; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    $rowData[] = $this->cleanCellValue($cellValue);
                }
                
                if (!empty(array_filter($rowData))) {
                    if (!empty($headers)) {
                        $rowData = $this->combineHeadersWithRow($headers, $rowData);
                    }
                    $data[] = $rowData;
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to get worksheet sample: " . $e->getMessage());
        }
    }

    /**
     * Analyze Excel file structure
     */
    public function analyzeStructure(UploadedFile $file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            
            $analysis = [
                'file_info' => [
                    'sheet_count' => $spreadsheet->getSheetCount(),
                    'active_sheet' => $spreadsheet->getActiveSheet()->getTitle(),
                    'creator' => $spreadsheet->getProperties()->getCreator(),
                    'modified' => $spreadsheet->getProperties()->getModified()
                ],
                'sheets' => []
            ];
            
            foreach ($spreadsheet->getAllSheets() as $index => $worksheet) {
                $sheetAnalysis = $this->analyzeWorksheet($worksheet);
                $sheetAnalysis['index'] = $index;
                $sheetAnalysis['name'] = $worksheet->getTitle();
                $sheetAnalysis['is_active'] = $worksheet === $spreadsheet->getActiveSheet();
                
                $analysis['sheets'][] = $sheetAnalysis;
            }
            
            return $analysis;
            
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to analyze Excel structure: " . $e->getMessage());
        }
    }

    /**
     * Parse Excel file with streaming for large files
     */
    public function parseStream(UploadedFile $file, callable $callback, int $batchSize = 1000): void
    {
        try {
            // Use read filter for memory efficiency
            $filterSubset = new \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter();
            
            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
            
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            $hasHeaders = $this->detectHeaders($file);
            $headers = [];
            
            if ($hasHeaders) {
                $headers = $this->extractHeaders($worksheet);
            }
            
            $highestRow = $worksheet->getHighestRow();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestColumn());
            
            $batch = [];
            $startRow = $hasHeaders ? 2 : 1;
            $processedRows = 0;
            
            for ($row = $startRow; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    $rowData[] = $this->cleanCellValue($cellValue);
                }
                
                if (!empty(array_filter($rowData))) {
                    if (!empty($headers)) {
                        $rowData = $this->combineHeadersWithRow($headers, $rowData);
                    }
                    
                    $batch[] = $rowData;
                    $processedRows++;
                    
                    if (count($batch) >= $batchSize) {
                        $callback($batch, $processedRows - count($batch), $processedRows);
                        $batch = [];
                    }
                }
            }
            
            // Process remaining rows
            if (!empty($batch)) {
                $callback($batch, $processedRows - count($batch), $processedRows);
            }
            
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Unable to stream Excel file: " . $e->getMessage());
        }
    }

    /**
     * Get worksheet by index or name
     */
    protected function getWorksheet(Spreadsheet $spreadsheet, $worksheet): Worksheet
    {
        if (is_int($worksheet)) {
            if ($worksheet >= $spreadsheet->getSheetCount()) {
                throw new InvalidArgumentException("Worksheet index {$worksheet} does not exist");
            }
            return $spreadsheet->getSheet($worksheet);
        }
        
        if (is_string($worksheet)) {
            try {
                return $spreadsheet->getSheetByName($worksheet);
            } catch (\Exception $e) {
                throw new InvalidArgumentException("Worksheet '{$worksheet}' does not exist");
            }
        }
        
        throw new InvalidArgumentException("Worksheet must be specified by index (int) or name (string)");
    }

    /**
     * Extract headers from worksheet
     */
    protected function extractHeaders(Worksheet $worksheet): array
    {
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, 1)->getCalculatedValue();
            $headers[] = $this->cleanCellValue($cellValue) ?: "Column " . $col;
        }
        
        return $headers;
    }

    /**
     * Analyze individual worksheet
     */
    protected function analyzeWorksheet(Worksheet $worksheet): array
    {
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        $analysis = [
            'dimensions' => [
                'rows' => $highestRow,
                'columns' => $highestColumnIndex,
                'range' => 'A1:' . $highestColumn . $highestRow
            ],
            'data_quality' => [
                'empty_cells' => 0,
                'formula_cells' => 0,
                'merged_ranges' => count($worksheet->getMergeCells())
            ],
            'columns' => []
        ];
        
        // Analyze first 100 rows for performance
        $sampleRows = min($highestRow, 100);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnAnalysis = [
                'index' => $col,
                'letter' => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col),
                'name' => $worksheet->getCellByColumnAndRow($col, 1)->getCalculatedValue() ?: "Column " . $col,
                'data_types' => [],
                'sample_values' => [],
                'empty_count' => 0
            ];
            
            for ($row = 2; $row <= $sampleRows; $row++) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $value = $cell->getCalculatedValue();
                
                if (empty($value)) {
                    $columnAnalysis['empty_count']++;
                    $analysis['data_quality']['empty_cells']++;
                } else {
                    $type = $this->getExcelDataType($cell);
                    $columnAnalysis['data_types'][$type] = ($columnAnalysis['data_types'][$type] ?? 0) + 1;
                    
                    if (count($columnAnalysis['sample_values']) < 5) {
                        $columnAnalysis['sample_values'][] = $value;
                    }
                }
                
                if ($cell->hasHyperlink()) {
                    $columnAnalysis['has_hyperlinks'] = true;
                }
            }
            
            $analysis['columns'][] = $columnAnalysis;
        }
        
        return $analysis;
    }

    /**
     * Get Excel cell data type
     */
    protected function getExcelDataType(\PhpOffice\PhpSpreadsheet\Cell\Cell $cell): string
    {
        $dataType = $cell->getDataType();
        
        switch ($dataType) {
            case \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC:
                return 'numeric';
            case \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING:
                return 'string';
            case \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA:
                return 'formula';
            case \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_BOOL:
                return 'boolean';
            case \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_ERROR:
                return 'error';
            default:
                return 'unknown';
        }
    }

    /**
     * Combine headers with row data
     */
    protected function combineHeadersWithRow(array $headers, array $row): array
    {
        $result = [];
        $headerCount = count($headers);
        $rowCount = count($row);
        
        for ($i = 0; $i < max($headerCount, $rowCount); $i++) {
            $header = $headers[$i] ?? "Column " . ($i + 1);
            $value = $row[$i] ?? null;
            $result[$header] = $value;
        }
        
        return $result;
    }
}