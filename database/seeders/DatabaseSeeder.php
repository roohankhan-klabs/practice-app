<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            RoleSeeder::class,
            UsersSeeder::class,
            AddressSeeder::class,
            PaymentMethodSeeder::class,
            VariantTypeSeeder::class,
            VariantOptionSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            DemoShopSeeder::class,
            DemoProductSeeder::class,
            DemoDeviceSeeder::class,
            DemoCartSeeder::class,
            // DemoOrderSeeder::class,
        ]);
    }
}
