<?php

namespace App\Console\Commands;

use App\Models\BusinessSubscription;
use App\Models\User;
use App\Notifications\SubscriptionExpired;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired {--dry-run : List expired subs without updating}';
    protected $description = 'Mark subscriptions as expired when end_date has passed';

    public function handle()
    {
        $expired = BusinessSubscription::whereIn('status', ['active', 'trial'])
            ->where('end_date', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return 0;
        }

        $this->info("Found {$expired->count()} expired subscription(s).");

        foreach ($expired as $sub) {
            $admin = User::find($sub->business_admin_id);
            $adminName = $admin ? $admin->name . ' (' . $admin->business_name . ')' : 'Unknown';
            $this->line("  - {$sub->id}: {$adminName} ended {$sub->end_date->format('d M Y')}");

            if ($this->option('dry-run')) {
                continue;
            }

            $sub->update(['status' => 'expired']);

            try {
                $admin?->notify(new SubscriptionExpired($sub));
            } catch (\Throwable $e) {
                $this->warn("  Failed to notify {$adminName}: {$e->getMessage()}");
            }
        }

        if (!$this->option('dry-run')) {
            $this->info("Expired subscriptions marked and admins notified.");
        }

        return 0;
    }
}
