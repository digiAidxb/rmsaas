<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateTenantCredentialsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-credentials {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing tenants to use secure database credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::on('landlord')
            ->whereNull('db_username')
            ->orWhereNull('db_password')
            ->get();

        if ($tenants->isEmpty()) {
            $this->info('All tenants already have secure database credentials.');
            return Command::SUCCESS;
        }

        $this->info("Found {$tenants->count()} tenant(s) that need secure credentials.");
        
        if (!$this->option('force') && !$this->confirm('This will create new database users and migrate existing data. Continue?')) {
            $this->info('Migration cancelled.');
            return Command::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->info("🔄 Migrating: {$tenant->name} ({$tenant->domain})");
            
            try {
                $this->migrateTenantCredentials($tenant);
                $this->info("✅ {$tenant->name} migrated successfully");
            } catch (\Exception $e) {
                $this->error("❌ Failed to migrate {$tenant->name}: " . $e->getMessage());
            }
            
            $this->newLine();
        }

        $this->info("🎉 Tenant credential migration completed!");
        return Command::SUCCESS;
    }

    private function migrateTenantCredentials(Tenant $tenant): void
    {
        // Generate new credentials
        $credentials = $tenant->generateDatabaseCredentials();
        
        // Connect as root to create new user
        $rootConnection = DB::connection('landlord');
        
        // Drop user if exists
        try {
            $rootConnection->statement("DROP USER IF EXISTS '{$credentials['username']}'@'%'");
        } catch (\Exception $e) {
            // User might not exist, continue
        }
        
        // Create new database user
        $rootConnection->statement(
            "CREATE USER '{$credentials['username']}'@'%' IDENTIFIED BY '{$credentials['password']}'"
        );
        
        // Grant privileges only to this tenant's database
        $rootConnection->statement(
            "GRANT ALL PRIVILEGES ON `{$tenant->database}`.* TO '{$credentials['username']}'@'%'"
        );
        
        // Flush privileges
        $rootConnection->statement("FLUSH PRIVILEGES");
        
        // Update tenant with new credentials
        $tenant->update([
            'db_username' => $credentials['username'],
            'db_password' => $credentials['password'],
            'db_host' => '127.0.0.1',
            'db_port' => 3306,
        ]);
        
        $this->info("🔐 Created database user: {$credentials['username']}");
    }
}
