<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // get cart
    public function index(Request $request)
    {
        $cartItems = $this->cartService->getUserCart($request);

        return $this->formatResponse('Cart items fetched successfully', $cartItems);
    }

    // add to cart
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $this->cartService->addItemToCart($request, $validated);

        return $this->formatResponse('Item added to cart successfully', $cartItem);
    }

    // update cart item
    public function update(Request $request, int $cartItemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $this->cartService->updateCartItem($request, $cartItemId, $validated);

        return $this->formatResponse('Cart item updated successfully', $cartItem);
    }

    // remove cart item
    public function destroy(Request $request, int $cartItemId)
    {
        $this->cartService->removeItemFromCart($request, $cartItemId);

        return $this->formatResponse('Cart item removed successfully');
    }

    // clear cart
    public function clearCart(Request $request)
    {
        $this->cartService->clearCart($request);

        return $this->formatResponse('Cart cleared successfully');
    }
}
