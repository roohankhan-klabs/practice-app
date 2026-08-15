<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = $request->user()->wishlist()->with('product');

        return $this->formatResponse('Wishlist fetched successfully', $wishlist);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = $request->user()->wishlist()->create($validated);

        return $this->formatResponse('Wishlist created successfully', $wishlist);
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
