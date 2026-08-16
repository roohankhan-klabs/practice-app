<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'order_id',
        'payment_method_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'response',
        'paid_at',
    ]
)]
class Payment extends Model
{
    protected $casts = [
        'response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
