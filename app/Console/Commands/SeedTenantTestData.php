<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Multitenancy\Models\Tenant;
use Database\Seeders\RestaurantTestDataSeeder;
use Illuminate\Support\Facades\DB;

class SeedTenantTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed-test-data {--tenant= : Tenant ID or domain} {--all : Seed all active tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed test data for restaurant tenants to demo features';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->seedAllTenants();
        } elseif ($this->option('tenant')) {
            $this->seedSpecificTenant($this->option('tenant'));
        } else {
            $this->interactiveMode();
        }
    }
    
    private function seedAllTenants()
    {
        $tenants = Tenant::where('status', 'approved')->get();
        
        if ($tenants->isEmpty()) {
            $this->error('No active tenants found.');
            return;
        }
        
        $this->info("Seeding test data for {$tenants->count()} tenants...");
        
        foreach ($tenants as $tenant) {
            $this->seedTenant($tenant);
        }
        
        $this->info('✅ All tenants seeded successfully!');
    }
    
    private function seedSpecificTenant($identifier)
    {
        // Try to find by ID first, then by domain
        $tenant = Tenant::find($identifier) ?? Tenant::where('domain', $identifier)->first();
        
        if (!$tenant) {
            $this->error("Tenant '{$identifier}' not found.");
            return;
        }
        
        $this->seedTenant($tenant);
        $this->info("✅ Tenant '{$tenant->name}' seeded successfully!");
    }
    
    private function interactiveMode()
    {
        $activeTenants = Tenant::where('status', 'approved')->get();
        
        if ($activeTenants->isEmpty()) {
            $this->error('No active tenants found.');
            return;
        }
        
        $this->info('Active Tenants:');
        $this->table(
            ['ID', 'Name', 'Domain', 'Test Data'],
            $activeTenants->map(function ($tenant) {
                return [
                    $tenant->id,
                    $tenant->name,
                    $tenant->domain,
                    $this->hasTestData($tenant) ? '✅ Yes' : '❌ No'
                ];
            })->toArray()
        );
        
        $tenantId = $this->ask('Enter tenant ID to seed test data');
        
        if (!$tenantId) {
            $this->error('No tenant selected.');
            return;
        }
        
        $this->seedSpecificTenant($tenantId);
    }
    
    private function seedTenant(Tenant $tenant)
    {
        $this->info("🌱 Seeding test data for: {$tenant->name} ({$tenant->domain})");
        
        try {
            // Set current tenant context
            $tenant->makeCurrent();
            
            // Run the test data seeder
            $seeder = new RestaurantTestDataSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            
            // Mark tenant as having test data
            $settingsJson = $tenant->getRawOriginal('settings') ?? '{}';
            $currentSettings = json_decode($settingsJson, true);
            if (!is_array($currentSettings)) {
                $currentSettings = [];
            }
            
            $newSettings = array_merge($currentSettings, [
                'has_test_data' => true, 
                'test_data_seeded_at' => now()->toISOString()
            ]);
            
            $tenant->update([
                'settings' => json_encode($newSettings)
            ]);
            
        } catch (\Exception $e) {
            $this->error("Failed to seed {$tenant->name}: " . $e->getMessage());
        }
    }
    
    private function hasTestData(Tenant $tenant): bool
    {
        $settings = json_decode($tenant->settings ?? '{}', true);
        return $settings['has_test_data'] ?? false;
    }
}
