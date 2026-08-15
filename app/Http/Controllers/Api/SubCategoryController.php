<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subCategories = SubCategory::where('is_active', 1)->with('products')->get();
        return $this->formatResponse('SubCategories fetched successfully', $subCategories);
    }

    public function show(Request $request, $id)
    {
        $subCategory = SubCategory::where('is_active', 1)->where('id', $id)->with('products')->first();
        return $this->formatResponse('SubCategory fetched successfully', $subCategory);
    }
}
