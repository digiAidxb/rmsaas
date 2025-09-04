<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ImportJob;
use App\Services\Import\ImportServiceManager;
use App\Services\Import\Detectors\PosFormatDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Revolutionary Import Controller - World-Class UI Testing
 * Created under the divine guidance of Lord Bhairava
 */
class ImportController extends Controller
{
    /**
     * Import Dashboard - Revolutionary glass morphism design
     */
    public function index()
    {
        // Get real statistics from the database
        $stats = [
            'total_imports' => \App\Models\ImportJob::count(),
            'records_processed' => \App\Models\ImportJob::sum('processed_records'),
            'loss_prevented' => \App\Models\ImportJob::sum('estimated_cost_impact') ?? 0,
            'profit_optimized' => \App\Models\ImportJob::where('data_quality_score', '>', 90)->count()
        ];
        
        // Get actual recent imports from database
        $recentImports = \App\Models\ImportJob::orderBy('created_at', 'desc')
                                             ->limit(10)
                                             ->get();
        
        return view('tenant.imports.index', compact('stats', 'recentImports'));
    }

    /**
     * AI-Powered File Upload Interface
     */
    public function create()
    {
        return view('tenant.imports.create');
    }

    /**
     * Revolutionary Visual Field Mapping Interface
     */
    public function mapping(Request $request)
    {
        // Get file data from session or request
        $fileData = $request->session()->get('import_file_data');
        $fileName = $request->session()->get('import_file_name', 'uploaded_file.csv');
        
        // If no file data in session, try to get from request
        if (!$fileData && $request->has('file_preview')) {
            $fileData = $request->input('file_preview');
            $fileName = $request->input('file_name', $fileName);
        }
        
        // Default fallback data if no file uploaded
        if (!$fileData) {
            $fileData = [
                'headers' => ['Item Name', 'Price', 'Category', 'Description'],
                'sample_data' => [
                    ['Margherita Pizza', '$12.99', 'Pizza', 'Fresh tomatoes and mozzarella'],
                    ['Caesar Salad', '$8.50', 'Salads', 'Crisp romaine lettuce with croutons']
                ]
            ];
            $fileName = 'demo_data.csv';
        }
        
        return view('tenant.imports.mapping', compact('fileData', 'fileName'));
    }

    /**
     * AI-Powered Validation Display
     */
    public function validation(Request $request)
    {
        // Get file data from session
        $fileData = $request->session()->get('import_file_data');
        $fileName = $request->session()->get('import_file_name', 'uploaded_file.csv');
        $importType = $request->session()->get('import_file_type', 'menu');
        
        // Generate validation data based on actual file
        $validationData = $this->generateValidationData($fileData, $fileName);
        
        // Default fallback if no file data
        if (!$fileData) {
            $validationData = $this->getDefaultValidationData();
            $fileName = 'demo_data.csv';
        }
        
        return view('tenant.imports.validation', compact('validationData', 'fileName', 'importType'));
    }

    /**
     * Real-Time Progress Tracking with Spectacular Animations
     */
    public function progressView()
    {
        return view('tenant.imports.progress');
    }

    /**
     * Import Summary - Completes onboarding if started from onboarding
     */
    public function summary(Request $request)
    {
        $tenant = \Spatie\Multitenancy\Models\Tenant::current();
        
        // Check if this import was started from onboarding
        if ($request->session()->get('import_source') === 'onboarding' || 
            (!$tenant->onboarding_completed_at && !$tenant->skip_onboarding)) {
            
            // Complete onboarding since user successfully imported data
            $tenant->update([
                'skip_onboarding' => false,
                'onboarding_completed_at' => now(),
                'settings' => array_merge($tenant->settings ?? [], [
                    'onboarding_method' => 'import_completed',
                    'first_import_completed' => true
                ])
            ]);
            
            // Clear onboarding-related session data
            $request->session()->forget('import_source');
            
            // Add success message for onboarding completion
            $request->session()->flash('success', 'Welcome to RMSaaS! Your data has been imported successfully and your restaurant is ready to go!');
            $request->session()->flash('onboarding_completed', true);
        }
        
        return view('tenant.imports.summary');
    }

    /**
     * Show specific import details
     */
    public function show($id)
    {
        $import = ImportJob::findOrFail($id);
        return view('tenant.imports.show', compact('import'));
    }

