<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property string $slug
 * @property string $sku
 * @property float $price
 * @property int $quantity
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 */
#[Fillable(['product_id', 'variant_option_ids', 'price', 'stock'])]
class Variant extends Model
{
    protected $appends = ['variant_options_summary', 'is_in_cart', 'final_price'];

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

    public function getVariantOptionsSummaryAttribute(): string
    {
        return $this->variantOptions()
            ->map(function (VariantOption $option): string {
                $typeName = $option->variantType?->name;

                return filled($typeName)
                    ? "{$typeName}: {$option->name}"
                    : $option->name;
            })
            ->implode(', ');
    }

    public function variantOptions(): Collection
    {
        $variantOptionIds = $this->variant_option_ids ?? [];

        if ($variantOptionIds === []) {
            return collect();
        }

        $positions = array_flip($variantOptionIds);

        return VariantOption::query()
            ->with('variantType')
            ->whereIn('id', $variantOptionIds)
            ->get()
            ->sortBy(fn(VariantOption $option) => $positions[$option->id] ?? PHP_INT_MAX)
            ->values();
    }

    public function getIsInCartAttribute(): bool
    {
        $userId = auth('sanctum')->user()->id ?? null;
        if ($userId === null) {
            return false;
        }

        return Cart::where('user_id', $userId)
            ->whereHas('cartItems', function ($query) {
                $query->where('product_id', $this->product_id)->where('variant_id', $this->id);
            })
            ->exists();
    }
    public function getFinalPriceAttribute()
    {
        if ($this->product->discount_type === 'percentage') {
            return $this->price - ($this->price * $this->product->discount_value / 100);
        } else if ($this->product->discount_type === 'fixed') {
            return $this->price - $this->product->discount_value;
        } else {
            return $this->price;
        }
    }
}
