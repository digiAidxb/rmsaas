<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateTenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {domain} {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $database = $this->argument('database');

        if (Tenant::on('landlord')->where('domain', $domain)->exists()) {
            $this->error("Tenant with domain '{$domain}' already exists.");
            return Command::FAILURE;
        }

        $tenant = Tenant::on('landlord')->create([
            'name' => $name,
            'domain' => $domain,
            'database' => $database,
            'settings' => [],
            'is_active' => true,
        ]);

        $this->info("Tenant '{$name}' created successfully with ID: {$tenant->id}");
        $this->info("Domain: {$domain}");
        $this->info("Database: {$database}");

        // Ask if user wants to set up the database
        if ($this->confirm('Do you want to set up the tenant database now?', true)) {
            $this->info("Setting up tenant database...");
            
            $exitCode = $this->call('tenant:setup', ['tenant_id' => $tenant->id]);
            
            if ($exitCode === Command::SUCCESS) {
                $this->info("🎉 Tenant is ready to use!");
            } else {
                $this->warn("⚠️ Tenant created but database setup failed. Run: php artisan tenant:setup {$tenant->id}");
            }
        } else {
            $this->info("⚠️ Remember to set up the database later: php artisan tenant:setup {$tenant->id}");
        }

        return Command::SUCCESS;
    }
}
