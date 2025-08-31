<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Test Routes for Phase 2 Development
|--------------------------------------------------------------------------
*/

// Test Route: Database verification (no tenant needed)
Route::get('/test/database', function () {
    try {
        return response()->json([
            'status' => 'success',
            'database_seeding' => [
                'countries' => DB::connection('landlord')->table('countries')->count(),
                'subscription_plans' => DB::connection('landlord')->table('subscription_plans')->count(),
                'admin_users' => DB::connection('landlord')->table('admin_users')->count(),
                'tenants' => DB::connection('landlord')->table('tenants')->count(),
                'total_users' => DB::connection('landlord')->table('users')->count(),
            ],
            'sample_tenants' => DB::connection('landlord')
                ->table('tenants')
                ->select('name', 'domain', 'status', 'business_type')
                ->limit(5)
                ->get(),
            'multi_language_users' => DB::connection('landlord')
                ->table('users')
                ->select('name', 'email', 'role', 'preferred_language', 'tenant_id')
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'language' => $user->preferred_language,
                        'language_name' => config('app.available_locales')[$user->preferred_language]['native'] ?? $user->preferred_language,
                        'tenant_id' => $user->tenant_id,
                    ];
                }),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Test Route: View tenant information and seeded data (requires tenant middleware)
Route::middleware(['tenant'])->get('/test/tenant-info', function () {
    $currentTenant = app('currentTenant');
    
    try {
        // Test tenant database connection
        $tenantDbTest = null;
        $tenantData = null;
        
        if ($currentTenant) {
            // Test connection to tenant database
            $tenantDbTest = [
                'connected' => true,
                'database' => config('database.connections.tenant.database'),
            ];
            
            // Get sample data from tenant database
            $tenantData = [
                'menu_categories' => DB::connection('tenant')->table('menu_categories')->count(),
                'menu_items' => DB::connection('tenant')->table('menu_items')->count(),
                'inventory_items' => DB::connection('tenant')->table('inventory_items')->count(),
                'sample_menu_items' => DB::connection('tenant')
                    ->table('menu_items')
                    ->join('menu_categories', 'menu_items.category_id', '=', 'menu_categories.id')
                    ->select('menu_items.name as item_name', 'menu_items.price', 'menu_categories.name as category')
                    ->limit(5)
                    ->get(),
                'sample_inventory' => DB::connection('tenant')
                    ->table('inventory_items')
                    ->select('name', 'sku', 'current_stock', 'unit')
                    ->limit(5)
                    ->get(),
            ];
        }
        
        return response()->json([
            'status' => 'success',
            'current_tenant' => $currentTenant ? [
                'id' => $currentTenant->id,
                'name' => $currentTenant->name,
                'domain' => $currentTenant->domain,
                'status' => $currentTenant->status,
                'database' => $currentTenant->database,
            ] : null,
            'database_connections' => [
                'landlord' => config('database.connections.landlord.database'),
                'tenant' => config('database.connections.tenant.database') ?? 'Not configured',
            ],
            'tenant_database' => $tenantDbTest,
            'tenant_data' => $tenantData,
            'available_locales' => array_keys(config('app.available_locales')),
            'current_locale' => app()->getLocale(),
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'current_tenant' => $currentTenant ? [
                'id' => $currentTenant->id,
                'name' => $currentTenant->name,
                'domain' => $currentTenant->domain,
                'database' => $currentTenant->database,
            ] : null,
        ], 500);
    }
});

// Test Route: List users for current tenant with languages
Route::middleware(['tenant'])->get('/test/tenant-users', function () {
    $currentTenant = app('currentTenant');
    
    if (!$currentTenant) {
        return response()->json(['error' => 'No tenant found'], 404);
    }
    
    $users = DB::connection('landlord')
        ->table('users')
        ->where('tenant_id', $currentTenant->id)
        ->select('name', 'email', 'role', 'preferred_language', 'is_active', 'last_login_at')
        ->get();
    
    return response()->json([
        'tenant' => $currentTenant->name,
        'user_count' => $users->count(),
        'users' => $users->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'language' => $user->preferred_language,
                'language_name' => config('app.available_locales')[$user->preferred_language]['native'] ?? $user->preferred_language,
                'is_active' => $user->is_active,
                'last_login' => $user->last_login_at,
            ];
        })
    ]);
});

// Test Route: Test authentication guard
Route::get('/test/auth-status', function () {
    return response()->json([
        'guards' => [
            'web' => Auth::guard('web')->check(),
            'tenant' => Auth::guard('tenant')->check(),
        ],
        'user' => Auth::guard('tenant')->user() ? [
            'id' => Auth::guard('tenant')->user()->id,
            'name' => Auth::guard('tenant')->user()->name,
            'email' => Auth::guard('tenant')->user()->email,
            'role' => Auth::guard('tenant')->user()->role,
            'language' => Auth::guard('tenant')->user()->preferred_language,
        ] : null,
        'session_locale' => session('locale'),
        'app_locale' => app()->getLocale(),
    ]);
});

// Test Route: Simulate login (for testing purposes)
Route::middleware(['tenant'])->post('/test/login', function (Request $request) {
    $email = $request->input('email');
    $password = $request->input('password', 'Restaurant@2025'); // Default password from seeder
    
    $currentTenant = app('currentTenant');
    if (!$currentTenant) {
        return response()->json(['error' => 'No tenant context'], 400);
    }
    
    // Try to authenticate with tenant guard
    if (Auth::guard('tenant')->attempt(['email' => $email, 'password' => $password], true)) {
        $user = Auth::guard('tenant')->user();
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'language' => $user->preferred_language,
                'language_name' => config('app.available_locales')[$user->preferred_language]['native'],
                'tenant_id' => $user->tenant_id,
            ],
            'tenant' => [
                'name' => $currentTenant->name,
                'domain' => $currentTenant->domain,
            ],
            'session_locale' => session('locale'),
            'app_locale' => app()->getLocale(),
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Login failed',
        'attempted_email' => $email,
        'tenant' => $currentTenant->name,
    ], 401);
});

// Test Route: List all tenants and their sample users
Route::get('/test/all-tenants', function () {
    $tenants = DB::connection('landlord')
        ->table('tenants')
        ->select('id', 'name', 'domain', 'status', 'business_type')
        ->limit(10)
        ->get();
    
    $tenantsWithUsers = $tenants->map(function ($tenant) {
        $sampleUsers = DB::connection('landlord')
            ->table('users')
            ->where('tenant_id', $tenant->id)
            ->select('name', 'email', 'role', 'preferred_language')
            ->limit(4) // Show first 4 users per tenant
            ->get();
        
        return [
            'tenant' => $tenant,
            'sample_users' => $sampleUsers->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'language' => $user->preferred_language,
                    'language_name' => config('app.available_locales')[$user->preferred_language]['native'] ?? $user->preferred_language,
                ];
            })
        ];
    });
    
    return response()->json($tenantsWithUsers);
});