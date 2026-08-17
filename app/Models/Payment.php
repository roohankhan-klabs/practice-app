<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'payment_method_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'response',
        'paid_at',
        'tracker'
    ]
)]
class Payment extends Model
{
    protected $casts = [
        'tracker' => 'array',
        'response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
