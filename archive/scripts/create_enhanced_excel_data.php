<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Create enhanced recipes and inventory Excel files with comprehensive data
 * covering all menu items, proper categorization, and real-world units
 */

function createEnhancedRecipesFile() {
    echo "👨‍🍳 CREATING ENHANCED RECIPES MASTER FILE\n";
    echo "=========================================\n\n";

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Enhanced Recipes Master');

    // Enhanced headers with more comprehensive fields
    $headers = [
        'A1' => 'Recipe Code',
        'B1' => 'Recipe Name', 
        'C1' => 'Menu Category',
        'D1' => 'Menu Subcategory',
        'E1' => 'Cuisine Type',
        'F1' => 'Portion Size',
        'G1' => 'Servings',
        'H1' => 'Prep Time (min)',
        'I1' => 'Cook Time (min)',
        'J1' => 'Total Time (min)',
        'K1' => 'Difficulty',
        'L1' => 'Cost Category',
        'M1' => 'Ingredient Code',
        'N1' => 'Ingredient Name',
        'O1' => 'Quantity',
        'P1' => 'Unit',
        'Q1' => 'Unit Cost (AED)',
        'R1' => 'Total Cost (AED)',
        'S1' => 'Preparation Method',
        'T1' => 'Is Optional',
        'U1' => 'Ingredient Category',
        'V1' => 'Nutritional Value',
        'W1' => 'Allergen Info',
        'X1' => 'Season Availability',
        'Y1' => 'Storage Requirements',
        'Z1' => 'Instructions',
        'AA1' => 'Chef Notes',
        'AB1' => 'Created Date',
        'AC1' => 'Last Modified'
    ];

    // Set headers
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }

    // Style headers
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563eb']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:AC1')->applyFromArray($headerStyle);

    // Comprehensive recipe data covering GHORKA RESTAURANT menu
    $recipeData = [
        // CHICKEN MOMO (Enhanced with more ingredients)
        ['RCP001', 'CHICKEN MOMO', 'NEPALI FOOD', 'STEAMED DUMPLINGS', 'Nepali', '10 pieces', 1, 45, 20, 65, 'Medium', 'Premium', 'INV010', 'Momo Flour', 0.3, 'KG', 6.5, 1.95, 'Sift and knead', 'No', 'Starch', 'High Carbs', 'Gluten', 'Year-round', 'Dry Storage', 'Make smooth dough with warm water', 'Use lukewarm water for best texture', '2025-09-02', '2025-09-02'],
        ['RCP001', 'CHICKEN MOMO', 'NEPALI FOOD', 'STEAMED DUMPLINGS', 'Nepali', '10 pieces', 1, 45, 20, 65, 'Medium', 'Premium', 'INV001', 'Chicken Breast', 0.25, 'KG', 18.5, 4.63, 'Mince finely', 'No', 'Protein', 'High Protein', 'None', 'Year-round', 'Frozen', 'Add to spice mixture', 'Fresh mince gives better texture', '2025-09-02', '2025-09-02'],
        ['RCP001', 'CHICKEN MOMO', 'NEPALI FOOD', 'STEAMED DUMPLINGS', 'Nepali', '10 pieces', 1, 45, 20, 65, 'Medium', 'Premium', 'INV021', 'Onions', 0.05, 'KG', 3.2, 0.16, 'Dice finely', 'No', 'Vegetable', 'Low Calories', 'None', 'Year-round', 'Cool Dry', 'Add for flavor base', 'Sweat first to reduce moisture', '2025-09-02', '2025-09-02'],
        ['RCP001', 'CHICKEN MOMO', 'NEPALI FOOD', 'STEAMED DUMPLINGS', 'Nepali', '10 pieces', 1, 45, 20, 65, 'Medium', 'Premium', 'INV023', 'Ginger', 0.01, 'KG', 22.5, 0.23, 'Grate fresh', 'No', 'Spice', 'Digestive Aid', 'None', 'Year-round', 'Refrigerated', 'Essential for authentic flavor', 'Use fresh ginger only', '2025-09-02', '2025-09-02'],
        ['RCP001', 'CHICKEN MOMO', 'NEPALI FOOD', 'STEAMED DUMPLINGS', 'Nepali', '10 pieces', 1, 45, 20, 65, 'Medium', 'Premium', 'INV022', 'Garlic', 0.01, 'KG', 18, 0.18, 'Mince fine', 'No', 'Spice', 'Antioxidants', 'None', 'Year-round', 'Cool Dry', 'Adds aromatic depth', 'Avoid burning during cooking', '2025-09-02', '2025-09-02'],
        ['RCP001', 'CHICKEN MOMO', 'NEPALI FOOD', 'STEAMED DUMPLINGS', 'Nepali', '10 pieces', 1, 45, 20, 65, 'Medium', 'Premium', 'INV033', 'Coriander Powder', 0.005, 'KG', 25, 0.13, 'Sprinkle', 'No', 'Spice', 'Flavor Enhancer', 'None', 'Year-round', 'Airtight Container', 'Ground fresh for best aroma', 'Store in cool place', '2025-09-02', '2025-09-02'],
        
        // CHICKEN BIRYANI (New comprehensive recipe)
        ['RCP002', 'CHICKEN BIRYANI', 'NEPALI FOOD', 'RICE DISHES', 'South Asian', '500g', 4, 30, 45, 75, 'Hard', 'Premium', 'INV006', 'Basmati Rice', 0.5, 'KG', 8.5, 4.25, 'Wash and soak', 'No', 'Starch', 'High Carbs', 'None', 'Year-round', 'Dry Storage', 'Soak for 30 mins before cooking', 'Use aged basmati for best results', '2025-09-02', '2025-09-02'],
        ['RCP002', 'CHICKEN BIRYANI', 'NEPALI FOOD', 'RICE DISHES', 'South Asian', '500g', 4, 30, 45, 75, 'Hard', 'Premium', 'INV001', 'Chicken Breast', 0.6, 'KG', 18.5, 11.1, 'Cut in chunks', 'No', 'Protein', 'High Protein', 'None', 'Year-round', 'Frozen', 'Marinate with yogurt', 'Bone-in pieces give more flavor', '2025-09-02', '2025-09-02'],
        ['RCP002', 'CHICKEN BIRYANI', 'NEPALI FOOD', 'RICE DISHES', 'South Asian', '500g', 4, 30, 45, 75, 'Hard', 'Premium', 'INV048', 'Yogurt', 0.1, 'KG', 12.5, 1.25, 'Whisk smooth', 'No', 'Dairy', 'Probiotics', 'Dairy', 'Year-round', 'Refrigerated', 'For marination', 'Use thick yogurt', '2025-09-02', '2025-09-02'],
        ['RCP002', 'CHICKEN BIRYANI', 'NEPALI FOOD', 'RICE DISHES', 'South Asian', '500g', 4, 30, 45, 75, 'Hard', 'Premium', 'INV041', 'Garam Masala', 0.01, 'KG', 45, 0.45, 'Sprinkle', 'No', 'Spice', 'Aromatic Blend', 'None', 'Year-round', 'Airtight Container', 'Add at end for aroma', 'Fresh ground is best', '2025-09-02', '2025-09-02'],
        ['RCP002', 'CHICKEN BIRYANI', 'NEPALI FOOD', 'RICE DISHES', 'South Asian', '500g', 4, 30, 45, 75, 'Hard', 'Premium', 'INV045', 'Ghee', 0.05, 'L', 65, 3.25, 'Heat gently', 'No', 'Fat', 'Healthy Fats', 'Dairy', 'Year-round', 'Room Temperature', 'For authentic flavor', 'Use pure ghee only', '2025-09-02', '2025-09-02'],
        
        // MUTTON CURRY (New recipe)
        ['RCP003', 'MUTTON CURRY', 'NEPALI FOOD', 'CURRY DISHES', 'South Asian', '400g', 3, 20, 90, 110, 'Hard', 'Premium', 'INV003', 'Lamb Shoulder', 0.5, 'KG', 45.8, 22.9, 'Cut in cubes', 'No', 'Protein', 'High Protein', 'None', 'Year-round', 'Frozen', 'Slow cook for tenderness', 'Bone-in for flavor', '2025-09-02', '2025-09-02'],
        ['RCP003', 'MUTTON CURRY', 'NEPALI FOOD', 'CURRY DISHES', 'South Asian', '400g', 3, 20, 90, 110, 'Hard', 'Premium', 'INV021', 'Onions', 0.15, 'KG', 3.2, 0.48, 'Slice thin', 'No', 'Vegetable', 'Low Calories', 'None', 'Year-round', 'Cool Dry', 'Caramelize for base', 'Cook until golden brown', '2025-09-02', '2025-09-02'],
        ['RCP003', 'MUTTON CURRY', 'NEPALI FOOD', 'CURRY DISHES', 'South Asian', '400g', 3, 20, 90, 110, 'Hard', 'Premium', 'INV024', 'Tomatoes', 0.1, 'KG', 4.5, 0.45, 'Chop rough', 'No', 'Vegetable', 'Vitamin C', 'None', 'Year-round', 'Cool Place', 'Add for acidity', 'Use ripe tomatoes', '2025-09-02', '2025-09-02'],
        ['RCP003', 'MUTTON CURRY', 'NEPALI FOOD', 'CURRY DISHES', 'South Asian', '400g', 3, 20, 90, 110, 'Hard', 'Premium', 'INV036', 'Turmeric Powder', 0.005, 'KG', 28.9, 0.14, 'Mix in', 'No', 'Spice', 'Anti-inflammatory', 'None', 'Year-round', 'Dark Container', 'For color and health', 'Stains easily', '2025-09-02', '2025-09-02'],
        
        // FISH CURRY (New recipe)
        ['RCP004', 'FISH CURRY', 'NEPALI FOOD', 'SEAFOOD', 'Coastal', '300g', 2, 15, 25, 40, 'Medium', 'Premium', 'INV004', 'Fish Fillet', 0.4, 'KG', 28.9, 11.56, 'Cut portions', 'No', 'Protein', 'Omega-3', 'Fish', 'Year-round', 'Frozen', 'Handle gently', 'Fresh fish preferred', '2025-09-02', '2025-09-02'],
        ['RCP004', 'FISH CURRY', 'NEPALI FOOD', 'SEAFOOD', 'Coastal', '300g', 2, 15, 25, 40, 'Medium', 'Premium', 'INV039', 'Coconut Milk', 0.2, 'L', 15.5, 3.1, 'Stir gently', 'No', 'Dairy Alternative', 'Healthy Fats', 'Tree Nuts', 'Year-round', 'Cool Place', 'For creamy texture', 'Use thick coconut milk', '2025-09-02', '2025-09-02'],
        ['RCP004', 'FISH CURRY', 'NEPALI FOOD', 'SEAFOOD', 'Coastal', '300g', 2, 15, 25, 40, 'Medium', 'Premium', 'INV030', 'Green Chilies', 0.02, 'KG', 12, 0.24, 'Slit lengthwise', 'Yes', 'Spice', 'Vitamin C', 'None', 'Year-round', 'Refrigerated', 'Adjust to taste', 'Remove seeds for less heat', '2025-09-02', '2025-09-02'],
        
        // DAL BHAT (Traditional Nepali)
        ['RCP005', 'DAL BHAT', 'NEPALI FOOD', 'TRADITIONAL SET', 'Nepali', '1 plate', 1, 20, 30, 50, 'Easy', 'Budget', 'INV052', 'Red Lentils', 0.1, 'KG', 8.2, 0.82, 'Wash and boil', 'No', 'Protein', 'Plant Protein', 'None', 'Year-round', 'Dry Storage', 'Main protein source', 'Pressure cook for speed', '2025-09-02', '2025-09-02'],
        ['RCP005', 'DAL BHAT', 'NEPALI FOOD', 'TRADITIONAL SET', 'Nepali', '1 plate', 1, 20, 30, 50, 'Easy', 'Budget', 'INV006', 'Basmati Rice', 0.15, 'KG', 8.5, 1.28, 'Boil separate', 'No', 'Starch', 'High Carbs', 'None', 'Year-round', 'Dry Storage', 'Staple accompaniment', 'Perfect rice to dal ratio', '2025-09-02', '2025-09-02'],
        ['RCP005', 'DAL BHAT', 'NEPALI FOOD', 'TRADITIONAL SET', 'Nepali', '1 plate', 1, 20, 30, 50, 'Easy', 'Budget', 'INV036', 'Turmeric Powder', 0.002, 'KG', 28.9, 0.06, 'Add while cooking', 'No', 'Spice', 'Anti-inflammatory', 'None', 'Year-round', 'Dark Container', 'Color and nutrition', 'Just a pinch needed', '2025-09-02', '2025-09-02']
    ];

    // Add data to sheet
    $row = 2;
    foreach ($recipeData as $data) {
        $col = 'A';
        foreach ($data as $value) {
            $sheet->setCellValue($col . $row, $value);
            $col++;
        }
        $row++;
    }

    // Auto-size columns
    foreach (range('A', 'AC') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Apply alternating row colors for better readability
    $alternateStyle = [
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8fafc']]
    ];
    
    for ($i = 2; $i <= $row - 1; $i += 2) {
        $sheet->getStyle('A' . $i . ':AC' . $i)->applyFromArray($alternateStyle);
    }

    // Save file
    $writer = new Xlsx($spreadsheet);
    $filename = 'rawdata/enhanced_recipes_master.xlsx';
    $writer->save($filename);
    
    echo "✅ Enhanced recipes file created: {$filename}\n";
    echo "📊 Total recipes: 5 complete recipes\n";
    echo "🥘 Total ingredients: " . ($row - 2) . " ingredient entries\n\n";
    
    return $filename;
}

