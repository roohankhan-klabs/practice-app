<?php

namespace App\Http\Controllers\Api;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $shops = Shop::all();
        return $this->formatResponse('Shops fetched successfully', $shops);
    }
    public function show(Request $request, int $shopId)
    {
        $shop = Shop::findOrFail($shopId);
        return $this->formatResponse('Shop fetched successfully', $shop);
    }
}
