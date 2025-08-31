<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small restaurants and cafes just getting started with inventory management.',
                'monthly_price' => 29.99,
                'yearly_price' => 299.99,
                'features' => json_encode([
                    'Up to 2 locations',
                    'Up to 5 users',
                    'Basic inventory tracking',
                    'Menu management',
                    'Basic reporting',
                    'Email support',
                    '1GB storage',
                    'Mobile app access',
                ]),
                'limits' => json_encode([
                    'locations' => 2,
                    'users' => 5,
                    'menu_items' => 200,
                    'inventory_items' => 500,
                    'storage_gb' => 1,
                    'api_calls_per_month' => 10000,
                    'reports_per_month' => 50,
                ]),
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing restaurants with multiple locations and advanced analytics needs.',
                'monthly_price' => 79.99,
                'yearly_price' => 799.99,
                'features' => json_encode([
                    'Up to 10 locations',
                    'Up to 25 users',
                    'Advanced inventory tracking',
                    'Recipe management',
                    'Waste tracking and analysis',
                    'Purchase order management',
                    'Advanced reporting & analytics',
                    'POS integrations',
                    'Phone & email support',
                    '10GB storage',
                    'Mobile app access',
                    'Custom branding',
                ]),
                'limits' => json_encode([
                    'locations' => 10,
                    'users' => 25,
                    'menu_items' => 1000,
                    'inventory_items' => 5000,
                    'recipes' => 500,
                    'storage_gb' => 10,
                    'api_calls_per_month' => 100000,
                    'reports_per_month' => 500,
                    'pos_integrations' => 3,
                ]),
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Comprehensive solution for large restaurant chains with advanced AI-powered insights.',
                'monthly_price' => 199.99,
                'yearly_price' => 1999.99,
                'features' => json_encode([
                    'Unlimited locations',
                    'Unlimited users',
                    'Complete inventory management',
                    'Advanced recipe costing',
                    'AI-powered loss prevention',
                    'Predictive analytics',
                    'Multi-currency support',
                    'Advanced supplier management',
                    'Custom integrations',
                    'White-label options',
                    '24/7 priority support',
                    '100GB storage',
                    'API access',
                    'Custom reporting',
                    'Advanced user permissions',
                ]),
                'limits' => json_encode([
                    'locations' => -1, // unlimited
                    'users' => -1, // unlimited
                    'menu_items' => -1,
                    'inventory_items' => -1,
                    'recipes' => -1,
                    'storage_gb' => 100,
                    'api_calls_per_month' => 1000000,
                    'reports_per_month' => -1,
                    'pos_integrations' => -1,
                    'custom_integrations' => true,
                ]),
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Custom',
                'slug' => 'custom',
                'description' => 'Tailored solution for enterprises with specific requirements and dedicated support.',
                'monthly_price' => 0.00, // Contact for pricing
                'yearly_price' => 0.00,
                'features' => json_encode([
                    'Custom feature development',
                    'Dedicated infrastructure',
                    'Custom SLA agreements',
                    'On-premise deployment options',
                    'Advanced security features',
                    'Dedicated account manager',
                    'Custom training programs',
                    'Integration consultation',
                    'Performance optimization',
                    'Disaster recovery planning',
                    'Compliance assistance',
                    'Custom analytics',
                ]),
                'limits' => json_encode([
                    'custom_pricing' => true,
                    'everything_included' => true,
                    'dedicated_resources' => true,
                    'sla_guaranteed' => true,
                ]),
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            $plan['created_at'] = now();
            $plan['updated_at'] = now();
        }

        DB::connection('landlord')->table('subscription_plans')->insert($plans);
    }
}