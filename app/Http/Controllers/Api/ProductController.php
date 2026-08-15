<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('images', 'variants', 'reviews')->get();
        $products->each(function ($product) {
            $product->variants->each(function ($variant) {
                $variant->setRelation('variantOptions', $variant->variantOptions());
            });
        });
        return $this->formatResponse('Products fetched successfully', $products);
    }

    public function show(Request $request, int $productId)
    {
        $product = Product::with('images', 'variants', 'reviews')->where('id', $productId)->first();
        $product->variants->each(function ($variant) {
            $variant->setRelation('variantOptions', $variant->variantOptions());
        });
        if (!$product) {
            return $this->formatResponse('Product not found', null, 404);
        }
        return $this->formatResponse('Product fetched successfully', $product);
    }

    public function review(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id', function ($attribute, $value, $fail) use ($request) {
                $hasOrdered = Order::whereHas('order_items', function ($query) use ($value) {
                    $query->where('product_id', $value);
                })->where('user_id', $request->user()->id)->exists();
                if (!$hasOrdered) {
                    $fail('You have not ordered this product');
                }
            }],
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        $product = Product::find($validated['product_id']);
        $review = Review::create([
            'user_id' => $request->user()->id,
            'shop_id' => $product->shop_id,
            'product_id' => $validated['product_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);
        return $this->formatResponse('Review added successfully', $review);
    }
}
