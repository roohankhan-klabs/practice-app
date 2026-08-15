<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'user_id',
        'payment_method_id',
        'reference_type',
        'reference_id',
        'transaction_id',
        'tap_charge_id',
        'tap_tracking_id',
        'status',
        'paid_at',
    ]
)]
class Payment extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
