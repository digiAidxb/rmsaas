<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class CreateLoginLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:login-link {--email= : User email} {--domain= : Tenant domain} {--list : List active tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create secure login links for tenant users (localhost testing)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listActiveTenants();
            return;
        }

        $domain = $this->option('domain');
        $email = $this->option('email');

        if (!$domain) {
            $domain = $this->ask('Enter tenant domain (e.g., tenant1.localhost)');
        }

        if (!$email) {
            $email = $this->ask('Enter user email');
        }

        if (!$domain || !$email) {
            $this->error('Both domain and email are required');
            return;
        }

        $this->createLoginLink($domain, $email);
    }

    private function listActiveTenants()
    {
        $activeTenants = Tenant::where('status', 'approved')->get();
        
        if ($activeTenants->isEmpty()) {
            $this->info('No active tenants found. Run: php artisan tenant:activate --all');
            return;
        }

        $this->info('Active Tenants:');
        $this->table(
            ['Domain', 'Name', 'Owner Email', 'Database'],
            $activeTenants->map(function ($tenant) {
                $owner = User::where('tenant_id', $tenant->id)->where('role', 'owner')->first();
                return [
                    $tenant->domain,
                    $tenant->name,
                    $owner ? $owner->email : 'N/A',
                    $tenant->database,
                ];
            })->toArray()
        );

        $this->line('');
        $this->info('💡 To create a login link, use:');
        $this->line('php artisan tenant:login-link --domain=DOMAIN --email=EMAIL');
    }

    private function createLoginLink($domain, $email)
    {
        $tenant = Tenant::where('domain', $domain)->first();
        
        if (!$tenant) {
            $this->error("Tenant with domain '{$domain}' not found.");
            return;
        }

        if ($tenant->status !== 'approved') {
            $this->error("Tenant '{$domain}' is not activated. Run: php artisan tenant:activate --email={$email}");
            return;
        }

        $user = User::where('email', $email)->where('tenant_id', $tenant->id)->first();
        
        if (!$user) {
            $this->error("User '{$email}' not found for tenant '{$domain}'.");
            return;
        }

        // Create a temporary signed URL for auto-login
        $loginToken = base64_encode(json_encode([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'expires' => Carbon::now()->addMinutes(30)->timestamp,
            'signature' => hash_hmac('sha256', $user->id . $tenant->id, config('app.key'))
        ]));

        $loginUrl = "http://{$tenant->domain}:8000/auto-login/{$loginToken}";

        $this->info("🔗 Auto-login link for {$user->name} ({$email}):");
        $this->line($loginUrl);
        $this->info("⏰ Link expires in 30 minutes");
        $this->line('');
        $this->info("🌐 Manual login at: http://{$tenant->domain}:8000/login");
        $this->info("📧 Email: {$user->email}");
        $this->info("🔒 Password: Restaurant@2025");
        $this->line('');
        
        // Show onboarding status
        if (!$tenant->onboarding_completed_at) {
            $this->info("📋 Onboarding Status: Not completed (will redirect to onboarding)");
        } else {
            $this->info("✅ Onboarding Status: Completed");
        }
    }
}
