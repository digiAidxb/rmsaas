<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class CleanAllTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:clean-all {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all tenants and their databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all tenants
        $tenants = Tenant::on('landlord')->get();
        
        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return;
        }
        
        $this->info("Found {$tenants->count()} tenants:");
        $this->table(
            ['ID', 'Name', 'Domain', 'Database'],
            $tenants->map(function ($tenant) {
                return [
                    $tenant->id,
                    $tenant->name,
                    $tenant->domain,
                    $tenant->database
                ];
            })->toArray()
        );
        
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL tenants and their databases? This cannot be undone!')) {
                $this->info('Operation cancelled.');
                return;
            }
        }
        
        $this->info('Deleting tenants and their databases...');
        
        foreach ($tenants as $tenant) {
            $this->line("Deleting tenant: {$tenant->name} ({$tenant->domain})");
            
            try {
                // Drop tenant database if it exists
                if ($tenant->database) {
                    DB::connection('landlord')->statement("DROP DATABASE IF EXISTS `{$tenant->database}`");
                    $this->line("  ✓ Dropped database: {$tenant->database}");
                }
                
                // Delete tenant record
                $tenant->delete();
                $this->line("  ✓ Deleted tenant record");
                
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to delete tenant {$tenant->name}: " . $e->getMessage());
            }
        }
        
        $this->info('✅ All tenants cleaned successfully!');
    }
}
