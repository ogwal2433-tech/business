<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'default_password',
        'shop_name',
        'shop_address',
        'shop_phone',
        'shop_email',
        'auto_stock_decrement',
        'system_language',
    ];
}
