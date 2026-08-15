<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
#[Fillable(['product_id', 'variant_option_ids', 'price', 'stock'])]
class Variant extends Model
{
    protected function casts(): array
    {
        return [
            'variant_option_ids' => 'array',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantoptions()
    {
        return $this->belongsToMany(VariantOption::class, 'variant_option_ids');
    }
}
