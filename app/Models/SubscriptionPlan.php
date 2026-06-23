<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    const FEATURE_MAP = [
        'ai_assistant'       => ['AI Assistant', 'AI assistant'],
        'advanced_analytics' => ['Advanced Analytics', 'Advanced analytics', 'Advanced reports'],
        'financial_position' => ['Financial Position', 'Financial position'],
        'credit_sales'       => ['Credit Sales', 'Credit sales'],
        'messages'           => ['Messages', 'messaging'],
        'basic_reports'      => ['Basic reports', 'Basic Reports'],
        'email_support'      => ['Email support', 'Email Support'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration_days',
        'max_employees',
        'description',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'float',
    ];

    public function subscriptions()
    {
        return $this->hasMany(BusinessSubscription::class, 'plan_id');
    }

    public function hasFeature(string $key): bool
    {
        $features = $this->features;
        if (is_string($features)) {
            $features = json_decode($features, true) ?? [];
        }
        $features = $features ?? [];
        $labels = self::FEATURE_MAP[$key] ?? [];
        foreach ($labels as $label) {
            if (in_array($label, $features)) {
                return true;
            }
        }
        return false;
    }
}
