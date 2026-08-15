<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->secondaryAddress(),
            'preffered_contact_number' => fake()->phoneNumber(),
            'postal_code' => fake()->postcode(),
            'city_id' => City::inRandomOrder()->first()?->id ?? 1,
            'state_id' => State::inRandomOrder()->first()?->id ?? 1,
            'country_id' => Country::inRandomOrder()->first()?->id ?? 1,
            'is_default' => false,
        ];
    }
}
