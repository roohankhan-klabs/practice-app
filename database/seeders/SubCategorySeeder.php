<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            ['category_id' => 1, 'name' => 'Mobiles', 'description' => 'SubCategory 1 products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 1, 'name' => 'Laptops', 'description' => 'SubCategory 2 products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 1, 'name' => 'Tablets', 'description' => 'SubCategory 3 products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 1, 'name' => 'Washing Machines', 'description' => 'SubCategory 4 products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 2, 'name' => 'Literature', 'description' => 'SubCategory 5 products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 2, 'name' => 'Poetry', 'description' => 'SubCategory 6 products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 2, 'name' => 'Biographies', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 2, 'name' => 'Comics', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 3, 'name' => 'Suits', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 3, 'name' => 'Shirts', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 3, 'name' => 'Pants', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 3, 'name' => 'Shoes', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 4, 'name' => 'Dresses', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 4, 'name' => 'Bags', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 5, 'name' => 'Toys', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 5, 'name' => 'Strollers', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 6, 'name' => 'Sofa', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 6, 'name' => 'Mattress', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 7, 'name' => 'Perfumes', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 7, 'name' => 'Skin Care', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 8, 'name' => 'Outdoor', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 8, 'name' => 'Waterproof', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 9, 'name' => 'Vitamins', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 9, 'name' => 'Supplements', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
            ['category_id' => 10, 'name' => 'Yoga', 'description' => 'Electronics products', 'sort_order' => 1, 'status' => 'active'],
            ['category_id' => 10, 'name' => 'Gym Equipment', 'description' => 'Books products', 'sort_order' => 2, 'status' => 'active'],
        ];

        foreach ($subcategories as $subcategory) {
            $category = Category::query()->findOrFail($subcategory['category_id']);

            SubCategory::query()->updateOrCreate(
                [
                    'category_id' => $category->id,
                    'name' => $subcategory['name'],
                ],
                $subcategory,
            );
        }
    }
}
