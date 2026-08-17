<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Payment;
use App\Services\SafepayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly SafePayService $safepayService) {}

    public function show(Request $request, int $paymentId)
    {
        $payment = Payment::where('id', $paymentId)->first();
        if (! $payment || ! $payment->order || $payment->order->user_id !== $request->user()->id) {
            return $this->formatError('Payment not found', 404);
        }
        return $this->formatResponse('Payment found.', $payment);
    }

    public function showByTracker(Request $request, string $tracker)
    {
        $payment = Payment::where('tracker', $tracker)->first();

        if (! $payment || ! $payment->order || $payment->order->user_id !== $request->user()->id) {
            return $this->formatError('Payment not found', 404);
        }

        $trackerResponse = $this->safepayService->getTracker($tracker);

        return $this->formatResponse('Payment found.', [
            'payment' => $payment,
            'tracker' => data_get($trackerResponse, 'data.tracker'),
            'action' => data_get($trackerResponse, 'data.action'),
        ]);
    }

    public function handleTransientToken(Request $request)
    {
        $response = $this->safepayService->processTransientToken($request->tracker, $request->transient_token);
        return $this->formatResponse('Successfully processed transient token.', $response);
    }
}