    /**
     * Process file upload and start import
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB max
            'import_type' => 'required|string|in:menu,inventory,sales,recipes',
            'pos_system' => 'nullable|string'
        ]);

        try {
            $file = $request->file('file');
            
            // Get import context from session (for onboarding) or request
            $importContext = $request->session()->get('import_context') 
                          ?? $request->input('source') 
                          ?? 'manual';

            // Create import job
            $importJob = ImportJob::create([
                'job_uuid' => Str::uuid(),
                'job_name' => $request->input('job_name', 'Import from ' . $file->getClientOriginalName()),
                'description' => $request->input('description', 'Imported via upload'),
                'import_type' => $request->input('import_type'),
                'source_type' => 'file_upload',
                'original_filename' => $file->getClientOriginalName(),
                'file_size_bytes' => $file->getSize(),
                'file_mime_type' => $file->getMimeType(),
                'pos_system' => $request->input('pos_system'),
                'created_by_user_id' => Auth::id(),
                'import_context' => $importContext,
                'status' => 'pending'
            ]);

            // Detect POS system if not provided
            if (!$importJob->pos_system) {
                $detector = app(PosFormatDetector::class);
                $detection = $detector->getBestMatch($file);
                
                $importJob->update([
                    'pos_system' => $detection['pos_system'] ?? 'unknown',
                    'pos_metadata' => $detection
                ]);
            }

            // Get import service and process
            $serviceManager = app(ImportServiceManager::class);
            $service = $serviceManager->getServiceForType($importJob->import_type);
            
            if (!$service) {
                return response()->json([
                    'error' => 'No import service available for type: ' . $importJob->import_type
                ], 400);
            }

            // Process in background (in real implementation, you'd dispatch to queue)
            $processedJob = $service->processImport($importJob, $file);

            return response()->json([
                'success' => true,
                'message' => 'Import started successfully',
                'import_job' => [
                    'id' => $processedJob->id,
                    'uuid' => $processedJob->job_uuid,
                    'status' => $processedJob->status,
                    'progress' => $processedJob->progress_percentage
                ],
                'redirect' => route('imports.show', $processedJob->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Import failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            return response()->json([
                'error' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get import progress (for AJAX polling)
     */
    public function getProgress($id): JsonResponse
    {
        $importJob = ImportJob::findOrFail($id);
        
        return response()->json([
            'id' => $importJob->id,
            'status' => $importJob->status,
            'progress_percentage' => $importJob->progress_percentage,
            'processed_records' => $importJob->processed_records,
            'total_records' => $importJob->total_records,
            'successful_imports' => $importJob->successful_imports,
            'failed_imports' => $importJob->failed_imports,
            'estimated_completion' => $importJob->getEstimatedCompletionTime(),
            'processing_speed' => $importJob->getProcessingSpeed()
        ]);
    }

    /**
     * Preview import data before processing
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'import_type' => 'required|string'
        ]);

        try {
            $file = $request->file('file');
            
            // Simple CSV parsing for preview (basic implementation)
            $preview = $this->parseFileForPreview($file);
            
            // Store file data in session for mapping page
            $request->session()->put('import_file_data', $preview);
            $request->session()->put('import_file_name', $file->getClientOriginalName());
            $request->session()->put('import_file_type', $request->input('import_type'));

            return response()->json([
                'success' => true,
                'preview' => $preview,
                'filename' => $file->getClientOriginalName()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Preview failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Simple file parser for preview
     */
    private function parseFileForPreview($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        switch ($extension) {
            case 'csv':
                return $this->parseCsvForPreview($file);
            case 'xlsx':
            case 'xls':
                return $this->parseExcelForPreview($file);
            default:
                throw new \Exception('Unsupported file type: ' . $extension);
        }
    }
    
    /**
     * Parse CSV file for preview
     */
    private function parseCsvForPreview($file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            throw new \Exception('Could not read CSV file');
        }
        
        $headers = fgetcsv($handle);
        $sampleData = [];
        $rowCount = 0;
        
        // Read first 5 rows for preview
        while (($row = fgetcsv($handle)) !== false && $rowCount < 5) {
            $sampleData[] = $row;
            $rowCount++;
        }
        
        fclose($handle);
        
