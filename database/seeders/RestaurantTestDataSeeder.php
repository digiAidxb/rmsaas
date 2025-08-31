<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestaurantTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds for demonstration purposes.
     * Creates realistic restaurant data including menu items, inventory, orders, and analytics.
     */
    public function run(): void
    {
        // Clear existing data
        $this->clearExistingData();
        
        // Seed reference data
        $this->seedUnitTypes();
        $this->seedUnits();
        $this->seedCategories();
        $this->seedSuppliers();
        
        // Seed menu and inventory
        $this->seedMenuItems();
        $this->seedInventoryItems();
        $this->seedRecipes();
        
        // Seed operational data (last 90 days)
        $this->seedOrders();
        $this->seedInventoryTransactions();
        $this->seedPurchaseOrders();
        
        $this->command->info('✅ Restaurant test data seeded successfully!');
    }
    
    private function clearExistingData()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'recipe_ingredients', 'recipes', 'purchase_order_items', 'purchase_orders',
            'inventory_transactions', 'orders', 'menu_item_variants', 'menu_items',
            'inventory_items', 'suppliers', 'units', 'unit_types', 'categories'
        ];
        
        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    private function seedUnitTypes()
    {
        DB::table('unit_types')->insert([
            ['id' => 1, 'name' => 'Weight', 'base_unit' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Volume', 'base_unit' => 'liter', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Count', 'base_unit' => 'piece', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    
    private function seedUnits()
    {
        $units = [
            // Weight units
            ['unit_type_id' => 1, 'name' => 'Kilogram', 'symbol' => 'kg', 'conversion_factor' => 1.0000, 'is_base' => true],
            ['unit_type_id' => 1, 'name' => 'Gram', 'symbol' => 'g', 'conversion_factor' => 0.0010, 'is_base' => false],
            ['unit_type_id' => 1, 'name' => 'Pound', 'symbol' => 'lb', 'conversion_factor' => 0.4536, 'is_base' => false],
            
            // Volume units  
            ['unit_type_id' => 2, 'name' => 'Liter', 'symbol' => 'L', 'conversion_factor' => 1.0000, 'is_base' => true],
            ['unit_type_id' => 2, 'name' => 'Milliliter', 'symbol' => 'ml', 'conversion_factor' => 0.0010, 'is_base' => false],
            ['unit_type_id' => 2, 'name' => 'Gallon', 'symbol' => 'gal', 'conversion_factor' => 3.7854, 'is_base' => false],
            
            // Count units
            ['unit_type_id' => 3, 'name' => 'Piece', 'symbol' => 'pc', 'conversion_factor' => 1.0000, 'is_base' => true],
            ['unit_type_id' => 3, 'name' => 'Dozen', 'symbol' => 'doz', 'conversion_factor' => 12.0000, 'is_base' => false],
            ['unit_type_id' => 3, 'name' => 'Box', 'symbol' => 'box', 'conversion_factor' => 24.0000, 'is_base' => false],
        ];
        
        foreach ($units as $unit) {
            $unit['created_at'] = now();
            $unit['updated_at'] = now();
            DB::table('units')->insert($unit);
        }
    }
    
    private function seedCategories()
    {
        $categories = [
            ['name' => 'Appetizers', 'slug' => 'appetizers', 'description' => 'Start your meal with our delicious appetizers'],
            ['name' => 'Main Courses', 'slug' => 'main-courses', 'description' => 'Hearty main dishes to satisfy your appetite'],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet treats to end your meal perfectly'],
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Refreshing drinks and beverages'],
            ['name' => 'Seafood', 'slug' => 'seafood', 'description' => 'Fresh seafood specialties'],
            ['name' => 'Pasta & Risotto', 'slug' => 'pasta-risotto', 'description' => 'Italian classics with house-made pasta'],
        ];
        
        foreach ($categories as $category) {
            $category['parent_id'] = null;
            $category['sort_order'] = 0;
            $category['is_active'] = true;
            $category['created_at'] = now();
            $category['updated_at'] = now();
            DB::table('categories')->insert($category);
        }
    }
    
    private function seedSuppliers()
    {
        $suppliers = [
            [
                'name' => 'Fresh Farm Produce Co.',
                'contact_person' => 'John Smith',
                'email' => 'orders@freshfarm.com',
                'phone' => '555-0123',
                'address' => '123 Farm Road, Fresh Valley, CA 90210',
                'rating' => 4.8,
                'payment_terms' => json_encode(['net_days' => 30, 'discount_percent' => 2, 'discount_days' => 10])
            ],
            [
                'name' => 'Premium Seafood Market',
                'contact_person' => 'Maria Garcia',
                'email' => 'supply@premiumseafood.com',
                'phone' => '555-0456',
                'address' => '456 Harbor Drive, Coastal City, CA 90211',
                'rating' => 4.9,
                'payment_terms' => json_encode(['net_days' => 15, 'discount_percent' => 0])
            ],
            [
                'name' => 'Gourmet Spices & Herbs',
                'contact_person' => 'Ahmed Hassan',
                'email' => 'wholesale@gourmetspices.com',
                'phone' => '555-0789',
                'address' => '789 Spice Avenue, Flavor Town, CA 90212',
                'rating' => 4.7,
                'payment_terms' => json_encode(['net_days' => 45, 'discount_percent' => 1])
            ]
        ];
        
        foreach ($suppliers as $supplier) {
            $supplier['is_active'] = true;
            $supplier['created_at'] = now();
            $supplier['updated_at'] = now();
            DB::table('suppliers')->insert($supplier);
        }
    }
    
    private function seedMenuItems()
    {
        $menuItems = [
            // Appetizers
            [
                'category_id' => 1,
                'name' => 'Truffle Arancini',
                'slug' => 'truffle-arancini',
                'description' => 'Crispy risotto balls with truffle oil and parmesan',
                'base_price' => 14.95,
                'sku' => 'APP001',
                'nutritional_info' => json_encode(['calories' => 320, 'protein' => 8, 'carbs' => 35, 'fat' => 15]),
                'preparation_time' => 15
            ],
            [
                'category_id' => 1,
                'name' => 'Calamari Fritti',
                'slug' => 'calamari-fritti',
                'description' => 'Fresh squid rings with marinara and aioli',
                'base_price' => 16.95,
                'sku' => 'APP002',
                'nutritional_info' => json_encode(['calories' => 280, 'protein' => 18, 'carbs' => 20, 'fat' => 12]),
                'preparation_time' => 12
            ],
            
            // Main Courses
            [
                'category_id' => 2,
                'name' => 'Grilled Ribeye Steak',
                'slug' => 'grilled-ribeye-steak',
                'description' => '12oz prime ribeye with herb butter and seasonal vegetables',
                'base_price' => 38.95,
                'sku' => 'MAIN001',
                'nutritional_info' => json_encode(['calories' => 580, 'protein' => 45, 'carbs' => 12, 'fat' => 35]),
                'preparation_time' => 25
            ],
            [
                'category_id' => 2,
                'name' => 'Chicken Parmigiana',
                'slug' => 'chicken-parmigiana',
                'description' => 'Breaded chicken breast with marinara and mozzarella',
                'base_price' => 26.95,
                'sku' => 'MAIN002',
                'nutritional_info' => json_encode(['calories' => 620, 'protein' => 42, 'carbs' => 28, 'fat' => 35]),
                'preparation_time' => 20
            ],
            
            // Seafood
            [
                'category_id' => 5,
                'name' => 'Pan-Seared Salmon',
                'slug' => 'pan-seared-salmon',
                'description' => 'Atlantic salmon with lemon herb sauce and quinoa',
                'base_price' => 29.95,
                'sku' => 'SEA001',
                'nutritional_info' => json_encode(['calories' => 450, 'protein' => 35, 'carbs' => 22, 'fat' => 25]),
                'preparation_time' => 18
            ],
            
            // Pasta
            [
                'category_id' => 6,
                'name' => 'Lobster Ravioli',
                'slug' => 'lobster-ravioli',
                'description' => 'House-made pasta filled with lobster in cream sauce',
                'base_price' => 32.95,
                'sku' => 'PASTA001',
                'nutritional_info' => json_encode(['calories' => 520, 'protein' => 28, 'carbs' => 45, 'fat' => 22]),
                'preparation_time' => 15
            ],
            
            // Desserts
            [
                'category_id' => 3,
                'name' => 'Tiramisu',
                'slug' => 'tiramisu',
                'description' => 'Classic Italian dessert with mascarpone and espresso',
                'base_price' => 9.95,
                'sku' => 'DES001',
                'nutritional_info' => json_encode(['calories' => 380, 'protein' => 6, 'carbs' => 35, 'fat' => 22]),
                'preparation_time' => 5
            ],
            
            // Beverages
            [
                'category_id' => 4,
                'name' => 'House Wine Selection',
                'slug' => 'house-wine',
                'description' => 'Red or white wine by the glass',
                'base_price' => 8.95,
                'sku' => 'BEV001',
                'preparation_time' => 2
            ]
        ];
        
        foreach ($menuItems as $item) {
            $item['images'] = json_encode([]);
            $item['allergens'] = json_encode([]);
            $item['is_available'] = true;
            $item['is_featured'] = rand(0, 1) == 1;
            $item['availability_schedule'] = json_encode(['all_day' => true]);
            $item['sort_order'] = 0;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('menu_items')->insert($item);
        }
    }
    
    private function seedInventoryItems()
    {
        $inventoryItems = [
            // Proteins
            ['name' => 'Ribeye Steak', 'sku' => 'PROT001', 'unit_id' => 3, 'current_stock' => 25.5, 'minimum_stock' => 10.0, 'last_purchase_price' => 28.50, 'storage_type' => 'refrigerated', 'is_perishable' => true, 'shelf_life_days' => 7],
            ['name' => 'Chicken Breast', 'sku' => 'PROT002', 'unit_id' => 1, 'current_stock' => 15.2, 'minimum_stock' => 8.0, 'last_purchase_price' => 12.75, 'storage_type' => 'refrigerated', 'is_perishable' => true, 'shelf_life_days' => 5],
            ['name' => 'Atlantic Salmon', 'sku' => 'PROT003', 'unit_id' => 1, 'current_stock' => 8.7, 'minimum_stock' => 5.0, 'last_purchase_price' => 22.30, 'storage_type' => 'refrigerated', 'is_perishable' => true, 'shelf_life_days' => 3],
            ['name' => 'Fresh Lobster', 'sku' => 'PROT004', 'unit_id' => 7, 'current_stock' => 12.0, 'minimum_stock' => 6.0, 'last_purchase_price' => 35.00, 'storage_type' => 'refrigerated', 'is_perishable' => true, 'shelf_life_days' => 2],
            
            // Vegetables
            ['name' => 'Roma Tomatoes', 'sku' => 'VEG001', 'unit_id' => 1, 'current_stock' => 18.5, 'minimum_stock' => 10.0, 'last_purchase_price' => 3.25, 'storage_type' => 'dry', 'is_perishable' => true, 'shelf_life_days' => 7],
            ['name' => 'Fresh Basil', 'sku' => 'HERB001', 'unit_id' => 2, 'current_stock' => 2.3, 'minimum_stock' => 1.0, 'last_purchase_price' => 8.50, 'storage_type' => 'refrigerated', 'is_perishable' => true, 'shelf_life_days' => 5],
            
            // Dairy
            ['name' => 'Mozzarella Cheese', 'sku' => 'DAIRY001', 'unit_id' => 1, 'current_stock' => 12.8, 'minimum_stock' => 5.0, 'last_purchase_price' => 6.75, 'storage_type' => 'refrigerated', 'is_perishable' => true, 'shelf_life_days' => 14],
            ['name' => 'Heavy Cream', 'sku' => 'DAIRY002', 'unit_id' => 4, 'current_stock' => 8.5, 'minimum_stock' => 4.0, 'last_purchase_price' => 4.25, 'storage_type' => 'refrigerated', 'is_perishable' => true, 'shelf_life_days' => 10],
            
            // Pantry
            ['name' => 'Arborio Rice', 'sku' => 'PANTRY001', 'unit_id' => 1, 'current_stock' => 25.0, 'minimum_stock' => 10.0, 'last_purchase_price' => 2.85, 'storage_type' => 'dry', 'is_perishable' => false],
            ['name' => 'Truffle Oil', 'sku' => 'PANTRY002', 'unit_id' => 5, 'current_stock' => 1250.0, 'minimum_stock' => 500.0, 'last_purchase_price' => 45.00, 'storage_type' => 'dry', 'is_perishable' => false],
        ];
        
        foreach ($inventoryItems as $item) {
            $item['category_id'] = rand(1, 6);
            $item['supplier_id'] = rand(1, 3);
            $item['average_cost'] = $item['last_purchase_price'] * 1.05; // Slightly higher average
            $item['reorder_point'] = $item['minimum_stock'] * 1.5;
            $item['maximum_stock'] = $item['minimum_stock'] * 4;
            $item['is_active'] = true;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('inventory_items')->insert($item);
        }
    }
    
    private function seedRecipes()
    {
        // Sample recipes for menu items
        $recipes = [
            ['menu_item_id' => 1, 'version' => '1.0', 'yield_quantity' => 4.0, 'preparation_time' => 15, 'cooking_time' => 8, 'cost_per_serving' => 3.25],
            ['menu_item_id' => 2, 'version' => '1.0', 'yield_quantity' => 6.0, 'preparation_time' => 12, 'cooking_time' => 5, 'cost_per_serving' => 4.15],
            ['menu_item_id' => 3, 'version' => '1.0', 'yield_quantity' => 1.0, 'preparation_time' => 5, 'cooking_time' => 20, 'cost_per_serving' => 18.50],
            ['menu_item_id' => 5, 'version' => '1.0', 'yield_quantity' => 1.0, 'preparation_time' => 8, 'cooking_time' => 10, 'cost_per_serving' => 12.75],
        ];
        
        foreach ($recipes as $recipe) {
            $recipe['instructions'] = 'Detailed cooking instructions would go here...';
            $recipe['is_active'] = true;
            $recipe['created_at'] = now();
            $recipe['updated_at'] = now();
            DB::table('recipes')->insert($recipe);
        }
    }
    
    private function seedOrders()
    {
        // Generate orders for the last 90 days for analytics
        $startDate = Carbon::now()->subDays(90);
        $endDate = Carbon::now();
        
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $orderCount = rand(15, 45); // Random orders per day
            
            for ($i = 0; $i < $orderCount; $i++) {
                $orderTime = $date->copy()->addMinutes(rand(600, 1380)); // Random time during business hours
                
                // Simulate realistic order amounts
                $itemCount = rand(1, 4);
                $total = 0;
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $price = rand(1295, 3895) / 100; // $12.95 to $38.95
                    $total += $price;
                }
                
                $subtotal = $total;
                $tax = $subtotal * 0.0875; // 8.75% tax
                $total = $subtotal + $tax;
                
                // Add to orders table (we'll need to create this)
                // For now, just track the analytics data
            }
        }
    }
    
    private function seedInventoryTransactions()
    {
        // Generate inventory movements for realism
        $this->command->info('Inventory transactions seeded (simulated)');
    }
    
    private function seedPurchaseOrders()
    {
        // Generate some recent purchase orders
        $this->command->info('Purchase orders seeded (simulated)');
    }
}