function createEnhancedInventoryFile() {
    echo "📦 CREATING ENHANCED INVENTORY MASTER FILE\n";
    echo "==========================================\n\n";

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Enhanced Inventory Master');

    // Enhanced headers with comprehensive fields
    $headers = [
        'A1' => 'Code',
        'B1' => 'Item Name',
        'C1' => 'Category', 
        'D1' => 'Subcategory',
        'E1' => 'Brand',
        'F1' => 'Unit Type',
        'G1' => 'Primary Unit',
        'H1' => 'Alternative Unit',
        'I1' => 'Conversion Factor',
        'J1' => 'Current Stock',
        'K1' => 'Min Stock',
        'L1' => 'Max Stock',
        'M1' => 'Reorder Point',
        'N1' => 'Unit Cost (AED)',
        'O1' => 'Total Value (AED)',
        'P1' => 'Supplier',
        'Q1' => 'Supplier Code',
        'R1' => 'Lead Time (Days)',
        'S1' => 'Storage Location',
        'T1' => 'Storage Temperature',
        'U1' => 'Expiry Days',
        'V1' => 'Is Perishable',
        'W1' => 'Allergen Info',
        'X1' => 'Origin Country',
        'Y1' => 'Seasonality',
        'Z1' => 'ABC Classification',
        'AA1' => 'Quality Grade',
        'AB1' => 'Recipe Usage Count',
        'AC1' => 'Monthly Consumption',
        'AD1' => 'Last Purchase Date',
        'AE1' => 'Last Updated'
    ];

    // Set headers
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }

    // Style headers
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A1:AE1')->applyFromArray($headerStyle);

    // Comprehensive inventory data with real restaurant focus
    $inventoryData = [
        // PROTEINS - High priority items
        ['INV001', 'Chicken Breast', 'PROTEINS', 'Poultry', 'Al Watania', 'Weight', 'KG', 'G', 1000, 25, 10, 50, 15, 18.5, 462.5, 'Al Watania Poultry', 'AWP001', 2, 'Freezer', '-18°C', 3, 'Yes', 'None', 'UAE', 'Year-round', 'A', 'Grade A', 5, 45, '2025-08-30', '2025-09-02'],
        ['INV002', 'Chicken Thigh', 'PROTEINS', 'Poultry', 'Al Watania', 'Weight', 'KG', 'G', 1000, 20, 8, 40, 12, 16.2, 324, 'Al Watania Poultry', 'AWP002', 2, 'Freezer', '-18°C', 3, 'Yes', 'None', 'UAE', 'Year-round', 'A', 'Grade A', 3, 25, '2025-08-30', '2025-09-02'],
        ['INV003', 'Lamb Shoulder', 'PROTEINS', 'Red Meat', 'Emirates Prime', 'Weight', 'KG', 'G', 1000, 15, 5, 30, 8, 45.8, 687, 'Emirates Meat', 'EM003', 3, 'Freezer', '-18°C', 5, 'Yes', 'None', 'Australia', 'Year-round', 'A', 'Premium', 2, 18, '2025-08-28', '2025-09-02'],
        ['INV004', 'Fish Fillet', 'PROTEINS', 'Seafood', 'Fresh Catch', 'Weight', 'KG', 'G', 1000, 12, 3, 25, 5, 28.9, 346.8, 'Dubai Fish Market', 'DFM001', 1, 'Refrigerator', '2-4°C', 1, 'Yes', 'Fish', 'Oman', 'Year-round', 'A', 'Grade A', 2, 15, '2025-09-01', '2025-09-02'],
        ['INV005', 'Eggs', 'PROTEINS', 'Dairy', 'Al Ain Fresh', 'Count', 'TRAY', 'PC', 30, 10, 3, 20, 5, 15.5, 155, 'Al Ain Farms', 'AAF001', 2, 'Refrigerator', '2-4°C', 14, 'Yes', 'Eggs', 'UAE', 'Year-round', 'B', 'Grade A', 4, 25, '2025-08-31', '2025-09-02'],

        // VEGETABLES - Fresh produce
        ['INV021', 'Onions', 'VEGETABLES', 'Root Vegetables', 'Local Fresh', 'Weight', 'KG', 'G', 1000, 30, 15, 60, 20, 3.2, 96, 'Local Farms', 'LF001', 1, 'Cool Storage', '10-15°C', 21, 'Yes', 'None', 'UAE', 'Year-round', 'B', 'Grade A', 8, 65, '2025-09-01', '2025-09-02'],
        ['INV022', 'Garlic', 'VEGETABLES', 'Aromatics', 'China Premium', 'Weight', 'KG', 'G', 1000, 8, 3, 15, 5, 18, 144, 'China Imports', 'CI001', 5, 'Cool Storage', '10-15°C', 30, 'Yes', 'None', 'China', 'Year-round', 'B', 'Premium', 6, 12, '2025-08-29', '2025-09-02'],
        ['INV023', 'Ginger', 'VEGETABLES', 'Aromatics', 'Fresh Origin', 'Weight', 'KG', 'G', 1000, 5, 2, 10, 3, 22.5, 112.5, 'India Imports', 'II001', 4, 'Cool Storage', '10-15°C', 21, 'Yes', 'None', 'India', 'Year-round', 'B', 'Premium', 5, 8, '2025-08-30', '2025-09-02'],
        ['INV024', 'Tomatoes', 'VEGETABLES', 'Fresh Vegetables', 'Hydroponic UAE', 'Weight', 'KG', 'G', 1000, 20, 8, 40, 12, 4.5, 90, 'Hydroponic Farms', 'HF001', 1, 'Cool Storage', '10-15°C', 7, 'Yes', 'None', 'UAE', 'Peak: Winter', 'B', 'Grade A', 4, 35, '2025-09-01', '2025-09-02'],
        ['INV025', 'Potatoes', 'VEGETABLES', 'Root Vegetables', 'Farm Fresh', 'Weight', 'KG', 'G', 1000, 50, 25, 100, 30, 2.5, 125, 'Local Farms', 'LF002', 2, 'Cool Storage', '8-12°C', 45, 'No', 'None', 'UAE', 'Year-round', 'C', 'Grade A', 3, 40, '2025-08-30', '2025-09-02'],

        // SPICES & SEASONINGS - Essential for Nepali cuisine
        ['INV030', 'Green Chilies', 'SPICES', 'Fresh Spices', 'Local Fresh', 'Weight', 'KG', 'G', 1000, 3, 1, 6, 2, 12, 36, 'Local Farms', 'LF003', 1, 'Refrigerator', '2-4°C', 10, 'Yes', 'None', 'UAE', 'Year-round', 'C', 'Grade A', 6, 8, '2025-09-01', '2025-09-02'],
        ['INV033', 'Coriander Powder', 'SPICES', 'Ground Spices', 'MDH', 'Weight', 'KG', 'G', 1000, 2, 1, 5, 1.5, 25, 50, 'MDH Spices', 'MDH001', 7, 'Dry Storage', 'Room Temp', 365, 'No', 'None', 'India', 'Year-round', 'B', 'Premium', 8, 3, '2025-08-25', '2025-09-02'],
        ['INV036', 'Turmeric Powder', 'SPICES', 'Ground Spices', 'Premium', 'Weight', 'KG', 'G', 1000, 1.5, 0.5, 3, 1, 28.9, 43.35, 'India Premium', 'IP001', 10, 'Dry Storage', 'Room Temp', 365, 'No', 'None', 'India', 'Year-round', 'B', 'Premium', 7, 2, '2025-08-20', '2025-09-02'],
        ['INV041', 'Garam Masala', 'SPICES', 'Spice Blends', 'Everest', 'Weight', 'KG', 'G', 1000, 1, 0.3, 2, 0.5, 45, 45, 'India Spice Co', 'ISC001', 14, 'Airtight Container', 'Room Temp', 180, 'No', 'None', 'India', 'Year-round', 'A', 'Premium', 9, 1.5, '2025-08-15', '2025-09-02'],

        // GRAINS & STAPLES
        ['INV006', 'Basmati Rice', 'GRAINS', 'Rice', 'India Gate', 'Weight', 'KG', 'G', 1000, 50, 25, 100, 35, 8.5, 425, 'Lulu Hypermarket', 'LH001', 5, 'Dry Storage', 'Room Temp', 365, 'No', 'None', 'India', 'Year-round', 'A', 'Premium', 6, 80, '2025-08-28', '2025-09-02'],
        ['INV010', 'Momo Flour', 'GRAINS', 'Specialty Flour', 'Nepal Origin', 'Weight', 'KG', 'G', 1000, 25, 10, 50, 15, 6.5, 162.5, 'Specialty Imports', 'SI001', 14, 'Dry Storage', 'Room Temp', 120, 'No', 'Gluten', 'Nepal', 'Year-round', 'A', 'Premium', 1, 20, '2025-08-25', '2025-09-02'],

        // DAIRY & FATS
        ['INV045', 'Ghee', 'DAIRY', 'Clarified Butter', 'Amul', 'Volume', 'L', 'ML', 1000, 5, 2, 10, 3, 65, 325, 'Amul Dairy', 'AD001', 3, 'Room Temperature', '20-25°C', 180, 'No', 'Dairy', 'India', 'Year-round', 'A', 'Premium', 5, 8, '2025-08-29', '2025-09-02'],
        ['INV048', 'Yogurt', 'DAIRY', 'Cultured Dairy', 'Al Rawabi', 'Weight', 'KG', 'G', 1000, 15, 5, 30, 8, 12.5, 187.5, 'Al Rawabi Dairy', 'ARD001', 1, 'Refrigerator', '2-4°C', 7, 'Yes', 'Dairy', 'UAE', 'Year-round', 'B', 'Grade A', 3, 25, '2025-09-01', '2025-09-02'],
        ['INV039', 'Coconut Milk', 'DAIRY ALTERNATIVES', 'Plant Milk', 'Chaokoh', 'Volume', 'L', 'ML', 1000, 10, 3, 20, 5, 15.5, 155, 'Thailand Imports', 'TI001', 7, 'Room Temperature', '20-25°C', 365, 'No', 'Tree Nuts', 'Thailand', 'Year-round', 'B', 'Premium', 2, 12, '2025-08-27', '2025-09-02'],

        // LEGUMES
        ['INV052', 'Red Lentils', 'LEGUMES', 'Dried Legumes', 'Organic', 'Weight', 'KG', 'G', 1000, 20, 10, 40, 15, 8.2, 164, 'India Imports', 'II002', 10, 'Dry Storage', 'Room Temp', 365, 'No', 'None', 'India', 'Year-round', 'B', 'Grade A', 1, 15, '2025-08-20', '2025-09-02'],

        // OILS & COOKING MEDIUMS
        ['INV043', 'Vegetable Oil', 'OILS', 'Cooking Oil', 'Al Arabi', 'Volume', 'L', 'ML', 1000, 20, 8, 40, 12, 4.8, 96, 'Al Arabi Trading', 'AAT001', 3, 'Room Temperature', '20-25°C', 365, 'No', 'None', 'UAE', 'Year-round', 'C', 'Standard', 7, 35, '2025-08-30', '2025-09-02'],

        // FROZEN ITEMS
        ['INV027', 'Frozen Peas', 'FROZEN', 'Frozen Vegetables', 'McCain', 'Weight', 'KG', 'G', 1000, 8, 3, 15, 5, 12.5, 100, 'McCain Foods', 'MF001', 5, 'Freezer', '-18°C', 365, 'No', 'None', 'Europe', 'Year-round', 'C', 'Grade A', 2, 10, '2025-08-28', '2025-09-02']
    ];

    // Add data to sheet
    $row = 2;
    foreach ($inventoryData as $data) {
        $col = 'A';
        foreach ($data as $value) {
            $sheet->setCellValue($col . $row, $value);
            $col++;
        }
        $row++;
    }

    // Auto-size columns
    foreach (range('A', 'AE') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Apply conditional formatting for stock levels
    $lowStockStyle = [
        'font' => ['color' => ['rgb' => 'dc2626']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']]
    ];
    
    // Apply alternating row colors
    $alternateStyle = [
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0fdf4']]
    ];
    
    for ($i = 2; $i <= $row - 1; $i += 2) {
        $sheet->getStyle('A' . $i . ':AE' . $i)->applyFromArray($alternateStyle);
    }

    // Save file
    $writer = new Xlsx($spreadsheet);
    $filename = 'rawdata/enhanced_inventory_master.xlsx';
    $writer->save($filename);
    
    echo "✅ Enhanced inventory file created: {$filename}\n";
    echo "📦 Total inventory items: " . ($row - 2) . " items\n";
    echo "🏷️ Categories covered: PROTEINS, VEGETABLES, SPICES, GRAINS, DAIRY, LEGUMES, OILS, FROZEN\n\n";
    
    return $filename;
}

// Execute file creation
echo "🚀 Starting enhanced Excel file creation...\n\n";

$recipesFile = createEnhancedRecipesFile();
$inventoryFile = createEnhancedInventoryFile();

echo "🎉 ENHANCED FILES CREATED SUCCESSFULLY!\n";
echo "======================================\n";
echo "📋 Recipes: {$recipesFile}\n";
echo "📦 Inventory: {$inventoryFile}\n\n";

echo "💡 KEY ENHANCEMENTS:\n";
echo "===================\n";
echo "✅ Comprehensive ingredient analysis\n";
echo "✅ Proper unit standardization (KG, L, PCS, TRAY)\n";
echo "✅ Real restaurant categories and subcategories\n";
echo "✅ Allergen and dietary information\n";
echo "✅ Storage requirements and shelf life\n";
echo "✅ Supplier and procurement details\n";
echo "✅ ABC classification for inventory optimization\n";
echo "✅ Recipe usage tracking\n";
echo "✅ Seasonal availability information\n";
echo "✅ Quality grades and brand information\n\n";

echo "🔄 NEXT STEPS:\n";
echo "=============\n";
echo "1. Test import with smart format detection\n";
echo "2. Validate unit conversions in database\n";
echo "3. Create summary report parser for existing data\n";
echo "4. Implement AI mapping optimization\n\n";

?>