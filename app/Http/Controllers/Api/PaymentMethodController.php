<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $paymentMethods = PaymentMethod::where('user_id', $request->user()->id)->where('is_active', true)->get();
        return $this->formatResponse('Payment methods fetched successfully', $paymentMethods);
    }
}
