<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    private CartService $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function index(Request $request)
    {
        $wishlist = $request->user()->wishlist()->with('product');

        return $this->formatResponse('Wishlist fetched successfully', $wishlist);
    }
    public function convertToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $cartItem = $this->cartService->addItemToCart($request, $validated);

        return $this->formatResponse('Item added to cart successfully', $cartItem);
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $wishlist = $request->user()->wishlist()->where('product_id', $validated['product_id'])->first();
        if ($wishlist) {
            $wishlist->delete();
            return $this->formatResponse('Removed from wishlist successfully', $wishlist);
        } else {
            $wishlist = $request->user()->wishlist()->create($validated);
            return $this->formatResponse('Added to wishlist successfully', $wishlist);
        }
    }

    public function destroy(Request $request)
    {
        $wishlist = $request->user()->wishlist()
            ->where('product_id', $request->product_id)
            ->first();
        if (!$wishlist) {
            return $this->formatResponse('Wishlist not found', null, 404);
        }
        $wishlist->delete();
        return $this->formatResponse('Removed from wishlist successfully', $wishlist);
    }
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        $wishlist = $request->user()->wishlist()
            ->whereIn('product_id', $validated['product_ids'])
            ->get();
        if ($wishlist->isEmpty()) {
            return $this->formatResponse('Wishlist not found', null, 404);
        }
        $wishlist->delete();
        return $this->formatResponse('Removed all items from wishlist successfully', $wishlist);
    }
}
