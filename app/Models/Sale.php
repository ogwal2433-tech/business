<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
protected $fillable = [
    'employee_id',
    'user_id',
    'product_id',
    'quantity',
    'admin_id',
    'total_amount',
    'pieces_sold',
    'price_per_piece',
    'discount',
    'status',
    'unit',
    'client_name',
    'amount_paid',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
{
    return $this->belongsTo(Inventory::class);
}

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
}
