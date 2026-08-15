<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create([
            'name' => 'New York',
            'state_id' => 1,
            'country_id' => 1,
        ]);
        City::create([
            'name' => 'Toronto',
            'state_id' => 2,
            'country_id' => 2,
        ]);
        City::create([
            'name' => 'Karachi',
            'state_id' => 3,
            'country_id' => 3,
        ]);
        City::create([
            'name' => 'Manama',
            'state_id' => 4,
            'country_id' => 4,
        ]);
    }
}
