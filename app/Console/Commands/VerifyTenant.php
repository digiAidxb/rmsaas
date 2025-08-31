<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;

class VerifyTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:verify {--email= : User email} {--domain= : Tenant domain} {--list : List pending tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify and approve tenant accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listPendingTenants();
            return;
        }

        $email = $this->option('email');
        $domain = $this->option('domain');

        if (!$email && !$domain) {
            $this->listPendingTenants();
            $this->line('');
            $email = $this->ask('Enter user email or tenant domain to verify');
            
            if (str_contains($email, '@')) {
                // It's an email
                $email = $email;
            } else {
                // It's likely a domain
                $domain = $email;
                $email = null;
            }
        }

        if ($email) {
            $this->verifyTenantByEmail($email);
        } elseif ($domain) {
            $this->verifyTenantByDomain($domain);
        } else {
            $this->error('Please provide either email or domain');
        }
    }

    private function listPendingTenants()
    {
        $pendingTenants = Tenant::on('landlord')->where('status', 'pending')->get();
        
        if ($pendingTenants->isEmpty()) {
            $this->info('No pending tenants found.');
            return;
        }

        $this->info('Pending Tenants:');
        $this->table(
            ['ID', 'Restaurant Name', 'Domain', 'User Email', 'Created'],
            $pendingTenants->map(function ($tenant) {
                $user = User::on('landlord')->where('tenant_id', $tenant->id)->first();
                return [
                    $tenant->id,
                    $tenant->name,
                    $tenant->domain,
                    $user ? $user->email : 'N/A',
                    $tenant->created_at->format('Y-m-d H:i')
                ];
            })->toArray()
        );
    }

    private function verifyTenantByEmail($email)
    {
        $user = User::on('landlord')->where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        $tenant = Tenant::on('landlord')->find($user->tenant_id);
        
        if (!$tenant) {
            $this->error("No tenant found for user '{$email}'.");
            return;
        }

        $this->verifyTenant($tenant, $user);
    }

    private function verifyTenantByDomain($domain)
    {
        $tenant = Tenant::on('landlord')->where('domain', $domain)->first();
        
        if (!$tenant) {
            $this->error("Tenant with domain '{$domain}' not found.");
            return;
        }

        $user = User::on('landlord')->where('tenant_id', $tenant->id)->first();
        
        if (!$user) {
            $this->error("No users found for tenant '{$domain}'.");
            return;
        }

        $this->verifyTenant($tenant, $user);
    }

    private function verifyTenant($tenant, $user)
    {
        if ($tenant->status === 'approved') {
            $this->info("✅ Tenant '{$tenant->name}' is already approved.");
            return;
        }

        // Show tenant details for confirmation
        $this->line('');
        $this->info('=== TENANT VERIFICATION ===');
        $this->line("Restaurant: {$tenant->name}");
        $this->line("Domain: {$tenant->domain}");
        $this->line("Owner: {$user->name} ({$user->email})");
        $this->line("Status: {$tenant->status}");
        $this->line("Created: {$tenant->created_at->format('Y-m-d H:i')}");
        $this->line('============================');
        $this->line('');

        if ($this->confirm('Do you want to approve this tenant?', true)) {
            // Approve tenant
            $tenant->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => 'console'
            ]);

            // Verify user email
            $user->update(['email_verified_at' => now()]);

            $this->info("✅ Tenant '{$tenant->name}' has been approved!");
            $this->info("📧 User email verified: {$user->email}");
            
            $this->line('');
            $this->info('=== ACCESS INFORMATION ===');
            $this->line("🌐 Login URL: http://{$tenant->domain}:8000/login");
            $this->line("📧 Email: {$user->email}");
            $this->line("🔒 Password: Restaurant@2025");
            $this->line('===========================');
            
            // Show command to create auto-login link
            $this->line('');
            $this->info('💡 To create an auto-login link, run:');
            $this->line("php artisan tenant:login-link --domain={$tenant->domain} --email={$user->email}");
        } else {
            $this->line('Tenant verification cancelled.');
        }
    }
}