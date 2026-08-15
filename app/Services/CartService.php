<?php

namespace App\Services;

use Illuminate\Http\Request;

class CartService
{
    public function getUserCart(Request $request)
    {
        return $request->user()->cartItems;
    }

    public function validateAddToCart(Request $request)
    {
        return $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
    }

    public function addItemToCart(Request $request, array $validated): \App\Models\CartItem
    {
        $cartItem = \App\Models\CartItem::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
        ]);

        return $cartItem;
    }

    public function validateUpdateCartItem(Request $request)
    {
        return $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
    }

    public function updateCartItem(Request $request, int $id, array $validated): \App\Models\CartItem
    {
        $cartItem = \App\Models\CartItem::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$cartItem) {
            throw new \Exception('Cart item not found');
        }

        $cartItem->update($validated);

        return $cartItem;
    }

    public function removeItemFromCart(Request $request, int $id): void
    {
        $cartItem = \App\Models\CartItem::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$cartItem) {
            throw new \Exception('Cart item not found');
        }

        $cartItem->delete();
    }

    public function clearCart(Request $request): void
    {
        \App\Models\CartItem::where('user_id', $request->user()->id)->delete();
    }
}
