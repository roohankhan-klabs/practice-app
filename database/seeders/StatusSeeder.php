<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create([
            'name' => 'Active',
        ]);
        Status::create([
            'name' => 'Inactive',
        ]);
        Status::create([
            'name' => 'Pending',
        ]);
        Status::create([
            'name' => 'Deleted',
        ]);
        Status::create([
            'name' => 'Blocked',
        ]);
        Status::create([
            'name' => 'Suspended',
        ]);
        Status::create([
            'name' => 'Expired',
        ]);
    }
}
