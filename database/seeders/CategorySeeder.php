<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronics products', 'image' => 'electronics.jpg', 'sort_order' => 1, 'status' => 'active'],
            ['name' => 'Books', 'description' => 'Books products', 'image' => 'books.jpg', 'sort_order' => 2, 'status' => 'active'],
            ['name' => 'Men', 'description' => 'Electronics products', 'image' => 'electronics.jpg', 'sort_order' => 1, 'status' => 'active'],
            ['name' => 'Women', 'description' => 'Books products', 'image' => 'books.jpg', 'sort_order' => 2, 'status' => 'active'],
            ['name' => 'Kids', 'description' => 'Electronics products', 'image' => 'electronics.jpg', 'sort_order' => 1, 'status' => 'active'],
            ['name' => 'Furniture', 'description' => 'Books products', 'image' => 'books.jpg', 'sort_order' => 2, 'status' => 'active'],
            ['name' => 'Beauty', 'description' => 'Electronics products', 'image' => 'electronics.jpg', 'sort_order' => 1, 'status' => 'active'],
            ['name' => 'Travel', 'description' => 'Books products', 'image' => 'books.jpg', 'sort_order' => 2, 'status' => 'active'],
            ['name' => 'Health', 'description' => 'Electronics products', 'image' => 'electronics.jpg', 'sort_order' => 1, 'status' => 'active'],
            ['name' => 'Sports', 'description' => 'Books products', 'image' => 'books.jpg', 'sort_order' => 2, 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['name' => $category['name']],
                $category,
            );
        }
    }
}
