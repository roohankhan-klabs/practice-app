<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return $this->formatResponse('Payment methods fetched successfully', $paymentMethods);
    }
}
