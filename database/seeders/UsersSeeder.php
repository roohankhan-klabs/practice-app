<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'status' => 'active',
            'role_id' => 1,
        ]);
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'status' => 'active',
            'role_id' => 2,
        ]);
        User::create([
            'name' => 'Customer 1',
            'email' => 'customer1@gmail.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'status' => 'active',
            'role_id' => 3,
        ]);
        User::create([
            'name' => 'Customer 2',
            'email' => 'customer2@gmail.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'status' => 'active',
            'role_id' => 3,
        ]);
        User::create([
            'name' => 'Customer 3',
            'email' => 'customer3@gmail.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'status' => 'active',
            'role_id' => 3,
        ]);
        User::create([
            'name' => 'Customer 4',
            'email' => 'customer4@gmail.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'status' => 'active',
            'role_id' => 3,
        ]);
    }
}
