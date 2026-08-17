<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class InitController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $products = Product::with('images', 'variants', 'reviews')->get();
        $shops = Shop::all();

        return $this->formatResponse('Init data fetched successfully', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'products' => $products,
            'shops' => $shops,
        ]);
    }
    public function cartItemsCount(Request $request)
    {
        $cartItemsCount = CartItem::where('cart_id', $request->user()->cart->id)->count();
        return $this->formatResponse('Cart items count fetched successfully', [
            'cart_items_count' => $cartItemsCount
        ]);
    }
}
