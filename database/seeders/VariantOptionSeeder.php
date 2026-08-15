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
        $variantOptions = [
            [
                'variant_type_id' => 1,
                'name' => 'Red',
                'hex_code' => '#ff0000',
                'is_active' => true
            ],
            [
                'variant_type_id' => 1,
                'name' => 'Green',
                'hex_code' => '#00ff00',
                'is_active' => true
            ],
            [
                'variant_type_id' => 1,
                'name' => 'Blue',
                'hex_code' => '#0000ff',
                'is_active' => true
            ],
            [
                'variant_type_id' => 2,
                'name' => 'Small',
                'is_active' => true
            ],
            [
                'variant_type_id' => 2,
                'name' => 'Medium',
                'is_active' => true
            ],
            [
                'variant_type_id' => 2,
                'name' => 'Large',
                'is_active' => true
            ],
            [
                'variant_type_id' => 3,
                'name' => 'Cotton',
                'is_active' => true
            ],
            [
                'variant_type_id' => 3,
                'name' => 'Polyester',
                'is_active' => true
            ],
            [
                'variant_type_id' => 3,
                'name' => 'Silk',
                'is_active' => true
            ],
            [
                'variant_type_id' => 4,
                'name' => '1kg',
                'is_active' => true
            ],
            [
                'variant_type_id' => 4,
                'name' => '2kg',
                'is_active' => true
            ],
            [
                'variant_type_id' => 4,
                'name' => '3kg',
                'is_active' => true
            ],
            [
                'variant_type_id' => 5,
                'name' => '10x10x10',
                'is_active' => true
            ],
            [
                'variant_type_id' => 5,
                'name' => '20x20x20',
                'is_active' => true
            ],
            [
                'variant_type_id' => 5,
                'name' => '30x30x30',
                'is_active' => true
            ],
            [
                'variant_type_id' => 6,
                'name' => '110V',
                'is_active' => true
            ],
            [
                'variant_type_id' => 6,
                'name' => '220V',
                'is_active' => true
            ],
            [
                'variant_type_id' => 7,
                'name' => '128GB',
                'is_active' => true
            ],
            [
                'variant_type_id' => 7,
                'name' => '256GB',
                'is_active' => true
            ],
            [
                'variant_type_id' => 7,
                'name' => '512GB',
                'is_active' => true
            ],
            [
                'variant_type_id' => 8,
                'name' => '4GB',
                'is_active' => true
            ],
            [
                'variant_type_id' => 8,
                'name' => '8GB',
                'is_active' => true
            ],
            [
                'variant_type_id' => 8,
                'name' => '16GB',
                'is_active' => true
            ],
            [
                'variant_type_id' => 9,
                'name' => '1 Year',
                'is_active' => true
            ],
            [
                'variant_type_id' => 9,
                'name' => '2 Year',
                'is_active' => true
            ],
            [
                'variant_type_id' => 9,
                'name' => '3 Year',
                'is_active' => true
            ],
            [
                'variant_type_id' => 10,
                'name' => '1 Pack',
                'is_active' => true
            ],
            [
                'variant_type_id' => 10,
                'name' => '2 Pack',
                'is_active' => true
            ],
            [
                'variant_type_id' => 10,
                'name' => '3 Pack',
                'is_active' => true
            ],
            [
                'variant_type_id' => 11,
                'name' => '1 Month',
                'is_active' => true
            ],
            [
                'variant_type_id' => 11,
                'name' => '3 Month',
                'is_active' => true
            ],
            [
                'variant_type_id' => 11,
                'name' => '6 Month',
                'is_active' => true
            ]
        ];
        foreach($variantOptions as $variantOption){
            if (!isset($variantOption['value'])) {
                $variantOption['value'] = strtolower($variantOption['name']);
            }
            VariantOption::create($variantOption);
        }
    }
}
