<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Super Admin',
            'Admin',
            'Customer',
            'Vendor',
            'Staff',
        ] as $name) {
            Role::query()->updateOrCreate(
                ['name' => $name],
                ['name' => $name],
            );
        }
    }
}
