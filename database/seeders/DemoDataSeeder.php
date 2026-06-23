<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\BusinessSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Richard's business
        $richard = User::create([
            'business_name' => 'Richard Enterprises',
            'name' => 'Richard Ogwal',
            'username' => 'richard',
            'email' => 'richard@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create another business
        $mary = User::create([
            'business_name' => 'Mary Fashion Store',
            'name' => 'Mary Nansubuga',
            'username' => 'mary',
            'email' => 'mary@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create one more
        $john = User::create([
            'business_name' => 'John Hardware Solutions',
            'name' => 'John Mukasa',
            'username' => 'john',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Assign subscriptions
        $plans = SubscriptionPlan::all();

        BusinessSubscription::create([
            'business_admin_id' => $richard->id,
            'plan_id' => $plans->where('slug', 'pro')->first()->id,
            'start_date' => now()->subDays(15),
            'end_date' => now()->addDays(15),
            'status' => 'active',
        ]);

        BusinessSubscription::create([
            'business_admin_id' => $mary->id,
            'plan_id' => $plans->where('slug', 'basic')->first()->id,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
            'status' => 'active',
        ]);

        BusinessSubscription::create([
            'business_admin_id' => $john->id,
            'plan_id' => $plans->where('slug', 'free-trial')->first()->id,
            'start_date' => now()->subDays(13),
            'end_date' => now()->addDay(),
            'status' => 'trial',
        ]);

        $this->command->info('Created 3 demo businesses with subscriptions.');
    }
}
