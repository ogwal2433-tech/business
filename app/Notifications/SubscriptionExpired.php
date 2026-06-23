<?php

namespace App\Notifications;

use App\Models\BusinessSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionExpired extends Notification
{
    use Queueable;

    public function __construct(public BusinessSubscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $planName = $this->subscription->plan?->name ?? 'N/A';
        return [
            'title' => 'Subscription Expired',
            'message' => "Your {$planName} subscription has expired. Renew now to restore access.",
            'subscription_id' => $this->subscription->id,
            'ended_at' => $this->subscription->end_date?->toDateTimeString(),
            'action_url' => route('admin.subscription.my'),
        ];
    }
}
