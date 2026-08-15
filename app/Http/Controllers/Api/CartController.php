<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cartItems = $this->cartService->getUserCart($request);

        return $this->formatResponse('Cart items fetched successfully', $cartItems);
    }

    public function addToCart(Request $request)
    {
        $validated = $this->cartService->validateAddToCart($request);

        $cartItem = $this->cartService->addItemToCart($request, $validated);

        return $this->formatResponse('Item added to cart successfully', $cartItem);
    }

    public function updateCartItem(Request $request, int $id)
    {
        $validated = $this->cartService->validateUpdateCartItem($request);

        $cartItem = $this->cartService->updateCartItem($request, $id, $validated);

        return $this->formatResponse('Cart item updated successfully', $cartItem);
    }

    public function removeCartItem(Request $request, int $id)
    {
        $this->cartService->removeItemFromCart($request, $id);

        return $this->formatResponse('Cart item removed successfully');
    }

    public function clearCart(Request $request)
    {
        $this->cartService->clearCart($request);

        return $this->formatResponse('Cart cleared successfully');
    }
}
