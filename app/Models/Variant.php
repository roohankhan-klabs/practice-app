<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

/**
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property string $slug
 * @property string $sku
 * @property float $price
 * @property int $quantity
 * @property bool $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 */
#[Fillable(['product_id', 'values', 'price', 'stock'])]
class Variant extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
