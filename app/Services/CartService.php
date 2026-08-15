<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function getUserCart(Request $request)
    {
        return $request->user()->cartItems;
    }

    public function addItemToCart(Request $request, array $validated): CartItem
    {
        $existingCartItem = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($existingCartItem) {
            $existingCartItem->update([
                'quantity' => $existingCartItem->quantity + $validated['quantity'],
            ]);

            return $existingCartItem;
        }

        $product = Product::find($validated['product_id']);

        if ($product->variants()->exists() && !$validated['variant_id']) {
            throw ValidationException::withMessages([
                'variant_id' => 'Variant is required',
            ]);
        }

        if ($validated['variant_id'] && !$product->variants()->where('id', $validated['variant_id'])->exists()) {
            throw ValidationException::withMessages([
                'variant_id' => 'Variant not found',
            ]);
        }

        $cartItem = CartItem::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
            'variant_id' => $validated['variant_id'] ?? null,
            'quantity' => $validated['quantity'],
        ]);

        return $cartItem;
    }

    public function updateCartItem(Request $request, int $id, array $validated): CartItem
    {
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->update($validated);

        return $cartItem;
    }

    public function removeItemFromCart(Request $request, int $id): void
    {
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();
    }

    public function clearCart(Request $request): void
    {
        CartItem::where('user_id', $request->user()->id)->delete();
    }
}

