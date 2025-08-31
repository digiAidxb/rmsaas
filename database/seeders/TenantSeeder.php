<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Get some countries and subscription plans for reference
        $countries = DB::connection('landlord')->table('countries')->pluck('id', 'code');
        $subscriptionPlans = DB::connection('landlord')->table('subscription_plans')->pluck('id', 'slug');
        $adminUsers = DB::connection('landlord')->table('admin_users')->pluck('id');

        $tenants = [
            [
                'name' => 'Bella Vista Italian Restaurant',
                'domain' => 'bellavista.rmsaas.local',
                'database' => 'tenant_bellavista',
                'country_id' => $countries['US'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['professional'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'takeaway', 'delivery']),
                'business_type' => 'Italian Restaurant',
                'contact_person' => 'Marco Rossi',
                'phone' => '+1-555-0123',
                'business_address' => '123 Little Italy Street, New York, NY 10012',
                'city' => 'New York',
                'postal_code' => '10012',
                'trial_ends_at' => now()->addDays(30),
                'approved_at' => now()->subDays(15),
                'approved_by' => $adminUsers->first(),
                'usage_limits' => json_encode([
                    'locations' => 10,
                    'users' => 25,
                    'storage_gb' => 10,
                ]),
                'usage_current' => json_encode([
                    'locations' => 2,
                    'users' => 8,
                    'storage_gb' => 2.3,
                ]),
                'last_activity_at' => now()->subMinutes(30),
                'is_active' => true,
            ],
            [
                'name' => 'Sakura Sushi & Ramen',
                'domain' => 'sakurasushi.rmsaas.local',
                'database' => 'tenant_sakura',
                'country_id' => $countries['CA'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['enterprise'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'takeaway', 'drive_through']),
                'business_type' => 'Japanese Restaurant',
                'contact_person' => 'Hiroshi Tanaka',
                'phone' => '+1-416-555-0234',
                'business_address' => '456 Queen Street West, Toronto, ON M5V 2A8',
                'city' => 'Toronto',
                'postal_code' => 'M5V 2A8',
                'trial_ends_at' => null,
                'approved_at' => now()->subDays(60),
                'approved_by' => $adminUsers->first(),
                'usage_limits' => json_encode([
                    'locations' => -1,
                    'users' => -1,
                    'storage_gb' => 100,
                ]),
                'usage_current' => json_encode([
                    'locations' => 5,
                    'users' => 35,
                    'storage_gb' => 15.7,
                ]),
                'last_activity_at' => now()->subMinutes(5),
                'is_active' => true,
            ],
            [
                'name' => 'Le Petit Café',
                'domain' => 'lepetitcafe.rmsaas.local',
                'database' => 'tenant_lepetit',
                'country_id' => $countries['FR'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['starter'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'takeaway']),
                'business_type' => 'French Café',
                'contact_person' => 'Marie Dubois',
                'phone' => '+33-1-42-36-12-34',
                'business_address' => '78 Rue de Rivoli, 75001 Paris',
                'city' => 'Paris',
                'postal_code' => '75001',
                'trial_ends_at' => now()->addDays(15),
                'approved_at' => now()->subDays(10),
                'approved_by' => $adminUsers->skip(1)->first(),
                'usage_limits' => json_encode([
                    'locations' => 2,
                    'users' => 5,
                    'storage_gb' => 1,
                ]),
                'usage_current' => json_encode([
                    'locations' => 1,
                    'users' => 3,
                    'storage_gb' => 0.5,
                ]),
                'last_activity_at' => now()->subHours(2),
                'is_active' => true,
            ],
            [
                'name' => 'Al Bayt Restaurant',
                'domain' => 'albayt.rmsaas.local',
                'database' => 'tenant_albayt',
                'country_id' => $countries['AE'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['professional'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'delivery', 'catering']),
                'business_type' => 'Middle Eastern Restaurant',
                'contact_person' => 'Ahmed Al Mahmoud',
                'phone' => '+971-4-123-4567',
                'business_address' => 'Dubai Mall, Downtown Dubai, Dubai',
                'city' => 'Dubai',
                'postal_code' => '00000',
                'trial_ends_at' => null,
                'approved_at' => now()->subDays(90),
                'approved_by' => $adminUsers->first(),
                'usage_limits' => json_encode([
                    'locations' => 10,
                    'users' => 25,
                    'storage_gb' => 10,
                ]),
                'usage_current' => json_encode([
                    'locations' => 3,
                    'users' => 15,
                    'storage_gb' => 5.2,
                ]),
                'last_activity_at' => now()->subMinutes(10),
                'is_active' => true,
            ],
            [
                'name' => 'Taj Mahal Indian Cuisine',
                'domain' => 'tajmahal.rmsaas.local',
                'database' => 'tenant_tajmahal',
                'country_id' => $countries['IN'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['professional'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'takeaway', 'delivery']),
                'business_type' => 'Indian Restaurant',
                'contact_person' => 'Rajesh Kumar',
                'phone' => '+91-11-2345-6789',
                'business_address' => 'Connaught Place, New Delhi 110001',
                'city' => 'New Delhi',
                'postal_code' => '110001',
                'trial_ends_at' => now()->addDays(20),
                'approved_at' => now()->subDays(5),
                'approved_by' => $adminUsers->skip(2)->first(),
                'usage_limits' => json_encode([
                    'locations' => 10,
                    'users' => 25,
                    'storage_gb' => 10,
                ]),
                'usage_current' => json_encode([
                    'locations' => 1,
                    'users' => 6,
                    'storage_gb' => 1.8,
                ]),
                'last_activity_at' => now()->subMinutes(45),
                'is_active' => true,
            ],
            [
                'name' => 'Dragon Palace Chinese Restaurant',
                'domain' => 'dragonpalace.rmsaas.local',
                'database' => 'tenant_dragonpalace',
                'country_id' => $countries['AU'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['enterprise'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'takeaway', 'delivery', 'catering']),
                'business_type' => 'Chinese Restaurant',
                'contact_person' => 'Li Wei Chen',
                'phone' => '+61-2-9876-5432',
                'business_address' => '88 George Street, Sydney NSW 2000',
                'city' => 'Sydney',
                'postal_code' => '2000',
                'trial_ends_at' => null,
                'approved_at' => now()->subDays(120),
                'approved_by' => $adminUsers->first(),
                'usage_limits' => json_encode([
                    'locations' => -1,
                    'users' => -1,
                    'storage_gb' => 100,
                ]),
                'usage_current' => json_encode([
                    'locations' => 8,
                    'users' => 45,
                    'storage_gb' => 25.4,
                ]),
                'last_activity_at' => now()->subMinutes(20),
                'is_active' => true,
            ],
            [
                'name' => 'Burger Junction',
                'domain' => 'burgerjunction.rmsaas.local',
                'database' => 'tenant_burgerjunction',
                'country_id' => $countries['US'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['starter'] ?? null,
                'status' => 'pending',
                'service_types' => json_encode(['dine_in', 'takeaway', 'drive_through']),
                'business_type' => 'Fast Food Restaurant',
                'contact_person' => 'Mike Johnson',
                'phone' => '+1-555-0789',
                'business_address' => '789 Main Street, Austin, TX 73301',
                'city' => 'Austin',
                'postal_code' => '73301',
                'trial_ends_at' => now()->addDays(30),
                'approved_at' => null,
                'approved_by' => null,
                'usage_limits' => json_encode([
                    'locations' => 2,
                    'users' => 5,
                    'storage_gb' => 1,
                ]),
                'usage_current' => json_encode([
                    'locations' => 0,
                    'users' => 1,
                    'storage_gb' => 0.1,
                ]),
                'last_activity_at' => now()->subDays(1),
                'is_active' => false,
            ],
            [
                'name' => 'Mediterranean Delights',
                'domain' => 'meddelights.rmsaas.local',
                'database' => 'tenant_meddelights',
                'country_id' => $countries['GB'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['professional'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'takeaway', 'catering']),
                'business_type' => 'Mediterranean Restaurant',
                'contact_person' => 'Dimitri Papadopoulos',
                'phone' => '+44-20-7123-4567',
                'business_address' => '45 Greek Street, Soho, London W1D 4EE',
                'city' => 'London',
                'postal_code' => 'W1D 4EE',
                'trial_ends_at' => null,
                'approved_at' => now()->subDays(45),
                'approved_by' => $adminUsers->skip(1)->first(),
                'usage_limits' => json_encode([
                    'locations' => 10,
                    'users' => 25,
                    'storage_gb' => 10,
                ]),
                'usage_current' => json_encode([
                    'locations' => 2,
                    'users' => 12,
                    'storage_gb' => 4.1,
                ]),
                'last_activity_at' => now()->subHours(1),
                'is_active' => true,
            ],
            [
                'name' => 'Taco Fiesta',
                'domain' => 'tacofiesta.rmsaas.local',
                'database' => 'tenant_tacofiesta',
                'country_id' => $countries['MX'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['starter'] ?? null,
                'status' => 'approved',
                'service_types' => json_encode(['dine_in', 'takeaway', 'food_truck']),
                'business_type' => 'Mexican Restaurant',
                'contact_person' => 'Carlos Rodriguez',
                'phone' => '+52-55-1234-5678',
                'business_address' => 'Calle de la Reforma 123, Centro, México DF',
                'city' => 'Mexico City',
                'postal_code' => '06000',
                'trial_ends_at' => now()->addDays(25),
                'approved_at' => now()->subDays(20),
                'approved_by' => $adminUsers->skip(2)->first(),
                'usage_limits' => json_encode([
                    'locations' => 2,
                    'users' => 5,
                    'storage_gb' => 1,
                ]),
                'usage_current' => json_encode([
                    'locations' => 1,
                    'users' => 4,
                    'storage_gb' => 0.7,
                ]),
                'last_activity_at' => now()->subMinutes(90),
                'is_active' => true,
            ],
            [
                'name' => 'The Coffee Bean House',
                'domain' => 'coffeebeanhouse.rmsaas.local',
                'database' => 'tenant_coffeebean',
                'country_id' => $countries['CA'] ?? null,
                'subscription_plan_id' => $subscriptionPlans['starter'] ?? null,
                'status' => 'suspended',
                'service_types' => json_encode(['dine_in', 'takeaway']),
                'business_type' => 'Coffee Shop',
                'contact_person' => 'Sarah Thompson',
                'phone' => '+1-604-555-0987',
                'business_address' => '321 Granville Street, Vancouver, BC V6C 1T1',
                'city' => 'Vancouver',
                'postal_code' => 'V6C 1T1',
                'trial_ends_at' => now()->subDays(5),
                'approved_at' => now()->subDays(90),
                'approved_by' => $adminUsers->skip(1)->first(),
                'rejection_reason' => 'Payment failed - subscription expired',
                'usage_limits' => json_encode([
                    'locations' => 2,
                    'users' => 5,
                    'storage_gb' => 1,
                ]),
                'usage_current' => json_encode([
                    'locations' => 1,
                    'users' => 2,
                    'storage_gb' => 0.3,
                ]),
                'last_activity_at' => now()->subDays(10),
                'is_active' => false,
            ],
        ];

        // Insert tenants and generate database credentials
        foreach ($tenants as $tenantData) {
            $tenantData['created_at'] = now();
            $tenantData['updated_at'] = now();
            
            // Generate secure database credentials for each tenant
            $domain = $tenantData['domain'];
            $tenantId = substr(md5($domain), 0, 8);
            
            $tenantData['db_username'] = 'tenant_' . $tenantId;
            $tenantData['db_password'] = bin2hex(random_bytes(16));
            $tenantData['db_host'] = '127.0.0.1';
            $tenantData['db_port'] = 3306;

            DB::connection('landlord')->table('tenants')->insert($tenantData);
        }
    }
}
