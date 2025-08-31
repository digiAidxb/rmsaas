<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class SetupAllTenantsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up databases for all existing tenants';

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

        $this->info("Found {$tenants->count()} tenant(s). Setting up databases...");
        
        foreach ($tenants as $tenant) {
            $this->info("📋 Setting up: {$tenant->name} ({$tenant->domain})");
            
            $exitCode = $this->call('tenant:setup', ['tenant_id' => $tenant->id]);
            
            if ($exitCode === Command::SUCCESS) {
                $this->info("✅ {$tenant->name} setup completed");
            } else {
                $this->error("❌ {$tenant->name} setup failed");
            }
            
            $this->newLine();
        }
        
        $this->info("🎉 All tenant database setups completed!");
        
        return Command::SUCCESS;
    }
}
