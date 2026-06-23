<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSubscription extends Model
{
    protected $fillable = [
        'business_admin_id',
        'plan_id',
        'start_date',
        'end_date',
        'trial_ends_at',
        'status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    public function businessAdmin()
    {
        return $this->belongsTo(User::class, 'business_admin_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentTransaction::class, 'subscription_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === 'trial';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->end_date && $this->end_date->isPast());
    }
}
