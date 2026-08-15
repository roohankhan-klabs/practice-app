<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'order_id',
        'user_id',
        'shop_id',
        'product_id',
        'rating',
        'title',
        'comment',
    ]
)]
class Review extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
