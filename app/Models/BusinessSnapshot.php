<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSnapshot extends Model
{
    protected $fillable = [
        'admin_id',
        'snapshot_date',
        'inventory_value',
        'cumulative_sales',
        'cumulative_expenses',
        'net_worth',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'inventory_value' => 'float',
        'cumulative_sales' => 'float',
        'cumulative_expenses' => 'float',
        'net_worth' => 'float',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
