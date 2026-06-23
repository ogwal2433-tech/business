<?php

namespace App\Models;
use App\Models\Account;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'business_name',
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'admin_id',
        'currency',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'employee_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'admin_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getCurrencyAttribute($value)
    {
        if ($this->isEmployee()) {
            return optional($this->admin)->getRawOriginal('currency') ?: 'UGX';
        }
        return $value ?: 'UGX';
    }

    public function subscription()
    {
        return $this->hasOne(BusinessSubscription::class, 'business_admin_id')->latestOfMany();
    }

    public function allSubscriptions()
    {
        return $this->hasMany(BusinessSubscription::class, 'business_admin_id');
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->isSuperAdmin()) return true;
        $sub = $this->subscription;
        if (!$sub) return false;
        return in_array($sub->status, ['active', 'trial']) && (!$sub->end_date || $sub->end_date->isFuture());
    }

    public function hasPendingSubscription(): bool
    {
        if ($this->isSuperAdmin()) return false;
        $sub = $this->subscription;
        if (!$sub) return false;
        return $sub->status === 'pending';
    }

    public function planMaxEmployees(): int
    {
        return $this->subscription?->plan?->max_employees ?? 0;
    }

    public function currentEmployeeCount(): int
    {
        return $this->employees()->count();
    }

    public function canAddMoreEmployees(): bool
    {
        $max = $this->planMaxEmployees();
        if ($max === 0) return true;
        return $this->currentEmployeeCount() < $max;
    }

    public function planHasFeature(string $feature): bool
    {
        return $this->subscription?->plan?->hasFeature($feature) ?? false;
    }

    public function employeeExpenses()
    {
        return $this->hasManyThrough(
            Expense::class,
            User::class,
            'admin_id',
            'employee_id',
            'id',
            'id'
        );
    }
}
