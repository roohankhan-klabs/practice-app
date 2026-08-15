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
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
