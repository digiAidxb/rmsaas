<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateTestTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-test {--approve : Automatically approve the tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test tenant for development/testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Creating test tenant...');
        
        try {
            // Create tenant
            $tenant = Tenant::create([
                'name' => 'Test Pizza Palace',
                'domain' => 'testpizza.localhost',
                'database' => 'rmsaas_testpizza',
                'status' => $this->option('approve') ? 'approved' : 'pending',
                'is_active' => $this->option('approve') ? true : false,
            ]);
            
            $this->info("✓ Created tenant: {$tenant->name} (ID: {$tenant->id})");
            $this->info("  Domain: {$tenant->domain}");
            $this->info("  Database: {$tenant->database}");
            $this->info("  Status: {$tenant->status}");
            
            // Generate database credentials if approved
            if ($this->option('approve')) {
                $credentials = $tenant->generateDatabaseCredentials();
                $tenant->update([
                    'db_username' => $credentials['username'],
                    'db_password' => $credentials['password'],
                    'db_host' => '127.0.0.1',
                    'db_port' => 3306,
                ]);
                
                $this->info("  DB Username: {$credentials['username']}");
                $this->info("  DB Password: {$credentials['password']}");
                
                // Create the database
                $this->info('📀 Creating tenant database...');
                DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `{$tenant->database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->info("✓ Created database: {$tenant->database}");
                
                // Set tenant current and run migrations
                $this->info('🔧 Running tenant migrations...');
                $tenant->makeCurrent();
                
                \Artisan::call('migrate', ['--force' => true]);
                $this->info('✓ Tenant migrations completed');
                
                // Create admin user
                $this->info('👤 Creating admin user...');
                $user = User::create([
                    'name' => 'John Smith',
                    'email' => 'admin@testpizza.com',
                    'password' => Hash::make('password123'),
                    'tenant_id' => $tenant->id,
                    'email_verified_at' => now(),
                ]);
                
                $this->info("✓ Created admin user: {$user->name} ({$user->email})");
                
                // Initialize onboarding
                $tenant->initializeOnboarding();
                $this->info('✓ Initialized onboarding steps');
            }
            
            $this->info('');
            $this->info('🎉 Test tenant created successfully!');
            $this->info('');
            $this->info('Tenant Details:');
            $this->info("  Name: {$tenant->name}");
            $this->info("  Domain: {$tenant->domain}");
            $this->info("  Status: {$tenant->status}");
            
            if ($this->option('approve')) {
                $this->info('');
                $this->info('Admin Login:');
                $this->info('  Email: admin@testpizza.com');
                $this->info('  Password: password123');
                $this->info('');
                $this->info("Access via: http://{$tenant->domain}:8000");
            } else {
                $this->info('');
                $this->info('⚠️  Tenant is pending approval. Run with --approve to auto-approve.');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to create tenant: ' . $e->getMessage());
            return 1;
        }
    }
}
