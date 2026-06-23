<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update super admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'business_name' => 'SmartBiz System',
                'name' => 'System Admin',
                'email' => 'system@smartbiz.app',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        // Create default subscription plans
        $plans = [
            [
                'name' => 'Free Trial',
                'slug' => 'free-trial',
                'price' => 0,
                'duration_days' => 14,
                'max_employees' => 1,
                'description' => 'Try SmartBiz free for 14 days with 1 employee. Full access to all features.',
                'features' => json_encode([
                    'Sales recording', 'Inventory management', 'Basic reports',
                    'Advanced analytics', 'AI Assistant', 'Priority support',
                    'Credit Sales', 'Messages', 'Financial Position', 'Email support',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 10000,
                'duration_days' => 30,
                'max_employees' => 3,
                'description' => 'For small businesses with up to 3 employees.',
                'features' => json_encode(['Sales recording', 'Inventory management', 'Basic reports', 'Email support']),
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 25000,
                'duration_days' => 30,
                'max_employees' => 0,
                'description' => 'Unlimited employees and access to all features.',
                'features' => json_encode([
                    'Sales recording', 'Inventory management', 'Basic reports',
                    'Unlimited employees', 'Advanced analytics', 'AI Assistant',
                    'Priority support', 'Credit Sales', 'Messages',
                    'Financial Position', 'Email support',
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        // Remove Enterprise plan if it exists from previous seed
        SubscriptionPlan::where('slug', 'enterprise')->delete();

        // Seed demo businesses
        $this->call(DemoDataSeeder::class);
    }
}
