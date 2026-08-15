<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            'US' => ['California'],
            'CA' => ['Alberta'],
            'PK' => ['Sindh'],
            'BH' => ['Capital Governorate'],
        ];

        foreach ($states as $countryCode => $names) {
            $country = Country::query()->where('code', $countryCode)->firstOrFail();

            foreach ($names as $name) {
                State::query()->updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $name,
                    ],
                    ['name' => $name],
                );
            }
        }
    }
}
