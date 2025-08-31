<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = DB::connection('landlord')->table('tenants')->get();
        
        foreach ($tenants as $tenant) {
            // Create users for each tenant with diverse roles and languages
            $this->seedUsersForTenant($tenant);
        }
    }

    private function seedUsersForTenant($tenant): void
    {
        $users = [];
        
        // Define realistic user profiles for restaurants
        $userProfiles = [
            // Owner (Arabic - as requested)
            [
                'name' => 'Ahmed Al-Rashid',
                'email' => 'owner@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'owner',
                'preferred_language' => 'ar',
                'timezone' => 'Asia/Dubai',
                'permissions' => [
                    'manage_all_settings',
                    'view_all_reports',
                    'manage_users',
                    'manage_inventory',
                    'manage_menu',
                    'manage_suppliers',
                    'manage_finances',
                    'view_analytics',
                ],
            ],
            // Manager (English - as requested)
            [
                'name' => 'Sarah Johnson',
                'email' => 'manager@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'manager',
                'preferred_language' => 'en',
                'timezone' => 'America/New_York',
                'permissions' => [
                    'manage_inventory',
                    'manage_menu',
                    'view_reports',
                    'manage_staff',
                    'manage_orders',
                    'view_analytics',
                ],
            ],
            // Accountant (Chinese - as requested)
            [
                'name' => 'Li Wei',
                'email' => 'accountant@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'accountant',
                'preferred_language' => 'zh',
                'timezone' => 'Asia/Shanghai',
                'permissions' => [
                    'view_financial_reports',
                    'manage_expenses',
                    'view_cost_analysis',
                    'export_financial_data',
                    'manage_budgets',
                ],
            ],
            // Operator (Hindi - as requested)
            [
                'name' => 'Raj Sharma',
                'email' => 'operator@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'operator',
                'preferred_language' => 'hi',
                'timezone' => 'Asia/Kolkata',
                'permissions' => [
                    'record_inventory',
                    'record_waste',
                    'view_menu',
                    'update_stock_levels',
                ],
            ],
            // Additional staff with diverse languages
            [
                'name' => 'Carlos Rodriguez',
                'email' => 'chef@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'chef',
                'preferred_language' => 'es',
                'timezone' => 'America/Mexico_City',
                'permissions' => [
                    'manage_recipes',
                    'view_menu',
                    'record_production',
                    'view_inventory',
                ],
            ],
            [
                'name' => 'Marie Dubois',
                'email' => 'supervisor@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'supervisor',
                'preferred_language' => 'fr',
                'timezone' => 'Europe/Paris',
                'permissions' => [
                    'manage_staff',
                    'view_reports',
                    'manage_inventory',
                    'record_waste',
                ],
            ],
            [
                'name' => 'Hans Mueller',
                'email' => 'inventory@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'inventory_manager',
                'preferred_language' => 'de',
                'timezone' => 'Europe/Berlin',
                'permissions' => [
                    'manage_inventory',
                    'manage_suppliers',
                    'create_purchase_orders',
                    'record_deliveries',
                    'view_stock_reports',
                ],
            ],
            [
                'name' => 'João Silva',
                'email' => 'cashier@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'cashier',
                'preferred_language' => 'pt',
                'timezone' => 'America/Sao_Paulo',
                'permissions' => [
                    'process_orders',
                    'handle_payments',
                    'view_menu',
                ],
            ],
            [
                'name' => 'Dmitri Petrov',
                'email' => 'maintenance@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'maintenance',
                'preferred_language' => 'ru',
                'timezone' => 'Europe/Moscow',
                'permissions' => [
                    'record_equipment_issues',
                    'view_maintenance_logs',
                ],
            ],
            [
                'name' => 'Yuki Tanaka',
                'email' => 'analyst@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => 'data_analyst',
                'preferred_language' => 'ja',
                'timezone' => 'Asia/Tokyo',
                'permissions' => [
                    'view_all_reports',
                    'export_data',
                    'view_analytics',
                    'create_custom_reports',
                ],
            ],
        ];

        // Generate additional staff members with random profiles
        $additionalStaffProfiles = [
            ['name' => 'Michael Brown', 'role' => 'server', 'lang' => 'en', 'tz' => 'America/New_York'],
            ['name' => 'Isabella Garcia', 'role' => 'hostess', 'lang' => 'es', 'tz' => 'America/Mexico_City'],
            ['name' => 'Fatima Al-Zahra', 'role' => 'server', 'lang' => 'ar', 'tz' => 'Asia/Dubai'],
            ['name' => 'Chen Ming', 'role' => 'kitchen_assistant', 'lang' => 'zh', 'tz' => 'Asia/Shanghai'],
            ['name' => 'Priya Patel', 'role' => 'prep_cook', 'lang' => 'hi', 'tz' => 'Asia/Kolkata'],
            ['name' => 'Sophie Martin', 'role' => 'server', 'lang' => 'fr', 'tz' => 'Europe/Paris'],
            ['name' => 'Anna Schneider', 'role' => 'bartender', 'lang' => 'de', 'tz' => 'Europe/Berlin'],
            ['name' => 'Pedro Santos', 'role' => 'delivery_driver', 'lang' => 'pt', 'tz' => 'America/Sao_Paulo'],
            ['name' => 'Alexei Volkov', 'role' => 'security', 'lang' => 'ru', 'tz' => 'Europe/Moscow'],
            ['name' => 'Sakura Yamamoto', 'role' => 'server', 'lang' => 'ja', 'tz' => 'Asia/Tokyo'],
        ];

        foreach ($additionalStaffProfiles as $profile) {
            $userProfiles[] = [
                'name' => $profile['name'],
                'email' => strtolower(str_replace(' ', '.', $profile['name'])) . '@' . str_replace('.rmsaas.local', '', $tenant->domain) . '.com',
                'role' => $profile['role'],
                'preferred_language' => $profile['lang'],
                'timezone' => $profile['tz'],
                'permissions' => $this->getDefaultPermissionsForRole($profile['role']),
            ];
        }

        // Create users with realistic data
        foreach ($userProfiles as $profile) {
            $users[] = [
                'tenant_id' => $tenant->id,
                'name' => $profile['name'],
                'email' => $profile['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('Restaurant@2025'),
                'role' => $profile['role'],
                'preferred_language' => $profile['preferred_language'],
                'timezone' => $profile['timezone'],
                'permissions' => json_encode($profile['permissions']),
                'employee_id' => 'EMP' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'phone' => $this->generatePhoneNumber(),
                'hire_date' => now()->subDays(rand(30, 1800))->format('Y-m-d'),
                'address' => $this->generateAddress(),
                'last_login_at' => now()->subDays(rand(0, 30)),
                'last_login_ip' => '192.168.1.' . rand(100, 200),
                'is_active' => rand(0, 10) > 1, // 90% active
                'language_preferences' => json_encode([
                    'date_format' => $this->getDateFormatForLanguage($profile['preferred_language']),
                    'number_format' => $this->getNumberFormatForLanguage($profile['preferred_language']),
                    'currency_position' => $this->getCurrencyPositionForLanguage($profile['preferred_language']),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all users for this tenant
        DB::connection('landlord')->table('users')->insert($users);
    }

    private function getDefaultPermissionsForRole(string $role): array
    {
        $permissions = [
            'server' => ['view_menu', 'process_orders', 'handle_tables'],
            'hostess' => ['manage_reservations', 'seat_customers', 'answer_phone'],
            'kitchen_assistant' => ['view_orders', 'assist_cooking', 'clean_kitchen'],
            'prep_cook' => ['prep_ingredients', 'view_recipes', 'manage_prep_area'],
            'bartender' => ['manage_bar', 'prepare_drinks', 'manage_bar_inventory'],
            'delivery_driver' => ['view_delivery_orders', 'update_delivery_status', 'manage_routes'],
            'security' => ['monitor_premises', 'manage_access', 'incident_reporting'],
            'auditor' => ['view_all_reports', 'audit_transactions', 'compliance_checking'],
        ];

        return $permissions[$role] ?? ['basic_access'];
    }


    private function generatePhoneNumber(): string
    {
        return '+1-' . rand(200, 999) . '-' . rand(100, 999) . '-' . rand(1000, 9999);
    }

    private function generateAddress(): string
    {
        $streets = ['Main St', 'Oak Ave', 'First St', 'Second St', 'Park Blvd', 'Center St'];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia'];
        
        return rand(100, 9999) . ' ' . $streets[array_rand($streets)] . ', ' . 
               $cities[array_rand($cities)] . ', ' . 
               ['NY', 'CA', 'IL', 'TX', 'AZ', 'PA'][array_rand(['NY', 'CA', 'IL', 'TX', 'AZ', 'PA'])] . ' ' . 
               rand(10000, 99999);
    }

    private function getDateFormatForLanguage(string $language): string
    {
        $formats = [
            'en' => 'MM/DD/YYYY',
            'ar' => 'DD/MM/YYYY',
            'zh' => 'YYYY-MM-DD',
            'hi' => 'DD/MM/YYYY',
            'es' => 'DD/MM/YYYY',
            'fr' => 'DD/MM/YYYY',
            'de' => 'DD.MM.YYYY',
            'pt' => 'DD/MM/YYYY',
            'ru' => 'DD.MM.YYYY',
            'ja' => 'YYYY/MM/DD',
        ];

        return $formats[$language] ?? 'MM/DD/YYYY';
    }

    private function getNumberFormatForLanguage(string $language): string
    {
        $formats = [
            'en' => '1,234.56',
            'ar' => '1,234.56',
            'zh' => '1,234.56',
            'hi' => '1,23,456.78',
            'es' => '1.234,56',
            'fr' => '1 234,56',
            'de' => '1.234,56',
            'pt' => '1.234,56',
            'ru' => '1 234,56',
            'ja' => '1,234.56',
        ];

        return $formats[$language] ?? '1,234.56';
    }

    private function getCurrencyPositionForLanguage(string $language): string
    {
        $positions = [
            'en' => 'before',
            'ar' => 'after',
            'zh' => 'before',
            'hi' => 'before',
            'es' => 'after',
            'fr' => 'after',
            'de' => 'after',
            'pt' => 'before',
            'ru' => 'after',
            'ja' => 'before',
        ];

        return $positions[$language] ?? 'before';
    }
}