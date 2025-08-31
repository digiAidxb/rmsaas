<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'domain',
        'database',
        'db_username',
        'db_password',
        'db_host',
        'db_port',
        'settings',
        'status',
        'approved_at',
        'is_active',
        'onboarding_status',
        'onboarding_completed_at',
        'skip_onboarding',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'db_password' => 'encrypted',
        'db_port' => 'integer',
        'approved_at' => 'datetime',
        'onboarding_status' => 'array',
        'onboarding_completed_at' => 'datetime',
        'skip_onboarding' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function generateDatabaseCredentials(): array
    {
        // Generate secure username (prefix + random string)
        $username = 'tenant_' . substr(str_replace(['-', '_'], '', $this->domain), 0, 8) . '_' . substr(md5($this->id), 0, 6);
        
        // Generate secure password
        $password = bin2hex(random_bytes(16)); // 32 character password
        
        return [
            'username' => $username,
            'password' => $password,
        ];
    }

    public function getDatabaseConfig(): array
    {
        return [
            'driver' => 'mysql',
            'host' => $this->db_host ?? '127.0.0.1',
            'port' => $this->db_port ?? 3306,
            'database' => $this->database,
            'username' => $this->db_username,
            'password' => $this->db_password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ];
    }

    public function getOnboardingSteps(): array
    {
        return [
            'welcome' => [
                'title' => 'Welcome to RMSaaS',
                'description' => 'Get started with your restaurant management system',
                'completed' => false
            ],
            'business_info' => [
                'title' => 'Business Information',
                'description' => 'Complete your restaurant profile and settings',
                'completed' => false
            ],
            'data_import' => [
                'title' => 'Import Your Data',
                'description' => 'Import existing menu items, inventory, and sales data',
                'completed' => false
            ],
            'menu_setup' => [
                'title' => 'Menu Configuration',
                'description' => 'Set up your menu items and categories',
                'completed' => false
            ],
            'inventory_setup' => [
                'title' => 'Inventory Management',
                'description' => 'Configure your ingredient inventory',
                'completed' => false
            ],
            'staff_setup' => [
                'title' => 'Staff Management',
                'description' => 'Add team members and set permissions',
                'completed' => false
            ],
            'dashboard_tour' => [
                'title' => 'Dashboard Tour',
                'description' => 'Learn about analytics and reporting features',
                'completed' => false
            ],
            'completed' => [
                'title' => 'Setup Complete',
                'description' => 'Your restaurant management system is ready to use',
                'completed' => false
            ]
        ];
    }

    public function initializeOnboarding(): void
    {
        $this->onboarding_status = $this->getOnboardingSteps();
        $this->save();
    }

    public function completeOnboardingStep(string $step): void
    {
        $status = $this->onboarding_status ?? $this->getOnboardingSteps();
        
        if (isset($status[$step])) {
            $status[$step]['completed'] = true;
            $this->onboarding_status = $status;
            
            // Check if all steps are completed
            $allCompleted = collect($status)->except('completed')->every(function($step) {
                return $step['completed'] === true;
            });
            
            if ($allCompleted) {
                $status['completed']['completed'] = true;
                $this->onboarding_completed_at = now();
            }
            
            $this->onboarding_status = $status;
            $this->save();
        }
    }

    public function getOnboardingProgress(): array
    {
        // Lazy initialize onboarding status if it doesn't exist
        if (!$this->onboarding_status) {
            $this->initializeOnboarding();
        }
        
        $status = $this->onboarding_status ?? $this->getOnboardingSteps();
        $totalSteps = collect($status)->except('completed')->count();
        $completedSteps = collect($status)->except('completed')->where('completed', true)->count();
        
        return [
            'total_steps' => $totalSteps,
            'completed_steps' => $completedSteps,
            'percentage' => $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0,
            'is_complete' => $this->onboarding_completed_at !== null,
            'steps' => $status
        ];
    }

    public function skipOnboarding(): void
    {
        $this->skip_onboarding = true;
        $this->onboarding_completed_at = now();
        
        // Mark all steps as completed
        $status = $this->getOnboardingSteps();
        foreach ($status as $key => $step) {
            $status[$key]['completed'] = true;
        }
        $this->onboarding_status = $status;
        $this->save();
    }
}
