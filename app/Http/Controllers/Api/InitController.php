<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SubCategory;

class InitController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $products = Product::with('images')->get();
        $shops = Shop::all();
        return $this->formatResponse('Init data fetched successfully', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'products' => $products,
            'shops' => $shops,
        ]);
    }
}
