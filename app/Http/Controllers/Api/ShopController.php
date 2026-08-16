<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $shops = Shop::with('products')->get();

        return $this->formatResponse('Shops fetched successfully', $shops);
    }

    public function show(Request $request, int $shopId)
    {
        $shop = Shop::with('products')->findOrFail($shopId);

        return $this->formatResponse('Shop fetched successfully', $shop);
    }
}
