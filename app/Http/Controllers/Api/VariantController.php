<?php

namespace App\Http\Controllers\Api;

use App\Models\Variant;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class VariantController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $variants = Variant::where('product_id', $validated['product_id'])->get();
        return $this->formatResponse('Variants fetched successfully', $variants);
    }
}
