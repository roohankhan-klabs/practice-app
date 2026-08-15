<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'United States',
                'code' => 'US',
                'iso_code' => 'USA',
                'phone_code' => '+1',
                'phone_number_digits' => 10,
                'country_code' => 'US',
                'currency' => 'USD',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'currency_exchange_rate' => '1',
                'currency_exchange_rate_date' => '2026-01-01',
            ],
            [
                'name' => 'Canada',
                'code' => 'CA',
                'iso_code' => 'CAN',
                'phone_code' => '+1',
                'phone_number_digits' => 10,
                'country_code' => 'CA',
                'currency' => 'CAD',
                'currency_code' => 'CAD',
                'currency_symbol' => '$',
                'currency_exchange_rate' => '1',
                'currency_exchange_rate_date' => '2026-01-01',
            ],
            [
                'name' => 'Pakistan',
                'code' => 'PK',
                'iso_code' => 'PAK',
                'phone_code' => '+92',
                'phone_number_digits' => 10,
                'country_code' => 'PK',
                'currency' => 'PKR',
                'currency_code' => 'PKR',
                'currency_symbol' => 'Rs',
                'currency_exchange_rate' => '1',
                'currency_exchange_rate_date' => '2026-01-01',
            ],
            [
                'name' => 'Bahrain',
                'code' => 'BH',
                'iso_code' => 'BHR',
                'phone_code' => '+973',
                'phone_number_digits' => 8,
                'country_code' => 'BH',
                'currency' => 'BHD',
                'currency_code' => 'BHD',
                'currency_symbol' => 'BD',
                'currency_exchange_rate' => '1',
                'currency_exchange_rate_date' => '2026-01-01',
            ],
        ];

        foreach ($countries as $country) {
            Country::query()->updateOrCreate(
                ['code' => $country['code']],
                $country,
            );
        }
    }
}
