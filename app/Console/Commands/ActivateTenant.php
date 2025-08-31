<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ActivateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:activate {--email= : User email} {--list : List all inactive tenants} {--all : Activate all inactive tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate tenant accounts and create login links for localhost testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listInactiveTenants();
            return;
        }

        if ($this->option('all')) {
            $this->activateAllTenants();
            return;
        }

        $email = $this->option('email');
        if (!$email) {
            $email = $this->ask('Enter the user email to activate their tenant');
        }

        if (!$email) {
            $this->error('Email is required');
            return;
        }

        $this->activateTenantByEmail($email);
    }

    private function listInactiveTenants()
    {
        $inactiveTenants = Tenant::where('status', '!=', 'approved')->get();
        
        if ($inactiveTenants->isEmpty()) {
            $this->info('No inactive tenants found.');
            return;
        }

        $this->info('Inactive Tenants:');
        $this->table(
            ['ID', 'Name', 'Domain', 'Status', 'Owner Email', 'Created'],
            $inactiveTenants->map(function ($tenant) {
                $owner = User::where('tenant_id', $tenant->id)->where('role', 'owner')->first();
                return [
                    $tenant->id,
                    $tenant->name,
                    $tenant->domain,
                    $tenant->status,
                    $owner ? $owner->email : 'N/A',
                    $tenant->created_at->format('Y-m-d H:i')
                ];
            })->toArray()
        );
    }

    private function activateAllTenants()
    {
        $inactiveTenants = Tenant::where('status', '!=', 'approved')->get();
        
        if ($inactiveTenants->isEmpty()) {
            $this->info('No inactive tenants found.');
            return;
        }

        foreach ($inactiveTenants as $tenant) {
            $this->activateTenant($tenant);
        }
    }

    private function activateTenantByEmail($email)
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        $tenant = Tenant::find($user->tenant_id);
        
        if (!$tenant) {
            $this->error("No tenant found for user '{$email}'.");
            return;
        }

        $this->activateTenant($tenant, $user);
    }

    private function activateTenant($tenant, $user = null)
    {
        // Activate tenant
        $tenant->update(['status' => 'approved']);
        
        if (!$user) {
            $user = User::where('tenant_id', $tenant->id)->where('role', 'owner')->first();
        }

        if (!$user) {
            $this->error("No owner found for tenant: {$tenant->name}");
            return;
        }

        // Verify user email
        $user->update(['email_verified_at' => now()]);

        $this->info("✅ Activated tenant: {$tenant->name}");
        $this->info("📧 User email verified: {$user->email}");
        
        // Generate tenant login URL
        $loginUrl = "http://{$tenant->domain}:8000/login";
        $this->info("🔗 Login URL: {$loginUrl}");
        
        // Display credentials
        $this->line('');
        $this->info('=== LOGIN CREDENTIALS ===');
        $this->line("Email: {$user->email}");
        $this->line("Domain: {$tenant->domain}");
        $this->line("Database: {$tenant->database}");
        $this->info('============================');
        $this->line('');

        // Generate curl command for easy testing
        $curlCommand = sprintf(
            'curl -X POST -H "Host: %s" -H "Content-Type: application/json" -d \'{"email":"%s","password":"Restaurant@2025"}\' http://localhost:8000/login',
            $tenant->domain,
            $user->email
        );
        
        $this->info('📋 Test login command:');
        $this->line($curlCommand);
        $this->line('');
    }
}
