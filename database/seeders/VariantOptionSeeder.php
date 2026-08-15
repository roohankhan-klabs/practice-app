<?php

namespace Database\Seeders;

use App\Models\VariantOption;
use App\Models\VariantType;
use Illuminate\Database\Seeder;

class VariantOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variantTypes = VariantType::query()->pluck('id', 'key');

        $variantOptions = [
            [
                'variant_type_key' => 'color',
                'name' => 'Red',
                'hex_code' => '#ff0000',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'color',
                'name' => 'Green',
                'hex_code' => '#00ff00',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'color',
                'name' => 'Blue',
                'hex_code' => '#0000ff',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'size',
                'name' => 'Small',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'size',
                'name' => 'Medium',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'size',
                'name' => 'Large',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'material',
                'name' => 'Cotton',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'material',
                'name' => 'Polyester',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'material',
                'name' => 'Silk',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'weight',
                'name' => '1kg',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'weight',
                'name' => '2kg',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'weight',
                'name' => '3kg',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'dimensions',
                'name' => '10x10x10',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'dimensions',
                'name' => '20x20x20',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'dimensions',
                'name' => '30x30x30',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'voltage',
                'name' => '110V',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'voltage',
                'name' => '220V',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'storage',
                'name' => '128GB',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'storage',
                'name' => '256GB',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'storage',
                'name' => '512GB',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'ram',
                'name' => '4GB',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'ram',
                'name' => '8GB',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'ram',
                'name' => '16GB',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'warranty',
                'name' => '1 Year',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'warranty',
                'name' => '2 Year',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'warranty',
                'name' => '3 Year',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'pack_size',
                'name' => '1 Pack',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'pack_size',
                'name' => '2 Pack',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'pack_size',
                'name' => '3 Pack',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'subscription_period',
                'name' => '1 Month',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'subscription_period',
                'name' => '3 Month',
                'is_active' => true,
            ],
            [
                'variant_type_key' => 'subscription_period',
                'name' => '6 Month',
                'is_active' => true,
            ],
        ];
        foreach ($variantOptions as $variantOption) {
            if (! isset($variantOption['value'])) {
                $variantOption['value'] = strtolower($variantOption['name']);
            }

            $variantTypeId = $variantTypes[$variantOption['variant_type_key']] ?? null;

            if (! $variantTypeId) {
                continue;
            }

            unset($variantOption['variant_type_key']);
            $variantOption['variant_type_id'] = $variantTypeId;

            VariantOption::query()->updateOrCreate(
                [
                    'variant_type_id' => $variantOption['variant_type_id'],
                    'name' => $variantOption['name'],
                ],
                $variantOption,
            );
        }
    }
}
