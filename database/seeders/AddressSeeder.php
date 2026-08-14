<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'user_id' => 3,
            'address_line_1' => '123 Main St',
            'address_line_2' => 'Apt 1',
            'preffered_contact_number' => '1234567890',
            'postal_code' => '12345',
            'city_id' => 1,
            'state_id' => 1,
            'country_id' => 1,
        ]);
        Address::create([
            'user_id' => 4,
            'address_line_1' => '456 Main St',
            'address_line_2' => 'Apt 2',
            'preffered_contact_number' => '1234567890',
            'postal_code' => '12345',
            'city_id' => 2,
            'state_id' => 2,
            'country_id' => 2,
        ]);
        Address::create([
            'user_id' => 5,
            'address_line_1' => '789 Main St',
            'address_line_2' => 'Apt 3',
            'preffered_contact_number' => '1234567890',
            'postal_code' => '12345',
            'city_id' => 3,
            'state_id' => 3,
            'country_id' => 3,
        ]);
        Address::create([
            'user_id' => 6,
            'address_line_1' => '101 Main St',
            'address_line_2' => 'Apt 4',
            'preffered_contact_number' => '1234567890',
            'postal_code' => '12345',
            'city_id' => 4,
            'state_id' => 4,
            'country_id' => 4,
        ]);
    }
}
