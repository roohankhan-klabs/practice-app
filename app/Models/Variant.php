<?php

namespace App\Models;

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
            ->sortBy(fn (VariantOption $option) => $positions[$option->id] ?? PHP_INT_MAX)
            ->values();
    }
}
