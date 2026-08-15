<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'phone' => '1234567890',
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '1234567890',
        ]);

        $customers = [
            ['name' => 'Customer 1', 'email' => 'customer1@gmail.com'],
            ['name' => 'Customer 2', 'email' => 'customer2@gmail.com'],
            ['name' => 'Customer 3', 'email' => 'customer3@gmail.com'],
            ['name' => 'Customer 4', 'email' => 'customer4@gmail.com'],
        ];

        foreach ($customers as $customer) {
            User::factory()->customer()->create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => '1234567890',
            ]);
        }
    }
}
