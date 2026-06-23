<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
protected $fillable = [
    'purchase_price',
    'price',
    'purchase_price_per_dozen',
    'selling_price_per_dozen',
    'purchase_price_per_carton',
    'selling_price_per_carton',
];
}
