<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use App\Services\SafePayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly SafePayService $safepayService
    ) {}

    public function show(
        Request $request,
        int $paymentId
    ) {
        $payment = Payment::where('id', $paymentId)
            ->with('orders')
            ->first();

        if (
            ! $payment ||
            $payment->orders->isEmpty() ||
            $payment->orders->first()->user_id !== $request->user()->id
        ) {
            return $this->formatError(
                'Payment not found',
                404
            );
        }

        return $this->formatResponse(
            'Payment found.',
            $payment
        );
    }

    public function createCaptureContext(
        Request $request,
        int $paymentId
    ) {
        $payment = Payment::where('id', $paymentId)
            ->with('orders')
            ->first();

        if (
            ! $payment ||
            $payment->orders->isEmpty() ||
            $payment->orders->first()->user_id !== $request->user()->id
        ) {
            return $this->formatError(
                'Payment not found',
                404
            );
        }

        $captureContext =
            $this->safepayService->generateCaptureContext(
                $payment
            );

        return $this->formatResponse(
            'Capture context generated successfully.',
            [
                'payment_id' => $payment->id,
                'tracker' => $payment->tracker,
                'capture_context' => $captureContext,
            ]
        );
    }

    public function handleTransientToken(
        Request $request,
        int $paymentId
    ) {
        $validated = $request->validate([
            'transient_token_jwt' => 'required|string',
        ]);

        $payment = Payment::where('id', $paymentId)
            ->with('orders')
            ->first();

        if (
            ! $payment ||
            $payment->orders->isEmpty() ||
            $payment->orders->first()->user_id !== $request->user()->id
        ) {
            return $this->formatError(
                'Payment not found',
                404
            );
        }

        $response =
            $this->safepayService->processTransientToken(
                $payment,
                $validated['transient_token_jwt']
            );

        if (
            data_get(
                $response,
                'data.tracker.next_actions.CYBERSOURCE.kind'
            ) === 'AUTHORIZATION' &&
            is_string($payment->tracker)
        ) {
            $response = $this->safepayService->authorize(
                $payment->tracker,
                true
            );
        }

        return $this->formatResponse(
            'Successfully processed transient token.',
            $response
        );
    }
}
