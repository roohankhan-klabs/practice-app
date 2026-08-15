<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('subcategories.products')->get();

        return $this->formatResponse('Categories fetched successfully', $categories);
    }

    public function show(Request $request, $id)
    {
        $category = Category::where('id', $id)->with('subcategories.products')->first();

        return $this->formatResponse('Category fetched successfully', $category);
    }
}
