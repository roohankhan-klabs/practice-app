<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * @return Collection<int, CartItem>
     */
    public function getUserCart(Request $request): Collection
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();
        if (! $cart) {
            return new Collection;
        }

        return CartItem::where('cart_id', $cart->id)->with(['product', 'variant'])->get();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function addItemToCart(Request $request, array $validated): CartItem
    {
        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $existingCartItem = CartItem::where('cart_id', $cart->id)
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

        if (! $product instanceof Product) {
            throw ValidationException::withMessages([
                'product_id' => 'Product not found',
            ]);
        }

        if ($product->variants()->exists() && ! ($validated['variant_id'] ?? null)) {
            throw ValidationException::withMessages([
                'variant_id' => 'Variant is required',
            ]);
        }

        if (($validated['variant_id'] ?? null) && ! $product->variants()->where('id', $validated['variant_id'])->exists()) {
            throw ValidationException::withMessages([
                'variant_id' => 'Variant not found',
            ]);
        }

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $validated['product_id'],
            'variant_id' => $validated['variant_id'] ?? null,
            'quantity' => $validated['quantity'],
        ]);

        return $cartItem;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function updateCartItem(Request $request, int $id, array $validated): CartItem
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if (! $cart) {
            throw (new ModelNotFoundException)->setModel(CartItem::class);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->update($validated);

        return $cartItem;
    }

    public function removeItemFromCart(Request $request, int $id): void
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if (! $cart) {
            throw (new ModelNotFoundException)->setModel(CartItem::class);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();
    }

    public function clearCart(Request $request): void
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
        }
    }

    public function getUserCartItems(Request $request, array $cartItemIds): Collection
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();
        if (! $cart) {
            return new Collection;
        }

        return CartItem::where('cart_id', $cart->id)->whereIn('id', $cartItemIds)->with(['product.images', 'variant'])->get();
    }
}
