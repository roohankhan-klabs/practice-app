<?php

namespace Database\Seeders;

use App\Models\VariantType;
use Illuminate\Database\Seeder;

class VariantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variantTypes = [
            [
                "key" => "color",
                "name" => "Color",
                "is_active" => true
            ],
            [
                "key" => "size",
                "name" => "Size",
                "is_active" => true
            ],
            [
                "key" => "material",
                "name" => "Material",
                "is_active" => true
            ],
            [
                "key" => "weight",
                "name" => "Weight",
                "is_active" => true
            ],
            [
                "key" => "dimensions",
                "name" => "Dimensions",
                "is_active" => true
            ],
            [
                "key" => "voltage",
                "name" => "Voltage",
                "is_active" => true
            ],
            [
                "key" => "storage",
                "name" => "Storage",
                "is_active" => true
            ],
            [
                "key" => "ram",
                "name" => "RAM",
                "is_active" => true
            ],
            [
                "key" => "warranty",
                "name" => "Warranty",
                "is_active" => true
            ],
            [
                "key" => "pack_size",
                "name" => "Pack Size",
                "is_active" => true
            ],
            [
                "key" => "subscription_period",
                "name" => "Subscription Period",
                "is_active" => true
            ]
        ];
        foreach ($variantTypes as $variantType) {
            VariantType::query()->updateOrCreate(
                ['key' => $variantType['key']],
                $variantType,
            );
        }
    }
}
