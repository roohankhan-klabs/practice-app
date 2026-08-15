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
        Country::create([
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
        ]);
        Country::create([
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
        ]);
        Country::create([
            'name' => 'Pakistan',
            'code' => 'PK',
            'iso_code' => 'PAK',
            'phone_code' => '+92',
            'phone_number_digits' => 10,
            'country_code' => 'PK',
            'currency' => 'PKR',
            'currency_code' => 'PKR',
            'currency_symbol' => '₨',
            'currency_exchange_rate' => '1',
            'currency_exchange_rate_date' => '2026-01-01',
        ]);
        Country::create([
            'name' => 'Bahrain',
            'code' => 'BH',
            'iso_code' => 'BHR',
            'phone_code' => '+973',
            'phone_number_digits' => 10,
            'country_code' => 'BH',
            'currency' => 'BHD',
            'currency_code' => 'BHD',
            'currency_symbol' => 'د.ب',
            'currency_exchange_rate' => '1',
            'currency_exchange_rate_date' => '2026-01-01',
        ]);
    }
}
