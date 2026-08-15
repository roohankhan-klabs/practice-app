<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'user_id',
        'shop_id',
        'reference',
        'address_id',
        'subtotal',
        'shipping_fees',
        'tax',
        'discount',
        'total_amount',
        'payment_method_id',
        'payment_id',
        'paid_at',
        'status',
    ]
)]
class Order extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
