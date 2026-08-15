<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'super@admin.com', 'phone' => '1000000001', 'role_id' => Role::SUPER_ADMIN],
            ['name' => 'Admin', 'email' => 'admin@admin.com', 'phone' => '1000000002', 'role_id' => Role::ADMIN],
            ['name' => 'Platform Staff', 'email' => 'staff@admin.com', 'phone' => '1000000003', 'role_id' => Role::STAFF],
            ['name' => 'Customer One', 'email' => 'customer1@gmail.com', 'phone' => '1000000101', 'role_id' => Role::CUSTOMER],
            ['name' => 'Customer Two', 'email' => 'customer2@gmail.com', 'phone' => '1000000102', 'role_id' => Role::CUSTOMER],
            ['name' => 'Customer Three', 'email' => 'customer3@gmail.com', 'phone' => '1000000103', 'role_id' => Role::CUSTOMER],
            ['name' => 'Customer Four', 'email' => 'customer4@gmail.com', 'phone' => '1000000104', 'role_id' => Role::CUSTOMER],
            ['name' => 'Vendor Alpha', 'email' => 'vendor1@gmail.com', 'phone' => '1000000201', 'role_id' => Role::VENDOR],
            ['name' => 'Vendor Beta', 'email' => 'vendor2@gmail.com', 'phone' => '1000000202', 'role_id' => Role::VENDOR],
        ];

        foreach ($users as $attributes) {
            $user = User::query()->where('email', $attributes['email'])->first();

            if ($user) {
                $user->forceFill([
                    'name' => $attributes['name'],
                    'phone' => $attributes['phone'],
                    'role_id' => $attributes['role_id'],
                    'status' => 'active',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])->save();

                continue;
            }

            $state = User::factory();

            if ($attributes['role_id'] === Role::SUPER_ADMIN) {
                $state = $state->superAdmin();
            } elseif ($attributes['role_id'] === Role::ADMIN) {
                $state = $state->admin();
            } elseif ($attributes['role_id'] === Role::STAFF) {
                $state = $state->staff();
            } elseif ($attributes['role_id'] === Role::VENDOR) {
                $state = $state->vendor();
            } else {
                $state = $state->customer();
            }

            $state->create($attributes);
        }
    }
}
