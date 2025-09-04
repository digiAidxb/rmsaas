<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Multitenancy\Models\Tenant;

class OnboardingController extends Controller
{
    public function index()
    {
        $tenant = Tenant::current();
        
        // Check if onboarding is already completed or skipped
        if ($tenant->onboarding_completed_at || $tenant->skip_onboarding) {
            return redirect()->route('dashboard');
        }
        
        // Check for incomplete import processes
        $incompletImport = $this->checkForIncompleteImport($tenant);
        if ($incompletImport) {
            return $this->resumeIncompleteImport($incompletImport);
        }
        
        // Get current onboarding progress
        $progress = $this->getOnboardingProgress($tenant);
        
        // Import statistics for motivation
        $importStats = [
            'avg_setup_time' => '2.3 minutes',
            'success_rate' => '99.8%',
            'supported_pos' => 9,
            'ai_accuracy' => '94.5%'
        ];
        
        return Inertia::render('Onboarding/Index', [
            'tenant' => $tenant,
            'progress' => $progress,
            'importStats' => $importStats,
            'incompleteImport' => $incompletImport
        ]);
    }

    public function step($stepName)
    {
        $tenant = Tenant::current();
        
        if (!$tenant->onboarding_status) {
            $tenant->initializeOnboarding();
        }
        
        $progress = $tenant->getOnboardingProgress();
        $steps = $progress['steps'];
        
        if (!isset($steps[$stepName])) {
            return redirect()->route('onboarding.index');
        }
        
        $currentStep = $steps[$stepName];
        $stepIndex = array_search($stepName, array_keys($steps));
        
        return Inertia::render("Onboarding/Steps/{$this->getStepComponent($stepName)}", [
            'tenant' => $tenant,
            'progress' => $progress,
            'currentStep' => $currentStep,
            'stepName' => $stepName,
            'stepIndex' => $stepIndex,
            'totalSteps' => count($steps) - 1, // -1 to exclude 'completed' step
        ]);
    }

    public function complete(Request $request, $stepName)
    {
        $tenant = Tenant::current();
        
        $request->validate([
            'data' => 'sometimes|array'
        ]);
        
        // Process step-specific data
        $this->processStepData($stepName, $request->input('data', []), $tenant);
        
        // Mark step as completed
        $tenant->completeOnboardingStep($stepName);
        
        // Get next step
        $progress = $tenant->getOnboardingProgress();
        $steps = array_keys($progress['steps']);
        $currentIndex = array_search($stepName, $steps);
        
        if ($currentIndex !== false && $currentIndex + 1 < count($steps)) {
            $nextStep = $steps[$currentIndex + 1];
            
            // Skip 'completed' step if it's next
            if ($nextStep === 'completed') {
                return redirect()->route('dashboard')->with('success', 'Onboarding completed successfully! Welcome to RMSaaS!');
            }
            
            return redirect()->route('onboarding.step', $nextStep);
        }
        
        return redirect()->route('dashboard')->with('success', 'Onboarding completed successfully!');
    }

    public function skip(Request $request)
    {
        $tenant = Tenant::current();
        
        // Check if user wants demo data
        $loadDemoData = $request->input('load_demo_data', false);
        
        if ($loadDemoData) {
            try {
                // Load comprehensive demo data
                $this->loadComprehensiveDemoData($tenant);
                
                // Mark onboarding as completed with demo data
                $tenant->update([
                    'skip_onboarding' => false,
                    'onboarding_completed_at' => now(),
                    'settings' => array_merge($tenant->settings ?? [], [
                        'demo_data_loaded' => true,
                        'demo_data_loaded_at' => now()->toISOString(),
                        'onboarding_method' => 'demo_data'
                    ])
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Demo data loaded successfully! Welcome to RMSaaS!',
                        'redirect' => route('dashboard')
                    ]);
                }
                
                return redirect()->route('dashboard')->with([
                    'success' => 'Welcome to RMSaaS! Demo data has been loaded to help you explore the platform.',
                    'demo_mode' => true
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to load demo data: ' . $e->getMessage());
                
                // Still complete onboarding even if demo data fails
                $tenant->update([
                    'skip_onboarding' => true,
                    'onboarding_completed_at' => now()
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Onboarding completed, but demo data failed to load.',
                        'redirect' => route('dashboard')
                    ]);
                }
                
                return redirect()->route('dashboard')->with([
                    'warning' => 'Onboarding completed, but demo data failed to load. You can import your data from the dashboard.',
                ]);
            }
        }
        
        // Skip onboarding without demo data
        $tenant->update([
            'skip_onboarding' => true,
            'onboarding_completed_at' => now(),
            'settings' => array_merge($tenant->settings ?? [], [
                'onboarding_method' => 'skipped'
            ])
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Onboarding skipped successfully!',
                'redirect' => route('dashboard')
            ]);
        }
        
