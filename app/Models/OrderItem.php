<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'order_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
        'total_price',
        'discount_amount',
        'discount_percentage',
        'sub_total',
        'shipping_price',
        'tax_price',
        'grand_total',
        'status',
        'is_active',
    ]
)]
class OrderItem extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // public function variant()
    // {
    //     return $this->belongsTo(Variant::class);
    // }
}
