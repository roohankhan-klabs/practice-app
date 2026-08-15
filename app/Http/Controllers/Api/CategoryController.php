<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', 1)->with('subcategories')->get();
        return $this->formatResponse('Categories fetched successfully', $categories);
    }

    public function show(Request $request, $id)
    {
        $category = Category::where('is_active', 1)->where('id', $id)->with('subcategories')->first();
        return $this->formatResponse('Category fetched successfully', $category);
    }
}