        return redirect()->route('dashboard')->with('info', 'Onboarding skipped. You can import your data and set up your restaurant from the dashboard.');
    }
    
    /**
     * Revolutionary Data Import Integration for Onboarding
     */
    public function importStep()
    {
        $tenant = Tenant::current();
        $progress = $tenant->getOnboardingProgress();
        
        // Import statistics for motivation
        $importStats = [
            'avg_setup_time' => '2.3 minutes',
            'success_rate' => '99.8%',
            'supported_pos' => 9,
            'ai_accuracy' => '94.5%'
        ];
        
        return view('tenant.onboarding.import', compact('tenant', 'progress', 'importStats'));
    }
    
    /**
     * Quick Import Setup - Streamlined for Onboarding
     */
    public function quickImportSetup(Request $request)
    {
        $tenant = Tenant::current();
        
        // Store setup preferences
        $settings = $tenant->settings ?? [];
        $settings['import_preference'] = $request->input('import_preference');
        $settings['pos_system'] = $request->input('pos_system');
        $settings['has_existing_data'] = $request->boolean('has_existing_data');
        $settings['onboarding_step'] = 'import_setup'; // Track progress step
        
        if ($request->input('import_preference') === 'now') {
            // Save progress and redirect to import
            $tenant->update([
                'settings' => array_merge($settings, [
                    'onboarding_method' => 'import_now',
                    'import_started' => true,
                    'import_started_at' => now()->toISOString()
                ])
            ]);
            
            // Set session to track that this import is from onboarding
            $request->session()->put('import_source', 'onboarding');
            $request->session()->put('import_context', 'onboarding');
            $request->session()->put('onboarding_import_preference', $request->input('import_preference'));
            
            return redirect()->route('imports.create')
                ->with('source', 'onboarding')
                ->with('import_context', 'onboarding')
                ->with('success', 'Let\'s import your restaurant data with AI precision!');
        } else {
            // Complete onboarding without immediate import
            $tenant->update([
                'skip_onboarding' => true,
                'onboarding_completed_at' => now(),
                'settings' => array_merge($settings, [
                    'onboarding_method' => 'import_later'
                ])
            ]);
            
            return redirect()->route('dashboard')
                ->with('info', 'Onboarding completed! You can import your data anytime from the dashboard.');
        }
    }

    private function getStepComponent($stepName): string
    {
        $componentMap = [
            'welcome' => 'Welcome',
            'business_info' => 'BusinessInfo',
            'data_import' => 'DataImport',
            'menu_setup' => 'MenuSetup',
            'inventory_setup' => 'InventorySetup',
            'staff_setup' => 'StaffSetup',
            'dashboard_tour' => 'DashboardTour',
        ];
        
        return $componentMap[$stepName] ?? 'Welcome';
    }

    private function processStepData($stepName, array $data, $tenant): void
    {
        switch ($stepName) {
            case 'business_info':
                // Update tenant settings with business information
                $settings = is_array($tenant->settings) ? $tenant->settings : [];
                $settings['business_info'] = $data;
                $tenant->settings = $settings;
                $tenant->save();
                break;
                
            case 'data_import':
                // Handle data import preferences
                $settings = is_array($tenant->settings) ? $tenant->settings : [];
                $settings['data_import'] = $data;
                $tenant->settings = $settings;
                $tenant->save();
                break;
                
            // Add more cases as needed for different steps
            default:
                // Store generic step data
                $settings = is_array($tenant->settings) ? $tenant->settings : [];
                $settings[$stepName] = $data;
                $tenant->settings = $settings;
                $tenant->save();
                break;
        }
    }

    /**
     * Load comprehensive demo data for restaurant exploration
     * Blessed by Lord Bhairava for complete restaurant data simulation
     */
    private function loadComprehensiveDemoData($tenant)
    {
        \DB::transaction(function () use ($tenant) {
            
            // 1. Load Categories (Hierarchical Structure)
            $this->createDemoCategories();
            
            // 2. Load Menu Items
            $this->createDemoMenuItems();
            
            // 3. Load Inventory Items
            $this->createDemoInventory();
            
            // 4. Load Recipes with Ingredients
            $this->createDemoRecipes();
            
            // 5. Load 7 Days of Sales Data
            $this->createDemoSalesData();
            
            // 6. Load Daily Reconciliations
            $this->createDemoReconciliations();
            
            // 7. Load Loss Analysis Data
            $this->createDemoLossAnalysis();
            
            \Log::info('Comprehensive demo data loaded successfully for tenant: ' . $tenant->id);
        });
    }
    
    /**
     * Create hierarchical categories for menu organization
     */
    private function createDemoCategories()
    {
        $categories = [
            // Main Categories
            [
                'name' => 'APPETIZERS',
                'code' => 'APP',
                'description' => 'Starters and small plates',
                'level' => 'main',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Hot Appetizers', 'code' => 'APP-HOT'],
                    ['name' => 'Cold Appetizers', 'code' => 'APP-COLD'],
                    ['name' => 'Sharing Plates', 'code' => 'APP-SHARE']
                ]
            ],
            [
                'name' => 'MAINS',
                'code' => 'MAIN',
                'description' => 'Main course dishes',
                'level' => 'main',
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Grilled Specialties', 'code' => 'MAIN-GRILL'],
                    ['name' => 'Pasta & Risotto', 'code' => 'MAIN-PASTA'],
                    ['name' => 'Seafood', 'code' => 'MAIN-SEA'],
                    ['name' => 'Vegetarian', 'code' => 'MAIN-VEG']
                ]
            ],
            [
                'name' => 'BEVERAGES',
                'code' => 'BEV',
                'description' => 'Drinks and beverages',
                'level' => 'main',
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Hot Beverages', 'code' => 'BEV-HOT'],
                    ['name' => 'Cold Beverages', 'code' => 'BEV-COLD'],
                    ['name' => 'Specialty Drinks', 'code' => 'BEV-SPEC'],
                    ['name' => 'Alcoholic', 'code' => 'BEV-ALC']
                ]
            ],
            [
                'name' => 'DESSERTS',
                'code' => 'DES',
                'description' => 'Sweet endings',
                'level' => 'main',
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Cakes & Tarts', 'code' => 'DES-CAKE'],
                    ['name' => 'Ice Cream', 'code' => 'DES-ICE'],
                    ['name' => 'Traditional Sweets', 'code' => 'DES-TRAD']
                ]
            ]
        ];
        
        foreach ($categories as $mainCat) {
            $children = $mainCat['children'] ?? [];
            unset($mainCat['children']);
            
            $parentCategory = \App\Models\Category::create($mainCat);
            
            foreach ($children as $childData) {
                \App\Models\Category::create([
                    'name' => $childData['name'],
                    'code' => $childData['code'],
                    'description' => 'Sub-category of ' . $mainCat['name'],
                    'parent_id' => $parentCategory->id,
                    'level' => 'sub',
                    'sort_order' => 1,
                    'is_active' => true
                ]);
            }
        }
    }
    
    /**
     * Create demo menu items with realistic pricing
     */
    private function createDemoMenuItems()
    {
        $menuItems = [
            // Appetizers
            ['name' => 'Truffle Arancini', 'price' => 16.50, 'category' => 'Hot Appetizers', 'description' => 'Crispy risotto balls with truffle oil and parmesan'],
            ['name' => 'Burrata Caprese', 'price' => 18.00, 'category' => 'Cold Appetizers', 'description' => 'Fresh burrata with heirloom tomatoes and basil'],
            ['name' => 'Charcuterie Board', 'price' => 28.00, 'category' => 'Sharing Plates', 'description' => 'Selection of cured meats, cheeses, and accompaniments'],
            
            // Mains
            ['name' => 'Ribeye Steak', 'price' => 48.00, 'category' => 'Grilled Specialties', 'description' => '12oz grass-fed ribeye with herb butter'],
            ['name' => 'Lobster Ravioli', 'price' => 32.00, 'category' => 'Pasta & Risotto', 'description' => 'House-made pasta filled with fresh lobster'],
            ['name' => 'Pan-Seared Salmon', 'price' => 29.00, 'category' => 'Seafood', 'description' => 'Atlantic salmon with lemon herb crust'],
            ['name' => 'Eggplant Parmigiana', 'price' => 22.00, 'category' => 'Vegetarian', 'description' => 'Layers of eggplant with marinara and mozzarella'],
            
            // Beverages
            ['name' => 'Espresso', 'price' => 3.50, 'category' => 'Hot Beverages', 'description' => 'Double shot of Italian espresso'],
            ['name' => 'Fresh Orange Juice', 'price' => 6.00, 'category' => 'Cold Beverages', 'description' => 'Freshly squeezed Valencia oranges'],
            ['name' => 'House Sangria', 'price' => 12.00, 'category' => 'Alcoholic', 'description' => 'Red wine with seasonal fruits'],
            
            // Desserts
            ['name' => 'Tiramisu', 'price' => 9.50, 'category' => 'Traditional Sweets', 'description' => 'Classic Italian dessert with mascarpone'],
            ['name' => 'Vanilla Bean Gelato', 'price' => 7.00, 'category' => 'Ice Cream', 'description' => 'House-made gelato with Madagascar vanilla']
        ];
        
        foreach ($menuItems as $item) {
            $category = \App\Models\Category::where('name', $item['category'])->first();
            
            if ($category) {
                \DB::table('menu_items')->insert([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'category_id' => $category->id,
                    'is_available' => true,
                    'preparation_time' => rand(10, 45),
                    'calories' => rand(200, 800),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    /**
     * Create demo inventory with realistic data
     */
    private function createDemoInventory()
    {
        $inventoryItems = [
            ['name' => 'Ribeye Steak', 'unit' => 'KG', 'cost_per_unit' => 35.00, 'stock' => 25, 'min_stock' => 5],
            ['name' => 'Fresh Salmon', 'unit' => 'KG', 'cost_per_unit' => 28.00, 'stock' => 18, 'min_stock' => 3],
            ['name' => 'Arborio Rice', 'unit' => 'KG', 'cost_per_unit' => 8.50, 'stock' => 45, 'min_stock' => 10],
            ['name' => 'Truffle Oil', 'unit' => 'L', 'cost_per_unit' => 85.00, 'stock' => 3, 'min_stock' => 1],
            ['name' => 'Parmesan Cheese', 'unit' => 'KG', 'cost_per_unit' => 45.00, 'stock' => 12, 'min_stock' => 2],
            ['name' => 'Fresh Basil', 'unit' => 'KG', 'cost_per_unit' => 15.00, 'stock' => 2, 'min_stock' => 0.5],
            ['name' => 'Extra Virgin Olive Oil', 'unit' => 'L', 'cost_per_unit' => 18.00, 'stock' => 8, 'min_stock' => 2],
            ['name' => 'San Marzano Tomatoes', 'unit' => 'KG', 'cost_per_unit' => 12.00, 'stock' => 20, 'min_stock' => 5],
            ['name' => 'Fresh Mozzarella', 'unit' => 'KG', 'cost_per_unit' => 22.00, 'stock' => 15, 'min_stock' => 3],
            ['name' => 'Coffee Beans', 'unit' => 'KG', 'cost_per_unit' => 25.00, 'stock' => 10, 'min_stock' => 2]
        ];
        
        foreach ($inventoryItems as $item) {
            \DB::table('inventory_items')->insert([
                'name' => $item['name'],
                'unit' => $item['unit'],
                'cost_per_unit' => $item['cost_per_unit'],
                'current_stock' => $item['stock'],
                'minimum_stock' => $item['min_stock'],
                'supplier' => 'Premium Food Suppliers',
                'category' => $this->getInventoryCategory($item['name']),
                'last_restocked_at' => now()->subDays(rand(1, 7)),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    
    /**
     * Get inventory category based on item name
     */
    private function getInventoryCategory($itemName)
    {
        $categories = [
            'meat' => ['Ribeye', 'Salmon'],
            'dairy' => ['Cheese', 'Mozzarella'],
            'produce' => ['Basil', 'Tomatoes'],
            'pantry' => ['Rice', 'Oil', 'Coffee'],
            'specialty' => ['Truffle']
        ];
        
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($itemName, $keyword) !== false) {
                    return ucfirst($category);
                }
            }
        }
        
        return 'General';
    }
    
    /**
     * Create demo recipes with ingredients and cost analysis
     */
    private function createDemoRecipes()
    {
        $recipes = [
            [
                'name' => 'Truffle Arancini Recipe',
                'menu_item' => 'Truffle Arancini',
                'yield_quantity' => 12,
                'yield_unit' => 'pieces',
                'prep_time' => 30,
                'cook_time' => 20,
                'ingredients' => [
                    ['name' => 'Arborio Rice', 'quantity' => 0.3, 'unit' => 'KG'],
                    ['name' => 'Parmesan Cheese', 'quantity' => 0.1, 'unit' => 'KG'],
                    ['name' => 'Truffle Oil', 'quantity' => 0.02, 'unit' => 'L']
                ]
            ],
            [
                'name' => 'Ribeye Steak Recipe',
                'menu_item' => 'Ribeye Steak',
                'yield_quantity' => 1,
                'yield_unit' => 'serving',
                'prep_time' => 10,
                'cook_time' => 15,
                'ingredients' => [
                    ['name' => 'Ribeye Steak', 'quantity' => 0.35, 'unit' => 'KG'],
                    ['name' => 'Extra Virgin Olive Oil', 'quantity' => 0.015, 'unit' => 'L']
                ]
            ],
            [
                'name' => 'Burrata Caprese Recipe',
                'menu_item' => 'Burrata Caprese',
                'yield_quantity' => 1,
                'yield_unit' => 'serving',
                'prep_time' => 8,
                'cook_time' => 0,
                'ingredients' => [
                    ['name' => 'Fresh Mozzarella', 'quantity' => 0.125, 'unit' => 'KG'],
                    ['name' => 'San Marzano Tomatoes', 'quantity' => 0.15, 'unit' => 'KG'],
                    ['name' => 'Fresh Basil', 'quantity' => 0.01, 'unit' => 'KG']
                ]
            ]
        ];
        
        foreach ($recipes as $recipeData) {
            $menuItem = \DB::table('menu_items')->where('name', $recipeData['menu_item'])->first();
            
            if ($menuItem) {
                $recipe = \DB::table('recipes')->insertGetId([
                    'name' => $recipeData['name'],
                    'menu_item_id' => $menuItem->id,
                    'yield_quantity' => $recipeData['yield_quantity'],
                    'yield_unit' => $recipeData['yield_unit'],
                    'prep_time_minutes' => $recipeData['prep_time'],
                    'cook_time_minutes' => $recipeData['cook_time'],
                    'total_time_minutes' => $recipeData['prep_time'] + $recipeData['cook_time'],
                    'instructions' => 'Detailed cooking instructions for ' . $recipeData['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                foreach ($recipeData['ingredients'] as $ingredient) {
                    $inventoryItem = \DB::table('inventory_items')->where('name', $ingredient['name'])->first();
                    
                    if ($inventoryItem) {
                        \DB::table('recipe_ingredients')->insert([
                            'recipe_id' => $recipe,
                            'inventory_item_id' => $inventoryItem->id,
                            'quantity' => $ingredient['quantity'],
                            'unit' => $ingredient['unit'],
                            'unit_cost' => $inventoryItem->cost_per_unit,
                            'total_cost' => $ingredient['quantity'] * $inventoryItem->cost_per_unit,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Create 7 days of realistic sales data
     */
    private function createDemoSalesData()
    {
        $menuItems = \DB::table('menu_items')->get();
        
        for ($day = 6; $day >= 0; $day--) {
            $date = now()->subDays($day);
            $isWeekend = $date->isWeekend();
            $dailyMultiplier = $isWeekend ? 1.4 : 1.0;
            
            foreach ($menuItems as $item) {
                $baseQuantity = $this->getBaseQuantityForItem($item->name);
                $quantity = max(1, intval($baseQuantity * $dailyMultiplier * (0.8 + (rand(0, 40) / 100))));
                $revenue = $quantity * $item->price;
                
                \DB::table('sales_transactions')->insert([
                    'menu_item_id' => $item->id,
                    'quantity_sold' => $quantity,
                    'unit_price' => $item->price,
                    'total_revenue' => $revenue,
                    'cost_of_goods' => $revenue * 0.35, // 35% COGS
                    'profit_margin' => $revenue * 0.65,
                    'transaction_date' => $date,
                    'day_part' => $this->getDayPart($item->name),
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            }
        }
    }
    
    /**
     * Get base daily quantity for menu items
     */
    private function getBaseQuantityForItem($itemName)
    {
        $popularItems = ['Tiramisu', 'Espresso', 'Sangria', 'Orange Juice'];
        $premiumItems = ['Ribeye', 'Lobster Ravioli', 'Charcuterie'];
        
        if (in_array($itemName, $popularItems)) {
            return rand(15, 25);
        } elseif (in_array($itemName, $premiumItems)) {
            return rand(3, 8);
        } else {
            return rand(8, 15);
        }
    }
    
    /**
     * Get day part based on item type
     */
    private function getDayPart($itemName)
    {
        $breakfastItems = ['Espresso', 'Orange Juice'];
        $dinnerItems = ['Ribeye', 'Lobster', 'Sangria'];
        
        if (in_array($itemName, $breakfastItems)) {
            return 'breakfast';
        } elseif (in_array($itemName, $dinnerItems)) {
            return 'dinner';
        } else {
            return 'lunch';
        }
    }
    
    /**
     * Create demo reconciliation data
     */
    private function createDemoReconciliations()
    {
        for ($day = 6; $day >= 0; $day--) {
            $date = now()->subDays($day);
            
            \DB::table('daily_reconciliations')->insert([
                'reconciliation_date' => $date,
                'expected_inventory' => rand(15000, 25000),
                'actual_inventory' => rand(14500, 24500),
                'variance_amount' => rand(-500, 200),
                'variance_percentage' => rand(-3, 2),
                'waste_amount' => rand(200, 800),
                'theft_suspected' => rand(0, 300),
                'created_at' => $date,
                'updated_at' => $date
            ]);
        }
    }
    
    /**
     * Create demo loss analysis data
     */
    private function createDemoLossAnalysis()
    {
        $lossTypes = ['waste', 'theft', 'spoilage', 'preparation_error', 'portion_control'];
        
        for ($day = 6; $day >= 0; $day--) {
            $date = now()->subDays($day);
            
            foreach ($lossTypes as $type) {
                if (rand(1, 3) === 1) { // 33% chance each day
                    \DB::table('loss_analyses')->insert([
                        'analysis_date' => $date,
                        'loss_type' => $type,
                        'loss_amount' => rand(50, 500),
                        'affected_items' => rand(2, 8),
                        'root_cause' => $this->getRootCause($type),
                        'prevention_action' => $this->getPreventionAction($type),
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);
                }
            }
        }
    }
    
    /**
     * Get root cause for loss type
     */
    private function getRootCause($lossType)
    {
        $causes = [
            'waste' => 'Over-preparation during peak hours',
            'theft' => 'Inadequate inventory monitoring',
            'spoilage' => 'Temperature control issues',
            'preparation_error' => 'Staff training gaps',
            'portion_control' => 'Inconsistent serving standards'
        ];
        
        return $causes[$lossType] ?? 'Under investigation';
    }
    
    /**
     * Get prevention action for loss type
     */
    private function getPreventionAction($lossType)
    {
        $actions = [
            'waste' => 'Implement demand forecasting',
            'theft' => 'Enhanced security protocols',
            'spoilage' => 'Temperature monitoring system',
            'preparation_error' => 'Additional staff training',
            'portion_control' => 'Standardized serving tools'
        ];
        
        return $actions[$lossType] ?? 'Develop action plan';
    }
    
    /**
     * Check for incomplete import processes from onboarding
     */
    private function checkForIncompleteImport($tenant)
    {
        // Check if there's an import job started from onboarding that's not completed
        if (!\DB::getSchemaBuilder()->hasTable('import_jobs')) {
            return null;
        }
        
        $incompleteImport = \DB::table('import_jobs')
            ->whereIn('status', ['pending', 'parsing', 'mapping', 'validating', 'importing'])
            ->where('import_context', 'onboarding')
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $incompleteImport;
    }
    
    /**
     * Resume incomplete import process
     */
    private function resumeIncompleteImport($importJob)
    {
        // Set session data to indicate resuming from onboarding
        session(['import_source' => 'onboarding']);
        session(['resuming_import' => true]);
        session(['import_job_id' => $importJob->id]);
        
        // Redirect to appropriate step based on import status
        switch ($importJob->status) {
            case 'pending':
                return redirect()->route('imports.create')
                    ->with('info', 'Continue your data import from where you left off...');
                    
            case 'parsing':
                return redirect()->route('imports.progress')
                    ->with('info', 'Your file is being processed. Please wait...');
                    
            case 'mapping':
                return redirect()->route('imports.mapping')
                    ->with('info', 'Continue with field mapping for your data...');
                    
            case 'validating':
                return redirect()->route('imports.validation')
                    ->with('info', 'Review and validate your import data...');
                    
            case 'importing':
                return redirect()->route('imports.progress')
                    ->with('info', 'Your import is in progress. Please wait...');
                    
            default:
                return redirect()->route('imports.show', $importJob->id)
                    ->with('info', 'View your import details...');
        }
    }
    
    /**
     * Get current onboarding progress
     */
    private function getOnboardingProgress($tenant)
    {
        $settings = $tenant->settings ?? [];
        
        // Calculate progress based on what's been done
        $completedSteps = 0;
        $totalSteps = 3;
        
        // Step 1: Initial choice made
        if (isset($settings['import_preference'])) {
            $completedSteps++;
        }
        
        // Step 2: Import started (if applicable)
        if (isset($settings['import_started']) || $this->hasAnyImportJobs($tenant)) {
            $completedSteps++;
        }
        
        // Step 3: Data available
        if ($this->hasAnyData($tenant)) {
            $completedSteps++;
        }
        
        return [
            'completed_steps' => $completedSteps,
            'total_steps' => $totalSteps,
            'current_step' => $this->getCurrentStep($completedSteps),
            'import_preference' => $settings['import_preference'] ?? null
        ];
    }
    
    /**
     * Check if tenant has any import jobs
     */
    private function hasAnyImportJobs($tenant)
    {
        if (!\DB::getSchemaBuilder()->hasTable('import_jobs')) {
            return false;
        }
        
        // Import jobs are stored in the tenant database, no need for tenant_id filter
        return \DB::table('import_jobs')->exists();
    }
    
    /**
     * Check if tenant has any data in the system
     */
    private function hasAnyData($tenant)
    {
        $hasMenuItems = \DB::getSchemaBuilder()->hasTable('menu_items') 
            ? \DB::table('menu_items')->exists() 
            : false;
            
        $hasInventory = \DB::getSchemaBuilder()->hasTable('inventory_items') 
            ? \DB::table('inventory_items')->exists() 
            : false;
            
        $hasSalesData = \DB::getSchemaBuilder()->hasTable('sales_transactions') 
            ? \DB::table('sales_transactions')->exists() 
            : false;
            
        return $hasMenuItems || $hasInventory || $hasSalesData;
    }
    
    /**
     * Get current step description
     */
    private function getCurrentStep($completedSteps)
    {
        $steps = [
            0 => 'Choose setup method',
            1 => 'Import or load data',
            2 => 'Verify and complete',
            3 => 'Setup complete'
        ];
        
        return $steps[$completedSteps] ?? 'Choose setup method';
    }
}
