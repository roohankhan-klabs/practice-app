<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'payment_id',
        'order_id',
        'gateway',
        'charge_id',
        'event_type',
        'amount',
        'currency',
        'status',
        'payload',
        'response',
        'ip_address',
        'paid_at',
    ]
)]
class PaymentLog extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
