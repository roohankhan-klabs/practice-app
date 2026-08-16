<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Models\SubCategory;
use App\Models\Variant;
use App\Models\VariantOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variantOptionIds = VariantOption::query()
            ->join('variant_types', 'variant_types.id', '=', 'variant_options.variant_type_id')
            ->select('variant_options.id', 'variant_options.name', 'variant_types.key')
            ->get()
            ->mapWithKeys(fn ($variantOption) => [
                $variantOption->key.':'.$variantOption->name => $variantOption->id,
            ]);

        $products = [
            [
                'shop_name' => 'Alpha Gadgets',
                'category' => 'Electronics',
                'sub_category' => 'Mobiles',
                'name' => 'Nova X Pro Phone',
                'slug' => 'nova-x-pro-phone',
                'price' => 32999,
                'shipping_price' => 250,
                'stock' => 40,
                'is_featured' => true,
                'variants' => [
                    ['values' => ['color' => 'Red', 'storage' => '128GB'], 'price' => 32999, 'stock' => 18],
                    ['values' => ['color' => 'Blue', 'storage' => '256GB'], 'price' => 36999, 'stock' => 12],
                ],
            ],
            [
                'shop_name' => 'Alpha Gadgets',
                'category' => 'Electronics',
                'sub_category' => 'Laptops',
                'name' => 'Orbit Air Laptop',
                'slug' => 'orbit-air-laptop',
                'price' => 124999,
                'shipping_price' => 500,
                'stock' => 15,
                'is_featured' => true,
                'variants' => [
                    ['values' => ['ram' => '8GB', 'storage' => '256GB'], 'price' => 124999, 'stock' => 8],
                    ['values' => ['ram' => '16GB', 'storage' => '512GB'], 'price' => 149999, 'stock' => 5],
                ],
            ],
            [
                'shop_name' => 'Alpha Gadgets',
                'category' => 'Electronics',
                'sub_category' => 'Tablets',
                'name' => 'Pixel Slate Go',
                'slug' => 'pixel-slate-go',
                'price' => 45999,
                'shipping_price' => 300,
                'stock' => 25,
                'is_featured' => false,
                'variants' => [
                    ['values' => ['color' => 'Green', 'storage' => '128GB'], 'price' => 45999, 'stock' => 10],
                    ['values' => ['color' => 'Blue', 'storage' => '256GB'], 'price' => 51999, 'stock' => 9],
                ],
            ],
            [
                'shop_name' => 'Desert Living',
                'category' => 'Beauty',
                'sub_category' => 'Perfumes',
                'name' => 'Amber Dusk Perfume',
                'slug' => 'amber-dusk-perfume',
                'price' => 8900,
                'shipping_price' => 150,
                'stock' => 60,
                'is_featured' => true,
                'variants' => [
                    ['values' => ['pack_size' => '1 Pack'], 'price' => 8900, 'stock' => 30],
                    ['values' => ['pack_size' => '2 Pack'], 'price' => 16900, 'stock' => 14],
                ],
            ],
            [
                'shop_name' => 'Desert Living',
                'category' => 'Health',
                'sub_category' => 'Supplements',
                'name' => 'Daily Balance Supplements',
                'slug' => 'daily-balance-supplements',
                'price' => 4200,
                'shipping_price' => 100,
                'stock' => 70,
                'is_featured' => false,
                'variants' => [
                    ['values' => ['pack_size' => '1 Pack'], 'price' => 4200, 'stock' => 35],
                    ['values' => ['pack_size' => '3 Pack'], 'price' => 11900, 'stock' => 18],
                ],
            ],
            [
                'shop_name' => 'Desert Living',
                'category' => 'Travel',
                'sub_category' => 'Outdoor',
                'name' => 'TrailLite Carry Pack',
                'slug' => 'traillite-carry-pack',
                'price' => 11500,
                'shipping_price' => 175,
                'stock' => 28,
                'is_featured' => false,
                'variants' => [
                    ['values' => ['color' => 'Red', 'size' => 'Medium'], 'price' => 11500, 'stock' => 12],
                    ['values' => ['color' => 'Blue', 'size' => 'Large'], 'price' => 12400, 'stock' => 8],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $shop = Shop::query()->where('shop_name', $productData['shop_name'])->firstOrFail();
            $category = Category::query()->where('name', $productData['category'])->firstOrFail();
            $subCategory = SubCategory::query()
                ->where('category_id', $category->id)
                ->where('name', $productData['sub_category'])
                ->firstOrFail();

            $product = Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'shop_id' => $shop->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $productData['name'],
                    'description' => 'Demo product seeded for local development and API exploration.',
                    'specifications' => json_encode([
                        'brand' => Str::before($productData['name'], ' '),
                        'warranty' => '1 year',
                        'support' => 'Local support available',
                    ]),
                    'price' => $productData['price'],
                    'shipping_price' => $productData['shipping_price'],
                    'stock' => $productData['stock'],
                    'low_stock_threshold' => 5,
                    'discount_type' => 'percentage',
                    'discount_value' => 10,
                    'show_in_app' => true,
                    'is_featured' => $productData['is_featured'],
                    'status' => 'active',
                    'reviewed_by' => 'seed-bot',
                    'reviewed_at' => now()->subDay(),
                    'rejection_reason' => null,
                ],
            );

            foreach ([1, 2] as $sortOrder) {
                ProductImage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'sort_order' => $sortOrder,
                    ],
                    [
                        'image' => $product->slug."-{$sortOrder}.jpg",
                        'alt_text' => $product->name.' image '.$sortOrder,
                        'is_thumbnail' => $sortOrder === 1,
                    ],
                );
            }

            foreach ($productData['variants'] as $variantData) {
                $optionIds = collect($variantData['values'])
                    ->map(fn (string $optionName, string $variantTypeKey) => $variantOptionIds->get($variantTypeKey.':'.$optionName))
                    ->sort()
                    ->values()
                    ->all();

                Variant::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variant_option_ids' => $optionIds,
                    ],
                    [
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ],
                );
            }
        }
    }
}
