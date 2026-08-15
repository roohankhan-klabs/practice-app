<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        State::create([
            'name' => 'California',
            'country_id' => 1,
        ]);
        State::create([
            'name' => 'Alberta',
            'country_id' => 2,
        ]);
        State::create([
            'name' => 'Sindh',
            'country_id' => 3,
        ]);
        State::create([
            'name' => 'Capital Governorate',
            'country_id' => 4,
        ]);
    }
}