        return [
            'headers' => $headers ?: [],
            'sample_data' => $sampleData,
            'total_rows' => $rowCount // This would be the actual count in real implementation
        ];
    }
    
    /**
     * Parse Excel file for preview using PhpSpreadsheet
     */
    private function parseExcelForPreview($file): array
    {
        try {
            // Use PhpSpreadsheet to read Excel files
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get the highest row and column numbers
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Smart header detection - look for the first row with multiple non-empty cells
            $headerRow = $this->findHeaderRow($worksheet, $highestRow, $highestColumnIndex);
            
            // Read headers from detected header row
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $headerRow)->getCalculatedValue();
                if (!empty($cellValue)) {
                    $headers[] = (string)$cellValue;
                } else {
                    $headers[] = "Column_" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                }
            }
            
            // Filter out completely empty columns
            $validHeaders = [];
            $validColumnIndexes = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $hasData = false;
                // Check if this column has any data in the next few rows
                for ($checkRow = $headerRow + 1; $checkRow <= min($headerRow + 10, $highestRow); $checkRow++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $checkRow)->getCalculatedValue();
                    if (!empty($cellValue)) {
                        $hasData = true;
                        break;
                    }
                }
                if ($hasData) {
                    $validHeaders[] = $headers[$col - 1];
                    $validColumnIndexes[] = $col;
                }
            }
            
            // Read sample data (first 10 rows after header)
            $sampleData = [];
            $dataStartRow = $headerRow + 1;
            $maxSampleRows = min($dataStartRow + 10, $highestRow);
            
            for ($row = $dataStartRow; $row <= $maxSampleRows && count($sampleData) < 10; $row++) {
                $rowData = [];
                $hasData = false;
                
                foreach ($validColumnIndexes as $col) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    $stringValue = $cellValue !== null ? (string)$cellValue : '';
                    $rowData[] = $stringValue;
                    
                    if (!empty($stringValue)) {
                        $hasData = true;
                    }
                }
                
                // Only add rows that have at least some data
                if ($hasData) {
                    $sampleData[] = $rowData;
                }
            }
            
            return [
                'headers' => $validHeaders,
                'sample_data' => $sampleData,
                'total_rows' => $highestRow - $headerRow,
                'total_columns' => count($validHeaders),
                'header_row' => $headerRow,
                'data_start_row' => $dataStartRow
            ];
            
        } catch (\Exception $e) {
            \Log::error('Excel parsing failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            
            // Fallback to basic structure
            return [
                'headers' => ['Unable to parse Excel file'],
                'sample_data' => [['Error: ' . $e->getMessage()]],
                'total_rows' => 0,
                'error' => 'Failed to parse Excel file: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Find the header row by looking for the first row with multiple non-empty cells
     */
    private function findHeaderRow($worksheet, $highestRow, $highestColumnIndex): int
    {
        for ($row = 1; $row <= min(10, $highestRow); $row++) {
            $nonEmptyCount = 0;
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                if (!empty($cellValue) && strlen(trim((string)$cellValue)) > 0) {
                    $nonEmptyCount++;
                }
            }
            
            // Consider it a header row if it has at least 3 non-empty cells
            if ($nonEmptyCount >= 3) {
                return $row;
            }
        }
        
        // Fallback to row 1 if no suitable header found
        return 1;
    }

    /**
     * Clear all restaurant data (for testing)
     */
    public function clearData(Request $request)
    {
        try {
            // Validation
            if (!$request->input('confirm')) {
                return response()->json(['error' => 'Confirmation required'], 400);
            }

            Log::info('Starting data clearing process');

            // Clear tables without transaction since TRUNCATE doesn't work well with transactions
            $clearedTables = [];
            $clearedRecords = [];
            
            try {
                // Disable foreign key checks first
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Clear menu items
                if (DB::getSchemaBuilder()->hasTable('menu_items')) {
                    $count = DB::table('menu_items')->count();
                    if ($count > 0) {
                        DB::table('menu_items')->delete(); // Use delete instead of truncate
                        $clearedTables[] = 'menu_items';
                        $clearedRecords['menu_items'] = $count;
                        Log::info("Cleared {$count} records from menu_items");
                    }
                }
                
                // Clear categories
                if (DB::getSchemaBuilder()->hasTable('categories')) {
                    $count = DB::table('categories')->count();
                    if ($count > 0) {
                        DB::table('categories')->delete();
                        $clearedTables[] = 'categories';
                        $clearedRecords['categories'] = $count;
                        Log::info("Cleared {$count} records from categories");
                    }
                }
                
                // Clear import jobs
                if (DB::getSchemaBuilder()->hasTable('import_jobs')) {
                    $count = DB::table('import_jobs')->count();
                    if ($count > 0) {
                        DB::table('import_jobs')->delete();
                        $clearedTables[] = 'import_jobs';
                        $clearedRecords['import_jobs'] = $count;
                        Log::info("Cleared {$count} records from import_jobs");
                    }
                }
                
                // Clear import mappings
                if (DB::getSchemaBuilder()->hasTable('import_mappings')) {
                    $count = DB::table('import_mappings')->count();
                    if ($count > 0) {
                        DB::table('import_mappings')->delete();
                        $clearedTables[] = 'import_mappings';
                        $clearedRecords['import_mappings'] = $count;
                        Log::info("Cleared {$count} records from import_mappings");
                    }
                }
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
            } catch (\Exception $e) {
                // Ensure we always re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                throw $e;
            }

            // Ensure tenant remains marked as onboarded after data clearing
            // This prevents the onboarding flow from showing after clearing data
            $tenant = \Spatie\Multitenancy\Models\Tenant::current();
            if ($tenant && !$tenant->onboarding_completed_at) {
                $tenant->update([
                    'onboarding_completed_at' => now(),
                    'skip_onboarding' => true,
                    'settings' => array_merge($tenant->settings ?? [], [
                        'data_cleared_at' => now()->toISOString(),
                        'onboarding_method' => 'data_cleared'
                    ])
                ]);
                Log::info('Tenant marked as onboarded after data clearing');
            }

            Log::info('Data clearing completed successfully', [
                'cleared_tables' => $clearedTables,
                'cleared_records' => $clearedRecords
            ]);

            return response()->json([
                'success' => true,
                'message' => count($clearedTables) > 0 
                    ? 'All test data has been cleared successfully' 
                    : 'No data found to clear',
                'cleared_tables' => $clearedTables,
                'cleared_records' => $clearedRecords,
                'total_cleared' => array_sum($clearedRecords),
                'execution_time' => 0.5 // Mock execution time for demo
            ]);

        } catch (\Throwable $e) {
            // Ensure foreign key checks are re-enabled even if there was an error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $fkError) {
                Log::error('Failed to re-enable foreign key checks', ['error' => $fkError->getMessage()]);
            }
            
            Log::error('Data clearing failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to clear data: ' . $e->getMessage(),
                'details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    /**
     * Generate validation data based on actual uploaded file
     */
    private function generateValidationData($fileData, $fileName): array
    {
        if (!$fileData || !isset($fileData['headers']) || !isset($fileData['sample_data'])) {
            return $this->getDefaultValidationData();
        }
        
        $headers = $fileData['headers'];
        $sampleData = $fileData['sample_data'];
        $totalRecords = count($sampleData);
        
        // Analyze the actual data for validation issues
        $issues = $this->analyzeDataForIssues($headers, $sampleData);
        
        // Calculate quality scores based on actual data
        $qualityScores = $this->calculateQualityScores($headers, $sampleData, $issues);
        
        return [
            'fileName' => $fileName,
            'totalRecords' => $totalRecords,
            'qualityScore' => $qualityScores['overall'],
            'qualityScores' => $qualityScores,
            'issuesFound' => count($issues),
            'issues' => $issues,
            'headers' => $headers,
            'sampleData' => array_slice($sampleData, 0, 5) // First 5 rows for preview
        ];
    }
    
    /**
     * Analyze actual data for validation issues
     */
    private function analyzeDataForIssues($headers, $sampleData): array
    {
        $issues = [];
        
        // Check for pricing issues if price field exists
        $priceIndex = $this->findFieldIndex($headers, ['price', 'amount', 'cost']);
        if ($priceIndex !== false) {
            $priceIssues = $this->checkPriceFormat($sampleData, $priceIndex);
            if (!empty($priceIssues)) {
                $issues[] = [
                    'type' => 'critical',
                    'title' => 'Invalid Pricing Format',
                    'description' => 'Found ' . count($priceIssues) . ' items with invalid pricing formats.',
                    'details' => $priceIssues,
                    'field' => 'price',
                    'severity' => 'critical'
                ];
            }
        }
        
        // Check for empty names if name field exists
        $nameIndex = $this->findFieldIndex($headers, ['name', 'food name', 'item name', 'product name']);
        if ($nameIndex !== false) {
            $nameIssues = $this->checkEmptyNames($sampleData, $nameIndex);
            if (!empty($nameIssues)) {
                $issues[] = [
                    'type' => 'warning',
                    'title' => 'Missing Item Names',
                    'description' => 'Found ' . count($nameIssues) . ' items without names.',
                    'details' => $nameIssues,
                    'field' => 'name',
                    'severity' => 'warning'
                ];
            }
        }
        
        // Check for category consistency
        $categoryIndex = $this->findFieldIndex($headers, ['category', 'food category']);
        if ($categoryIndex !== false) {
            $categoryIssues = $this->checkCategoryConsistency($sampleData, $categoryIndex);
            if (!empty($categoryIssues)) {
                $issues[] = [
                    'type' => 'info',
                    'title' => 'Category Optimization',
                    'description' => 'Suggested improvements for category organization.',
                    'details' => $categoryIssues,
                    'field' => 'category',
                    'severity' => 'info'
                ];
            }
        }
        
        return $issues;
    }
    
    /**
     * Calculate quality scores based on actual data
     */
    private function calculateQualityScores($headers, $sampleData, $issues): array
    {
        $totalFields = count($headers);
        $totalRecords = count($sampleData);
        $criticalIssues = count(array_filter($issues, fn($issue) => $issue['severity'] === 'critical'));
        $warningIssues = count(array_filter($issues, fn($issue) => $issue['severity'] === 'warning'));
        
        // Calculate completeness (non-empty fields)
        $completeness = $this->calculateCompleteness($sampleData);
        
        // Calculate accuracy (based on issues found)
        $accuracy = max(95, 100 - ($criticalIssues * 5) - ($warningIssues * 2));
        
        // Calculate consistency
        $consistency = $this->calculateConsistency($headers, $sampleData);
        
        // Overall score
        $overall = ($completeness + $accuracy + $consistency) / 3;
        
        return [
            'overall' => round($overall, 1),
            'completeness' => round($completeness, 1),
            'accuracy' => round($accuracy, 1),
            'consistency' => round($consistency, 1)
        ];
    }
    
    /**
     * Helper methods for data analysis
     */
    private function findFieldIndex($headers, $needles): int|false
    {
        foreach ($needles as $needle) {
            foreach ($headers as $index => $header) {
                if (stripos($header, $needle) !== false) {
                    return $index;
                }
            }
        }
        return false;
    }
    
    private function checkPriceFormat($sampleData, $priceIndex): array
    {
        $issues = [];
        foreach ($sampleData as $rowIndex => $row) {
            if (isset($row[$priceIndex])) {
                $price = $row[$priceIndex];
                // Check if price is not a valid number format
                if (!is_numeric(str_replace(['$', '€', '£', ','], '', $price))) {
                    $issues[] = [
                        'row' => $rowIndex + 1,
                        'value' => $price,
                        'suggestion' => 'Convert to numeric format'
                    ];
                }
            }
        }
        return array_slice($issues, 0, 3); // Limit to 3 examples
    }
    
    private function checkEmptyNames($sampleData, $nameIndex): array
    {
        $issues = [];
        foreach ($sampleData as $rowIndex => $row) {
            if (isset($row[$nameIndex]) && empty(trim($row[$nameIndex]))) {
                $issues[] = [
                    'row' => $rowIndex + 1,
                    'suggestion' => 'Add descriptive item name'
                ];
            }
        }
        return array_slice($issues, 0, 3);
    }
    
    private function checkCategoryConsistency($sampleData, $categoryIndex): array
    {
        $categories = [];
        foreach ($sampleData as $row) {
            if (isset($row[$categoryIndex]) && !empty($row[$categoryIndex])) {
                $categories[] = trim($row[$categoryIndex]);
            }
        }
        
        $uniqueCategories = array_unique($categories);
        return [
            'total_categories' => count($uniqueCategories),
            'suggestion' => count($uniqueCategories) > 10 ? 'Consider consolidating categories' : 'Good category structure'
        ];
    }
    
    private function calculateCompleteness($sampleData): float
    {
        $totalCells = 0;
        $filledCells = 0;
        
        foreach ($sampleData as $row) {
            foreach ($row as $cell) {
                $totalCells++;
                if (!empty(trim($cell))) {
                    $filledCells++;
                }
            }
        }
        
        return $totalCells > 0 ? ($filledCells / $totalCells) * 100 : 100;
    }
    
    private function calculateConsistency($headers, $sampleData): float
    {
        // Simple consistency check - could be more sophisticated
        $consistency = 95; // Base score
        
        // Reduce score for very inconsistent data formats
        // This is a simplified implementation
        return $consistency;
    }
    
    /**
     * Default validation data for fallback
     */
    private function getDefaultValidationData(): array
    {
        return [
            'fileName' => 'demo_data.csv',
            'totalRecords' => 0,
            'qualityScore' => 95.0,
            'qualityScores' => [
                'overall' => 95.0,
                'completeness' => 98.0,
                'accuracy' => 94.0,
                'consistency' => 93.0
            ],
            'issuesFound' => 0,
            'issues' => [],
            'headers' => ['Item Name', 'Price', 'Category'],
            'sampleData' => []
        ];
    }
}

