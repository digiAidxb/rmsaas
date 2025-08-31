<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetupTenantDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup {tenant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up database and migrations for a tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        
        $tenant = Tenant::on('landlord')->find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found.");
            return Command::FAILURE;
        }

        $this->info("Setting up database for tenant: {$tenant->name}");
        $this->info("Database: {$tenant->database}");

        try {
            // Create the database
            $this->createDatabase($tenant->database);
            
            // Generate and create database user
            $credentials = $this->createDatabaseUser($tenant);
            
            // Update tenant with database credentials
            $this->updateTenantCredentials($tenant, $credentials);
            
            // Configure tenant database connection
            $this->configureTenantConnection($tenant);
            
            // Run migrations on tenant database
            $this->runTenantMigrations($tenant);
            
            $this->info("✅ Tenant database setup completed successfully!");
            $this->info("🔐 Database user: {$credentials['username']}");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to set up tenant database: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function createDatabase(string $databaseName): void
    {
        $this->info("Creating database: {$databaseName}");
        
        // Get landlord connection config
        $config = config('database.connections.landlord');
        
        // Connect without specifying database to create it
        $connection = DB::connection('landlord');
        $connection->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
        
        $this->info("Database created successfully.");
    }

    private function createDatabaseUser(Tenant $tenant): array
    {
        $this->info("Creating database user for tenant...");
        
        // Generate credentials
        $credentials = $tenant->generateDatabaseCredentials();
        
        // Connect as root to create user
        $connection = DB::connection('landlord');
        
        // Drop user if exists (for re-setup scenarios)
        try {
            $connection->statement("DROP USER IF EXISTS '{$credentials['username']}'@'%'");
        } catch (\Exception $e) {
            // User might not exist, continue
        }
        
        // Create database user
        $connection->statement(
            "CREATE USER '{$credentials['username']}'@'%' IDENTIFIED BY '{$credentials['password']}'"
        );
        
        // Grant privileges only to this tenant's database
        $connection->statement(
            "GRANT ALL PRIVILEGES ON `{$tenant->database}`.* TO '{$credentials['username']}'@'%'"
        );
        
        // Grant additional permissions needed for Laravel migrations
        $connection->statement(
            "GRANT PROCESS, SHOW DATABASES ON *.* TO '{$credentials['username']}'@'%'"
        );
        
        // Flush privileges
        $connection->statement("FLUSH PRIVILEGES");
        
        $this->info("Database user created successfully.");
        
        return $credentials;
    }

    private function updateTenantCredentials(Tenant $tenant, array $credentials): void
    {
        $tenant->update([
            'db_username' => $credentials['username'],
            'db_password' => $credentials['password'],
            'db_host' => '127.0.0.1',
            'db_port' => 3306,
        ]);
        
        // Refresh the tenant model
        $tenant->refresh();
    }

    private function configureTenantConnection(Tenant $tenant): void
    {
        // Update tenant connection config to use tenant's specific credentials
        Config::set('database.connections.tenant', $tenant->getDatabaseConfig());
        
        // Clear database connection cache
        DB::purge('tenant');
    }

    private function runTenantMigrations(Tenant $tenant): void
    {
        $this->info("Running migrations on tenant database...");
        
        // Make tenant current so migrations run on correct database
        $tenant->makeCurrent();
        
        // Run migrations on tenant database
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--force' => true,
        ]);
        
        $this->info("Migrations completed.");
    }
}
