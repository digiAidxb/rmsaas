<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Comprehensive analysis of recipes_master.xlsx to understand
 * ingredient coverage and optimization opportunities
 */
function analyzeRecipesData() {
    $filePath = 'rawdata/recipes_master.xlsx';
    
    if (!file_exists($filePath)) {
        echo "❌ Recipes file not found: {$filePath}\n";
        return;
    }
    
    echo "👨‍🍳 ANALYZING RECIPES MASTER DATA\n";
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
        
        // Extract all recipe data
        $recipes = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $recipeData = [];
            foreach ($headers as $col => $header) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                $recipeData[$header] = $cellValue;
            }
            
            // Skip empty rows
            if (!empty(array_filter($recipeData))) {
                $recipes[] = $recipeData;
            }
        }
        
        echo "🍽️ RECIPE ANALYSIS\n";
        echo "==================\n";
        echo "Total recipes found: " . count($recipes) . "\n\n";
        
        // Analyze recipe structure
        if (!empty($recipes)) {
            analyzeRecipeStructure($recipes);
            analyzeIngredients($recipes);
            analyzeCategoriesAndUnits($recipes);
            generateOptimizationSuggestions($recipes);
        }
        
        return $recipes;
        
    } catch (Exception $e) {
        echo "❌ Error analyzing recipes file: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

function analyzeRecipeStructure($recipes) {
    echo "📊 RECIPE STRUCTURE ANALYSIS\n";
    echo "===========================\n";
    
    $recipeNames = [];
    $categories = [];
    $difficulties = [];
    $servingSizes = [];
    
    foreach ($recipes as $recipe) {
        // Extract recipe names
        $recipeName = $recipe['Recipe Name'] ?? $recipe['name'] ?? '';
        if (!empty($recipeName)) {
            $recipeNames[] = $recipeName;
        }
        
        // Extract categories
        $category = $recipe['Category'] ?? $recipe['category'] ?? '';
        if (!empty($category)) {
            $categories[] = $category;
        }
        
        // Extract difficulty levels
        $difficulty = $recipe['Difficulty'] ?? $recipe['difficulty'] ?? '';
        if (!empty($difficulty)) {
            $difficulties[] = $difficulty;
        }
        
        // Extract serving sizes
        $servings = $recipe['Servings'] ?? $recipe['servings'] ?? '';
        if (!empty($servings)) {
            $servingSizes[] = $servings;
        }
    }
    
    echo "Recipe Names: " . count(array_unique($recipeNames)) . " unique recipes\n";
    echo "Categories: " . implode(', ', array_unique($categories)) . "\n";
    echo "Difficulty Levels: " . implode(', ', array_unique($difficulties)) . "\n";
    echo "Serving Sizes Range: " . min($servingSizes) . " - " . max($servingSizes) . " servings\n\n";
    
    // Show sample recipes
    echo "📋 SAMPLE RECIPES (First 5):\n";
    echo "============================\n";
    foreach (array_slice($recipes, 0, 5) as $i => $recipe) {
        echo "Recipe " . ($i + 1) . ":\n";
        foreach ($recipe as $field => $value) {
            if (!empty($value)) {
                echo "  {$field}: {$value}\n";
            }
        }
        echo "\n";
    }
}

function analyzeIngredients($recipes) {
    echo "🥬 INGREDIENT ANALYSIS\n";
    echo "=====================\n";
    
    $allIngredients = [];
    $ingredientUnits = [];
    $ingredientQuantities = [];
    
    foreach ($recipes as $recipe) {
        foreach ($recipe as $field => $value) {
            // Look for ingredient-related fields
            if (stripos($field, 'ingredient') !== false && !empty($value)) {
                if (stripos($field, 'name') !== false) {
                    $allIngredients[] = $value;
                } elseif (stripos($field, 'unit') !== false) {
                    $ingredientUnits[] = $value;
                } elseif (stripos($field, 'quantity') !== false && is_numeric($value)) {
                    $ingredientQuantities[] = $value;
                }
            }
        }
    }
    
    $uniqueIngredients = array_unique($allIngredients);
    $uniqueUnits = array_unique($ingredientUnits);
    
    echo "Total ingredients found: " . count($allIngredients) . "\n";
    echo "Unique ingredients: " . count($uniqueIngredients) . "\n";
    echo "Units used: " . implode(', ', $uniqueUnits) . "\n";
    
    if (!empty($ingredientQuantities)) {
        echo "Quantity range: " . min($ingredientQuantities) . " - " . max($ingredientQuantities) . "\n";
    }
    
    echo "\n🔍 INGREDIENT BREAKDOWN:\n";
    echo "=======================\n";
    
    // Categorize ingredients
    $proteinSources = [];
    $vegetables = [];
    $spices = [];
    $dairy = [];
    $grains = [];
    
    foreach ($uniqueIngredients as $ingredient) {
        $ingredientLower = strtolower($ingredient);
        
        if (preg_match('/chicken|mutton|lamb|beef|fish|egg/', $ingredientLower)) {
            $proteinSources[] = $ingredient;
        } elseif (preg_match('/onion|tomato|potato|carrot|peas|spinach|capsicum|garlic|ginger/', $ingredientLower)) {
            $vegetables[] = $ingredient;
        } elseif (preg_match('/masala|powder|cumin|coriander|turmeric|chili|pepper|salt|cardamom/', $ingredientLower)) {
            $spices[] = $ingredient;
        } elseif (preg_match('/milk|cream|yogurt|cheese|ghee|butter/', $ingredientLower)) {
            $dairy[] = $ingredient;
        } elseif (preg_match('/rice|flour|bread|wheat|gram/', $ingredientLower)) {
            $grains[] = $ingredient;
        }
    }
    
    echo "🥩 Proteins (" . count($proteinSources) . "): " . implode(', ', $proteinSources) . "\n";
    echo "🥬 Vegetables (" . count($vegetables) . "): " . implode(', ', $vegetables) . "\n";
    echo "🌶️ Spices (" . count($spices) . "): " . implode(', ', $spices) . "\n";
    echo "🥛 Dairy (" . count($dairy) . "): " . implode(', ', $dairy) . "\n";
    echo "🌾 Grains (" . count($grains) . "): " . implode(', ', $grains) . "\n\n";
}

function analyzeCategoriesAndUnits($recipes) {
    echo "📊 CATEGORIES AND UNITS ANALYSIS\n";
    echo "===============================\n";
    
    // Extract all units used
    $allUnits = [];
    foreach ($recipes as $recipe) {
        foreach ($recipe as $field => $value) {
            if (stripos($field, 'unit') !== false && !empty($value)) {
                $allUnits[] = strtolower(trim($value));
            }
        }
    }
    
    $unitCounts = array_count_values($allUnits);
    arsort($unitCounts);
    
    echo "📏 UNITS USAGE FREQUENCY:\n";
    echo "========================\n";
    foreach ($unitCounts as $unit => $count) {
        echo "{$unit}: {$count} times\n";
    }
    
    echo "\n🏷️ RECOMMENDED UNIT STANDARDIZATION:\n";
    echo "===================================\n";
    
    $unitMapping = [
        'weight' => ['kg', 'g', 'lbs', 'oz'],
        'volume' => ['liter', 'ml', 'cup', 'tbsp', 'tsp'],
        'pieces' => ['piece', 'pcs', 'whole', 'slice'],
        'packets' => ['packet', 'pack', 'bag', 'box']
    ];
    
    foreach ($unitMapping as $category => $units) {
        $found = array_intersect($units, array_keys($unitCounts));
        if (!empty($found)) {
            echo "📦 {$category}: " . implode(', ', $found) . "\n";
        }
    }
    echo "\n";
}

function generateOptimizationSuggestions($recipes) {
    echo "💡 OPTIMIZATION SUGGESTIONS\n";
    echo "===========================\n";
    
    // Cost analysis opportunities
    echo "💰 COST OPTIMIZATION OPPORTUNITIES:\n";
    echo "==================================\n";
    echo "1. Ingredient consolidation: Look for recipes using similar ingredients\n";
    echo "2. Bulk purchasing: Identify high-frequency ingredients for bulk orders\n";
    echo "3. Seasonal planning: Match recipes with seasonal ingredient availability\n";
    echo "4. Waste reduction: Find recipes that can share prep ingredients\n\n";
    
    // Menu engineering suggestions
    echo "📋 MENU ENGINEERING SUGGESTIONS:\n";
    echo "===============================\n";
    echo "1. Recipe complexity analysis: Balance difficult vs easy recipes\n";
    echo "2. Prep time optimization: Group recipes by preparation requirements\n";
    echo "3. Equipment utilization: Match recipes with available cooking equipment\n";
    echo "4. Staff skill requirements: Align recipe complexity with chef capabilities\n\n";
    
    // Inventory management
    echo "📦 INVENTORY MANAGEMENT INSIGHTS:\n";
    echo "===============================\n";
    echo "1. Safety stock levels: Calculate based on recipe frequency\n";
    echo "2. Lead time planning: Account for ingredient procurement times\n";
    echo "3. Shelf life tracking: Priority ordering for perishable ingredients\n";
    echo "4. Cross-utilization: Maximize ingredient usage across multiple recipes\n\n";
}

// Execute analysis
echo "🚀 Starting comprehensive recipes analysis...\n\n";
$recipes = analyzeRecipesData();
echo "✅ Recipes analysis complete!\n";

?>