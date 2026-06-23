<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'quantity',
        'price_per_unit',
        'purchase_date',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
