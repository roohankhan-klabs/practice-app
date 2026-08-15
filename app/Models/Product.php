<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $shop_id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property float $price
 * @property int $stock
 * @property int $status
 */
#[Fillable(
    [
        'shop_id',
        'sub_category_id',
        'name',
        'slug',
        'description',
        'specification',
        'price',
        'shipping_price',
        'stock',
        'low_stock_threshold',
        'discount_type',
        'discount_value',
        'show_in_app',
        'is_featured',
        'status',
        'reviewed_by',
        'rejection_reason',
    ]
)]
class Product extends Model
{
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

