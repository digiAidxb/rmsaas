<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-database {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and setup a tenant-specific database with schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        
        $this->info("Setting up tenant database for: {$domain}");
        
        // Find the tenant
        $tenant = DB::connection('landlord')
            ->table('tenants')
            ->where('domain', $domain)
            ->first();
            
        if (!$tenant) {
            $this->error("Tenant not found for domain: {$domain}");
            return 1;
        }
        
        try {
            // Create the database
            $this->info("Creating database: {$tenant->database}");
            DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$tenant->database}`");
            
            // Setup tenant-specific database connection
            config(["database.connections.tenant_temp" => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => $tenant->database,
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]]);
            
            // Clear connection cache
            DB::purge('tenant_temp');
            
            // Test connection
            DB::connection('tenant_temp')->select('SELECT 1 as test');
            $this->info("Database connection successful!");
            
            // Create basic tenant schema
            $this->info("Creating tenant schema...");
            $this->createTenantSchema($tenant->database);
            
            // Seed tenant with sample data
            $this->info("Seeding tenant data...");
            $this->seedTenantData($tenant);
            
            $this->info("✅ Tenant database setup complete for: {$domain}");
            
        } catch (\Exception $e) {
            $this->error("Failed to create tenant database: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function createTenantSchema($database)
    {
        // Create tenant-specific tables
        DB::connection('tenant_temp')->statement("
            CREATE TABLE IF NOT EXISTS `menu_categories` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `is_active` boolean DEFAULT true,
                `sort_order` int DEFAULT 0,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        DB::connection('tenant_temp')->statement("
            CREATE TABLE IF NOT EXISTS `menu_items` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `category_id` bigint unsigned,
                `name` varchar(255) NOT NULL,
                `description` text,
                `price` decimal(10,2) NOT NULL,
                `cost` decimal(10,2) DEFAULT NULL,
                `is_active` boolean DEFAULT true,
                `ingredients` json,
                `allergens` json,
                `nutritional_info` json,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `menu_items_category_id_foreign` (`category_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        DB::connection('tenant_temp')->statement("
            CREATE TABLE IF NOT EXISTS `inventory_items` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `sku` varchar(100) UNIQUE,
                `unit` varchar(50) NOT NULL,
                `current_stock` decimal(10,3) DEFAULT 0,
                `minimum_stock` decimal(10,3) DEFAULT 0,
                `maximum_stock` decimal(10,3) DEFAULT NULL,
                `unit_cost` decimal(10,2) DEFAULT NULL,
                `supplier_info` json,
                `storage_location` varchar(255),
                `expiry_date` date,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `inventory_items_sku_unique` (`sku`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        $this->info("Tenant schema created successfully!");
    }
    
    private function seedTenantData($tenant)
    {
        // Sample menu categories
        DB::connection('tenant_temp')->table('menu_categories')->insert([
            ['name' => 'Appetizers', 'description' => 'Start your meal with these delicious appetizers', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Main Courses', 'description' => 'Hearty main dishes to satisfy your hunger', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Desserts', 'description' => 'Sweet treats to end your meal', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Beverages', 'description' => 'Refreshing drinks and hot beverages', 'is_active' => true, 'sort_order' => 4],
        ]);
        
        // Sample menu items
        $categories = DB::connection('tenant_temp')->table('menu_categories')->get();
        
        foreach ($categories as $category) {
            switch ($category->name) {
                case 'Appetizers':
                    DB::connection('tenant_temp')->table('menu_items')->insert([
                        [
                            'category_id' => $category->id,
                            'name' => 'Caesar Salad',
                            'description' => 'Fresh romaine lettuce with caesar dressing, croutons, and parmesan cheese',
                            'price' => 12.99,
                            'cost' => 4.50,
                            'ingredients' => json_encode(['romaine lettuce', 'caesar dressing', 'croutons', 'parmesan cheese']),
                            'allergens' => json_encode(['dairy', 'gluten'])
                        ],
                        [
                            'category_id' => $category->id,
                            'name' => 'Chicken Wings',
                            'description' => 'Spicy buffalo wings served with ranch dressing',
                            'price' => 15.99,
                            'cost' => 6.25,
                            'ingredients' => json_encode(['chicken wings', 'buffalo sauce', 'ranch dressing']),
                            'allergens' => json_encode(['dairy'])
                        ]
                    ]);
                    break;
                    
                case 'Main Courses':
                    DB::connection('tenant_temp')->table('menu_items')->insert([
                        [
                            'category_id' => $category->id,
                            'name' => 'Grilled Salmon',
                            'description' => 'Atlantic salmon grilled to perfection with lemon herb butter',
                            'price' => 28.99,
                            'cost' => 12.50,
                            'ingredients' => json_encode(['salmon fillet', 'lemon', 'herbs', 'butter']),
                            'allergens' => json_encode(['fish', 'dairy'])
                        ]
                    ]);
                    break;
            }
        }
        
        // Sample inventory items
        DB::connection('tenant_temp')->table('inventory_items')->insert([
            [
                'name' => 'Salmon Fillet',
                'sku' => 'FISH-SALMON-001',
                'unit' => 'kg',
                'current_stock' => 25.5,
                'minimum_stock' => 10.0,
                'maximum_stock' => 50.0,
                'unit_cost' => 18.50,
                'supplier_info' => json_encode(['supplier' => 'Fresh Fish Co.', 'contact' => '+1-555-0199']),
                'storage_location' => 'Walk-in Freezer #1'
            ],
            [
                'name' => 'Romaine Lettuce',
                'sku' => 'VEG-LETTUCE-001',
                'unit' => 'heads',
                'current_stock' => 48.0,
                'minimum_stock' => 20.0,
                'maximum_stock' => 100.0,
                'unit_cost' => 1.25,
                'supplier_info' => json_encode(['supplier' => 'Green Valley Farms', 'contact' => '+1-555-0177']),
                'storage_location' => 'Walk-in Cooler'
            ],
            [
                'name' => 'Chicken Wings',
                'sku' => 'MEAT-WINGS-001',
                'unit' => 'kg',
                'current_stock' => 15.2,
                'minimum_stock' => 8.0,
                'maximum_stock' => 30.0,
                'unit_cost' => 8.75,
                'supplier_info' => json_encode(['supplier' => 'Premium Poultry', 'contact' => '+1-555-0188']),
                'storage_location' => 'Walk-in Freezer #2'
            ]
        ]);
        
        $this->info("Tenant sample data seeded successfully!");
    }
}
