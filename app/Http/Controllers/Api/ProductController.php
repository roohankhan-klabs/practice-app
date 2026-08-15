<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('images', 'variants', 'reviews')->get();
        return $this->formatResponse('Products fetched successfully', $products);
    }

    public function show(Request $request, $id)
    {
        $product = Product::with('images', 'variants', 'reviews')->where('id', $id)->first();
        if (!$product) {
            return $this->formatResponse('Product not found', null, 404);
        }
        return $this->formatResponse('Product fetched successfully', $product);
    }

    public function review(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);
        return $this->formatResponse('Review added successfully', $review);
    }
}
