<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'name',
        'code',
        'icon',
        'fee_type',
        'fee_value',
        'sort_order',
        'is_active',
    ]
)]
class PaymentMethod extends Model
{
    public const VISA = 1;

    public const MASTERCARD = 2;

    public const CASH_ON_DELIVERY = 3;

    public const SAFEPAY = 4;

    public const JAZZCASH = 5;

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
