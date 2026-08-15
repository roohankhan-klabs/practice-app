<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['country_code' => 'US', 'state' => 'California', 'name' => 'Los Angeles'],
            ['country_code' => 'CA', 'state' => 'Alberta', 'name' => 'Calgary'],
            ['country_code' => 'PK', 'state' => 'Sindh', 'name' => 'Karachi'],
            ['country_code' => 'BH', 'state' => 'Capital Governorate', 'name' => 'Manama'],
        ];

        foreach ($cities as $city) {
            $country = Country::query()->where('code', $city['country_code'])->firstOrFail();
            $state = State::query()
                ->where('country_id', $country->id)
                ->where('name', $city['state'])
                ->firstOrFail();

            City::query()->updateOrCreate(
                [
                    'state_id' => $state->id,
                    'country_id' => $country->id,
                    'name' => $city['name'],
                ],
                ['name' => $city['name']],
            );
        }
    }
}
