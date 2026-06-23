<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    protected $fillable = [
        'sale_id',
        'amount',
        'next_installment_date',
        'note',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
