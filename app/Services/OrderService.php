<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderService
{
    public function placeOrder(Request $request, array $validated): Order
    {
        foreach ($validated['cart_items'] as $item) {
        }
        return $order;
    }
}
