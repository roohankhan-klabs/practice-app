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
        'specifications',
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
        'reviewed_at',
        'rejection_reason',
    ]
)]
class Product extends Model
{
    protected $appends = ['is_in_cart', 'is_in_wishlist', 'final_price'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function getIsInCartAttribute()
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return false;
        }

        return Cart::where('user_id', $user->id)
            ->whereHas('cartItems', function ($query) {
                $query->where('product_id', $this->id);
            })
            ->exists();
    }
    public function getIsInWishlistAttribute()
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return false;
        }

        return Wishlist::where('user_id', $user->id)
            ->where('product_id', $this->id)
            ->exists();
    }
    public function getFinalPriceAttribute(){
        if ($this->discount_type === 'percentage') {
            return $this->price - ($this->price * $this->discount_value / 100);
        } else if ($this->discount_type === 'fixed') {
            return $this->price - $this->discount_value;
        } else {
            return $this->price;
        }
    }
}
