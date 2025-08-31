<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantSecurityStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:security-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show security status of all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::on('landlord')->get();
        
        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return Command::SUCCESS;
        }

        $headers = ['ID', 'Name', 'Domain', 'Database', 'DB User', 'Security Status'];
        $rows = [];

        foreach ($tenants as $tenant) {
            $securityStatus = $tenant->db_username && $tenant->db_password 
                ? '🔐 Secure' 
                : '⚠️ Using Root';
                
            $dbUser = $tenant->db_username ?: 'root';
            
            $rows[] = [
                $tenant->id,
                $tenant->name,
                $tenant->domain,
                $tenant->database,
                $dbUser,
                $securityStatus,
            ];
        }

        $this->table($headers, $rows);
        
        $secureCount = $tenants->where('db_username', '!=', null)->count();
        $totalCount = $tenants->count();
        
        $this->newLine();
        $this->info("Security Summary:");
        $this->info("🔐 Secure tenants: {$secureCount}");
        $this->info("⚠️ Insecure tenants: " . ($totalCount - $secureCount));
        $this->info("Total tenants: {$totalCount}");

        return Command::SUCCESS;
    }
}
