<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'user_id',
        'shop_id',
        'address_id',
        'payment_id',
        'reference',
        'subtotal',
        'shipping_fees',
        'tax',
        'discount',
        'total_amount',
        'status',
    ]
)]
class Order extends Model
{
    public const INITIALIZED = 'initialized';
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const PROCESSING = 'processing';
    public const READY_FOR_DELIVERY = 'ready_for_delivery';
    public const OUT_FOR_DELIVERY = 'out_for_delivery';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';
    public const RETURNED = 'returned';
    public const NOT_DELIVERED = 'not_delivered';

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
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
