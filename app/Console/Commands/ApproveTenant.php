<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApproveTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:approve {domain : The tenant domain to approve}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve a pending tenant and set up their database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        
        // Find tenant
        $tenant = Tenant::where('domain', $domain)->first();
        
        if (!$tenant) {
            $this->error("Tenant with domain '{$domain}' not found.");
            return 1;
        }
        
        if ($tenant->status === 'approved') {
            $this->info("Tenant '{$tenant->name}' is already approved.");
            
            // Still show login details and DNS setup for already approved tenants
            $owner = $tenant->users()->where('role', 'owner')->first();
            if ($owner) {
                $this->info("");
                $this->info("Owner Login Details:");
                $this->info("  Email: {$owner->email}");
                $this->info("  Access URL: http://{$tenant->domain}:8000");
                
                $this->warn("");
                $this->warn("⚠️  DNS Configuration Required:");
                $this->warn("Add this entry to your hosts file (C:\\Windows\\System32\\drivers\\etc\\hosts):");
                $this->warn("127.0.0.1    {$tenant->domain}");
                $this->warn("");
            }
            return 0;
        }
        
        $this->info("Approving tenant: {$tenant->name} ({$tenant->domain})");
        
        try {
            // First, verify database exists (should be created during registration)
            $databaseExists = DB::connection('landlord')->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$tenant->database]);
            if (empty($databaseExists)) {
                throw new \Exception("Tenant database '{$tenant->database}' not found. Database should be created during registration.");
            }
            $this->info("Found existing tenant database: {$tenant->database}");
            
            DB::beginTransaction();
            
            // Update tenant status to approved
            $tenant->update([
                'status' => 'approved',
                'approved_at' => now(),
                'is_active' => true,
            ]);
            
            // Generate database credentials (force regeneration for existing tenants to update permissions)
            $credentials = $tenant->generateDatabaseCredentials();
            
            // Always update credentials and permissions to ensure they have latest permissions
            $this->info("Setting up database user: {$credentials['username']}");
            
            // Create database user with root connection
            $connection = DB::connection('landlord');
            
            // Drop user if exists (for re-setup scenarios) - both % and localhost
            try {
                $connection->statement("DROP USER IF EXISTS '{$credentials['username']}'@'%'");
                $connection->statement("DROP USER IF EXISTS '{$credentials['username']}'@'localhost'");
            } catch (\Exception $e) {
                // User might not exist, continue
            }
            
            // Create database user for both % and localhost
            $connection->statement(
                "CREATE USER '{$credentials['username']}'@'localhost' IDENTIFIED BY '{$credentials['password']}'"
            );
            $connection->statement(
                "CREATE USER '{$credentials['username']}'@'%' IDENTIFIED BY '{$credentials['password']}'"
            );
            
            // Grant privileges to both hosts
            $connection->statement(
                "GRANT ALL PRIVILEGES ON `{$tenant->database}`.* TO '{$credentials['username']}'@'localhost'"
            );
            $connection->statement(
                "GRANT ALL PRIVILEGES ON `{$tenant->database}`.* TO '{$credentials['username']}'@'%'"
            );
            
            // Grant global SELECT permission needed for Laravel migration information_schema checks
            $connection->statement(
                "GRANT SELECT ON *.* TO '{$credentials['username']}'@'localhost'"
            );
            $connection->statement(
                "GRANT SELECT ON *.* TO '{$credentials['username']}'@'%'"
            );
            
            // Flush privileges
            $connection->statement("FLUSH PRIVILEGES");
            
            // Update tenant with credentials
            $tenant->update([
                'db_username' => $credentials['username'],
                'db_password' => $credentials['password'],
                'db_host' => '127.0.0.1',
                'db_port' => 3306,
            ]);
            
            $this->info("Generated database credentials:");
            $this->info("  Username: {$credentials['username']}");
            $this->info("  Password: {$credentials['password']}");
            
            // Set tenant current and run migrations
            $this->info("Running tenant migrations...");
            $tenant->makeCurrent();
            \Artisan::call('migrate', ['--force' => true, '--database' => 'tenant']);
            
            // Initialize onboarding
            $tenant->initializeOnboarding();
            $this->info("Initialized onboarding steps");
            
            DB::commit();
            
            $this->info("✅ Tenant '{$tenant->name}' approved successfully!");
            
            // Get owner user for login info
            $owner = $tenant->users()->where('role', 'owner')->first();
            if ($owner) {
                $this->info("");
                $this->info("Owner Login Details:");
                $this->info("  Email: {$owner->email}");
                $this->info("  Access URL: http://{$tenant->domain}:8000");
                
                $this->warn("");
                $this->warn("⚠️  DNS Configuration Required:");
                $this->warn("Add this entry to your hosts file (C:\\Windows\\System32\\drivers\\etc\\hosts):");
                $this->warn("127.0.0.1    {$tenant->domain}");
                $this->warn("");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            $this->error("Failed to approve tenant: " . $e->getMessage());
            return 1;
        }
    }
}
