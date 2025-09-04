<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Comprehensive analysis of inventory_master.xlsx to understand
 * ingredient coverage, categories, and unit standardization
 */
function analyzeInventoryData() {
    $filePath = 'rawdata/inventory_master.xlsx';
    
    if (!file_exists($filePath)) {
        echo "❌ Inventory file not found: {$filePath}\n";
        return;
    }
    
    echo "📦 ANALYZING INVENTORY MASTER DATA\n";
    echo "==================================\n\n";
    
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        
        echo "📊 FILE STRUCTURE\n";
        echo "================\n";
        echo "Total rows: {$highestRow}\n";
        echo "Total columns: {$highestColumn}\n\n";
        
        // Extract headers
        $headers = [];
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headerValue = $worksheet->getCellByColumnAndRow($col, 1)->getCalculatedValue();
            if (!empty($headerValue)) {
                $headers[$col] = trim($headerValue);
            }
        }
        
        echo "📋 HEADERS DETECTED:\n";
        echo "==================\n";
        foreach ($headers as $col => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            echo "Column {$colLetter} ({$col}): '{$header}'\n";
        }
        echo "\n";
        
        // Extract all inventory data
        $inventory = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $itemData = [];
            foreach ($headers as $col => $header) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                $itemData[$header] = $cellValue;
            }
            
            // Skip empty rows
            if (!empty(array_filter($itemData))) {
                $inventory[] = $itemData;
            }
        }
        
        echo "📦 INVENTORY ANALYSIS\n";
        echo "====================\n";
        echo "Total inventory items: " . count($inventory) . "\n\n";
        
        // Analyze inventory structure
        if (!empty($inventory)) {
            analyzeInventoryStructure($inventory);
            analyzeCategoriesAndSubcategories($inventory);
            analyzeUnitsAndMeasurements($inventory);
            analyzeRecipeCoverage($inventory);
            generateInventoryOptimizations($inventory);
        }
        
        return $inventory;
        
    } catch (Exception $e) {
        echo "❌ Error analyzing inventory file: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

function analyzeInventoryStructure($inventory) {
    echo "📊 INVENTORY STRUCTURE ANALYSIS\n";
    echo "==============================\n";
    
    $itemCodes = [];
    $itemNames = [];
    $suppliers = [];
    $stockLevels = [];
    $costs = [];
    
    foreach ($inventory as $item) {
        // Extract item codes
        $code = $item['Item Code'] ?? $item['code'] ?? '';
        if (!empty($code)) {
            $itemCodes[] = $code;
        }
        
        // Extract item names
        $name = $item['Item Name'] ?? $item['name'] ?? '';
        if (!empty($name)) {
            $itemNames[] = $name;
        }
        
        // Extract suppliers
        $supplier = $item['Supplier'] ?? $item['supplier'] ?? '';
        if (!empty($supplier)) {
            $suppliers[] = $supplier;
        }
        
        // Extract stock levels
        $stock = $item['Current Stock'] ?? $item['stock'] ?? '';
        if (is_numeric($stock)) {
            $stockLevels[] = $stock;
        }
        
        // Extract costs
        $cost = $item['Unit Cost'] ?? $item['cost'] ?? '';
        if (is_numeric($cost)) {
            $costs[] = $cost;
        }
    }
    
    echo "Item Codes: " . count(array_unique($itemCodes)) . " unique codes\n";
    echo "Item Names: " . count(array_unique($itemNames)) . " unique items\n";
    echo "Suppliers: " . implode(', ', array_unique($suppliers)) . "\n";
    
    if (!empty($stockLevels)) {
        echo "Stock Range: " . min($stockLevels) . " - " . max($stockLevels) . " units\n";
    }
    
    if (!empty($costs)) {
        echo "Cost Range: AED " . number_format(min($costs), 2) . " - AED " . number_format(max($costs), 2) . "\n";
    }
    
    echo "\n📋 SAMPLE INVENTORY ITEMS (First 10):\n";
    echo "====================================\n";
    foreach (array_slice($inventory, 0, 10) as $i => $item) {
        echo "Item " . ($i + 1) . ":\n";
        foreach ($item as $field => $value) {
            if (!empty($value)) {
                echo "  {$field}: {$value}\n";
            }
        }
        echo "\n";
    }
}

function analyzeCategoriesAndSubcategories($inventory) {
    echo "🏷️ CATEGORIES AND SUBCATEGORIES ANALYSIS\n";
    echo "========================================\n";
    
    $categories = [];
    $subcategories = [];
    
    foreach ($inventory as $item) {
        $category = $item['Category'] ?? $item['category'] ?? '';
        $subcategory = $item['Subcategory'] ?? $item['subcategory'] ?? '';
        
        if (!empty($category)) {
            $categories[] = $category;
        }
        
        if (!empty($subcategory)) {
            $subcategories[] = $subcategory;
        }
    }
    
    $categoryCounts = array_count_values($categories);
    $subcategoryCounts = array_count_values($subcategories);
    
    arsort($categoryCounts);
    arsort($subcategoryCounts);
    
    echo "📊 CATEGORIES BY FREQUENCY:\n";
    echo "==========================\n";
    foreach ($categoryCounts as $category => $count) {
        echo "{$category}: {$count} items\n";
    }
    
    echo "\n📊 SUBCATEGORIES BY FREQUENCY:\n";
    echo "=============================\n";
    foreach ($subcategoryCounts as $subcategory => $count) {
        echo "{$subcategory}: {$count} items\n";
    }
    
    // Suggest category optimization
    echo "\n💡 CATEGORY OPTIMIZATION SUGGESTIONS:\n";
    echo "====================================\n";
    
    $suggestedCategories = [
        'PROTEINS' => ['chicken', 'mutton', 'fish', 'egg', 'lamb', 'beef'],
        'VEGETABLES' => ['onion', 'tomato', 'potato', 'carrot', 'peas', 'capsicum'],
        'SPICES & SEASONINGS' => ['masala', 'powder', 'salt', 'pepper', 'cardamom'],
        'DAIRY & EGGS' => ['milk', 'yogurt', 'cream', 'cheese', 'ghee', 'butter'],
        'GRAINS & FLOUR' => ['rice', 'flour', 'wheat', 'bread'],
        'OILS & FATS' => ['oil', 'ghee', 'butter'],
        'CONDIMENTS & SAUCES' => ['sauce', 'paste', 'vinegar'],
        'BEVERAGES' => ['tea', 'coffee', 'juice', 'water'],
        'FROZEN ITEMS' => ['frozen'],
        'DRY GOODS' => ['lentils', 'beans', 'nuts']
    ];
    
    foreach ($suggestedCategories as $category => $keywords) {
        echo "📦 {$category}:\n";
        $foundItems = [];
        
        foreach ($inventory as $item) {
            $itemName = strtolower($item['Item Name'] ?? '');
            foreach ($keywords as $keyword) {
                if (strpos($itemName, $keyword) !== false) {
                    $foundItems[] = $item['Item Name'] ?? '';
                    break;
                }
            }
        }
        
        $foundItems = array_unique($foundItems);
        if (!empty($foundItems)) {
            echo "  Found: " . implode(', ', array_slice($foundItems, 0, 5));
            if (count($foundItems) > 5) {
                echo " (+" . (count($foundItems) - 5) . " more)";
            }
            echo "\n";
        } else {
            echo "  No items found - consider adding\n";
        }
    }
    echo "\n";
}

function analyzeUnitsAndMeasurements($inventory) {
    echo "📏 UNITS AND MEASUREMENTS ANALYSIS\n";
    echo "=================================\n";
    
    $units = [];
    $measurements = [];
    
    foreach ($inventory as $item) {
        $unit = $item['Unit'] ?? $item['unit'] ?? '';
        if (!empty($unit)) {
            $units[] = strtolower(trim($unit));
        }
        
        // Look for measurement-related fields
        foreach ($item as $field => $value) {
            if (stripos($field, 'measurement') !== false || 
                stripos($field, 'size') !== false || 
                stripos($field, 'weight') !== false) {
                if (!empty($value)) {
                    $measurements[] = $value;
                }
            }
        }
    }
    
    $unitCounts = array_count_values($units);
    arsort($unitCounts);
    
    echo "📊 UNITS USAGE FREQUENCY:\n";
    echo "========================\n";
    foreach ($unitCounts as $unit => $count) {
        echo "{$unit}: {$count} times\n";
    }
    
    echo "\n🎯 UNIT STANDARDIZATION RECOMMENDATIONS:\n";
    echo "======================================\n";
    
    $standardUnits = [
        'Weight' => [
            'Primary' => 'KG',
            'Secondary' => 'G',
            'Usage' => 'For solid ingredients (meat, vegetables, spices)',
            'Conversion' => '1 KG = 1000 G'
        ],
        'Volume' => [
            'Primary' => 'L',
            'Secondary' => 'ML',
            'Usage' => 'For liquids (oil, milk, sauces)',
            'Conversion' => '1 L = 1000 ML'
        ],
        'Pieces' => [
            'Primary' => 'PCS',
            'Secondary' => 'PC',
            'Usage' => 'For countable items (eggs, fruits)',
            'Conversion' => 'Direct count'
        ],
        'Packaging' => [
            'Primary' => 'PACK',
            'Secondary' => 'BOX',
            'Usage' => 'For packaged goods',
            'Conversion' => 'Per supplier specification'
        ]
    ];
    
    foreach ($standardUnits as $type => $details) {
        echo "📦 {$type}:\n";
        echo "  Primary: {$details['Primary']}\n";
        echo "  Secondary: {$details['Secondary']}\n";
        echo "  Usage: {$details['Usage']}\n";
        echo "  Conversion: {$details['Conversion']}\n\n";
    }
}

function analyzeRecipeCoverage($inventory) {
    echo "🍽️ RECIPE COVERAGE ANALYSIS\n";
    echo "===========================\n";
    
    // Define recipe ingredients from our recipes analysis
    $recipeIngredients = [
        'Chicken Breast', 'Momo Flour', 'Onions', 'Ginger', 'Garlic',
        'Eggs', 'Fish Fillet', 'Lamb Shoulder', 'Tomatoes', 'Potatoes',
        'Carrots', 'Frozen Peas', 'Garam Masala', 'Turmeric Powder',
        'Green Chilies', 'Red Chili Powder', 'Cardamom', 'Coconut Milk',
        'Yogurt', 'Milk', 'Basmati Rice'
    ];
    
    $inventoryItems = [];
    foreach ($inventory as $item) {
        $name = $item['Item Name'] ?? '';
        if (!empty($name)) {
            $inventoryItems[] = $name;
        }
    }
    
    echo "🔍 RECIPE INGREDIENT COVERAGE:\n";
    echo "=============================\n";
    
    $covered = [];
    $missing = [];
    
    foreach ($recipeIngredients as $ingredient) {
        $found = false;
        foreach ($inventoryItems as $inventoryItem) {
            if (stripos($inventoryItem, $ingredient) !== false || 
                stripos($ingredient, $inventoryItem) !== false) {
                $covered[] = $ingredient;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $missing[] = $ingredient;
        }
    }
    
    $coveragePercentage = (count($covered) / count($recipeIngredients)) * 100;
    
    echo "Coverage: " . round($coveragePercentage, 1) . "% (" . count($covered) . "/" . count($recipeIngredients) . ")\n\n";
    
    echo "✅ COVERED INGREDIENTS (" . count($covered) . "):\n";
    echo "============================\n";
    foreach ($covered as $ingredient) {
        echo "  ✓ {$ingredient}\n";
    }
    
    echo "\n❌ MISSING INGREDIENTS (" . count($missing) . "):\n";
    echo "============================\n";
    foreach ($missing as $ingredient) {
        echo "  ✗ {$ingredient}\n";
    }
    echo "\n";
}

function generateInventoryOptimizations($inventory) {
    echo "💡 INVENTORY OPTIMIZATION RECOMMENDATIONS\n";
    echo "========================================\n";
    
    echo "📊 PROCUREMENT OPTIMIZATION:\n";
    echo "===========================\n";
    echo "1. ABC Analysis: Categorize items by value and usage frequency\n";
    echo "2. Economic Order Quantity: Calculate optimal order quantities\n";
    echo "3. Supplier Consolidation: Group items by supplier for bulk discounts\n";
    echo "4. Lead Time Analysis: Track procurement times for better planning\n\n";
    
    echo "📈 STOCK MANAGEMENT:\n";
    echo "===================\n";
    echo "1. Safety Stock Levels: Set minimum levels based on usage patterns\n";
    echo "2. Reorder Points: Calculate when to reorder each item\n";
    echo "3. Max Stock Levels: Prevent overstocking and waste\n";
    echo "4. Turnover Analysis: Identify slow and fast-moving items\n\n";
    
    echo "💰 COST OPTIMIZATION:\n";
    echo "====================\n";
    echo "1. Price Tracking: Monitor supplier price changes\n";
    echo "2. Substitute Analysis: Find alternative suppliers/products\n";
    echo "3. Seasonal Planning: Adjust procurement for seasonal prices\n";
    echo "4. Bulk Discount Analysis: Identify opportunities for volume pricing\n\n";
    
    echo "🔄 OPERATIONAL EFFICIENCY:\n";
    echo "=========================\n";
    echo "1. Storage Optimization: Organize by category and usage frequency\n";
    echo "2. FIFO Implementation: First-In-First-Out for perishables\n";
    echo "3. Cross-docking: Direct supplier-to-kitchen for high-turnover items\n";
    echo "4. Waste Tracking: Monitor and reduce ingredient waste\n\n";
}

// Execute analysis
echo "🚀 Starting comprehensive inventory analysis...\n\n";
$inventory = analyzeInventoryData();
echo "✅ Inventory analysis complete!\n";

?>