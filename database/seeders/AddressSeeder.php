<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addresses = [
            ['email' => 'customer1@gmail.com', 'country_code' => 'US', 'state' => 'California', 'city' => 'Los Angeles', 'line_1' => '101 Market Street', 'line_2' => 'Suite 10', 'postal_code' => '90001'],
            ['email' => 'customer2@gmail.com', 'country_code' => 'CA', 'state' => 'Alberta', 'city' => 'Calgary', 'line_1' => '202 River Road', 'line_2' => 'Unit 6', 'postal_code' => 'T2P0A1'],
            ['email' => 'customer3@gmail.com', 'country_code' => 'PK', 'state' => 'Sindh', 'city' => 'Karachi', 'line_1' => '303 Clifton Block 5', 'line_2' => null, 'postal_code' => '75600'],
            ['email' => 'customer4@gmail.com', 'country_code' => 'BH', 'state' => 'Capital Governorate', 'city' => 'Manama', 'line_1' => '404 Seef District', 'line_2' => 'Office 8', 'postal_code' => '428'],
            ['email' => 'vendor1@gmail.com', 'country_code' => 'PK', 'state' => 'Sindh', 'city' => 'Karachi', 'line_1' => '12 Tech Street', 'line_2' => 'Floor 2', 'postal_code' => '75500'],
            ['email' => 'vendor2@gmail.com', 'country_code' => 'BH', 'state' => 'Capital Governorate', 'city' => 'Manama', 'line_1' => '88 Commerce Avenue', 'line_2' => null, 'postal_code' => '322'],
        ];

        foreach ($addresses as $address) {
            $user = User::query()->where('email', $address['email'])->firstOrFail();
            $country = Country::query()->where('code', $address['country_code'])->firstOrFail();
            $state = State::query()
                ->where('country_id', $country->id)
                ->where('name', $address['state'])
                ->firstOrFail();
            $city = City::query()
                ->where('country_id', $country->id)
                ->where('state_id', $state->id)
                ->where('name', $address['city'])
                ->firstOrFail();

            Address::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'shop_id' => null,
                    'address_line_1' => $address['line_1'],
                ],
                [
                    'address_line_2' => $address['line_2'],
                    'preffered_contact_number' => $user->phone,
                    'postal_code' => $address['postal_code'],
                    'city_id' => $city->id,
                    'state_id' => $state->id,
                    'country_id' => $country->id,
                    'is_default' => true,
                ],
            );
        }
    }
}
