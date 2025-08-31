<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminUsers = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@rmsaas.com',
                'email_verified_at' => now(),
                'password' => Hash::make('SuperAdmin@2025'),
                'role' => 'super_admin',
                'permissions' => json_encode([
                    'manage_all_tenants',
                    'manage_admin_users',
                    'manage_subscription_plans',
                    'view_all_analytics',
                    'manage_system_settings',
                    'manage_billing',
                    'manage_integrations',
                    'view_audit_logs',
                    'manage_security',
                    'export_data',
                ]),
                'last_login_at' => now()->subMinutes(30),
                'last_login_ip' => '192.168.1.100',
                'is_active' => true,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
            [
                'name' => 'Platform Administrator',
                'email' => 'admin@rmsaas.com',
                'email_verified_at' => now(),
                'password' => Hash::make('PlatformAdmin@2025'),
                'role' => 'admin',
                'permissions' => json_encode([
                    'manage_tenants',
                    'view_tenant_analytics',
                    'manage_subscription_plans',
                    'manage_billing',
                    'view_audit_logs',
                    'manage_support_tickets',
                    'export_reports',
                ]),
                'last_login_at' => now()->subHours(2),
                'last_login_ip' => '192.168.1.101',
                'is_active' => true,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
            [
                'name' => 'Support Manager',
                'email' => 'support@rmsaas.com',
                'email_verified_at' => now(),
                'password' => Hash::make('SupportManager@2025'),
                'role' => 'support_manager',
                'permissions' => json_encode([
                    'view_tenants',
                    'manage_support_tickets',
                    'view_tenant_analytics',
                    'assist_tenant_setup',
                    'manage_documentation',
                    'export_support_reports',
                ]),
                'last_login_at' => now()->subMinutes(15),
                'last_login_ip' => '192.168.1.102',
                'is_active' => true,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
            [
                'name' => 'Financial Analyst',
                'email' => 'finance@rmsaas.com',
                'email_verified_at' => now(),
                'password' => Hash::make('FinanceAnalyst@2025'),
                'role' => 'finance',
                'permissions' => json_encode([
                    'view_billing_data',
                    'manage_subscription_billing',
                    'view_financial_reports',
                    'manage_payment_methods',
                    'export_financial_data',
                    'view_revenue_analytics',
                ]),
                'last_login_at' => now()->subHours(4),
                'last_login_ip' => '192.168.1.103',
                'is_active' => true,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
            [
                'name' => 'Technical Support',
                'email' => 'tech-support@rmsaas.com',
                'email_verified_at' => now(),
                'password' => Hash::make('TechSupport@2025'),
                'role' => 'technical_support',
                'permissions' => json_encode([
                    'view_tenant_technical_data',
                    'manage_integrations',
                    'view_system_logs',
                    'manage_api_access',
                    'troubleshoot_issues',
                    'export_technical_reports',
                ]),
                'last_login_at' => now()->subMinutes(45),
                'last_login_ip' => '192.168.1.104',
                'is_active' => true,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
        ];

        foreach ($adminUsers as $user) {
            $user['created_at'] = now();
            $user['updated_at'] = now();
        }

        DB::connection('landlord')->table('admin_users')->insert($adminUsers);
    }
}