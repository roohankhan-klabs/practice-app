<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class SafepayService
{
    private string $baseUrl;

    private string $apiKey;

    private string $merchantSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('safepay.base_url'),
            '/'
        );

        $this->apiKey = config('safepay.api_key');

        $this->merchantSecret = config(
            'safepay.merchant_secret'
        );
    }

    public function pay(Request $request, Order $order, Payment $payment)
    {
        $amount = (int) round(
            (float) $order->total_amount * 100
        );

        if ($amount <= 0) {
            return response()->json([
                'message' => 'Invalid order amount.',
            ], 422);
        }

        try {
            $newPayment = $payment::create([
                'order_id' => $order->id,
                'payment_method_id' => PaymentMethod::SAFEPAY,
                'amount' => $order->total_amount,
                'currency' => 'PKR',
                'status' => PaymentStatus::PENDING,
            ]);

            $result = $this->createTracker(
                amount: $amount,
                currency: 'PKR',
                metadata: [
                    'order_id' => (string) $order->id,
                    'payment_id' => (string) $newPayment->id,
                ],
            );
            $tracker = data_get(
                $result,
                'data.tracker'
            );

            if (! $tracker) {
                throw new RuntimeException(
                    'Safepay did not return a tracker.'
                );
            }

            $newPayment->update([
                'transaction_id' => $tracker,
                'status' => PaymentStatus::PROCESSING,
                'response' => $result,
            ]);
            $captureContext =
                $this->generateCaptureContext(
                    $tracker
                );

            return response()->json([
                'message' => 'Safepay payment initialized.',
                'payment' => [
                    'id' => $newPayment->id,
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'currency' => 'PKR',
                    'status' => $newPayment->status,
                    'tracker' => $tracker,
                ],

                'capture_context' => data_get(
                    $captureContext,
                    'data'
                ),
            ]);
        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Unable to initialize payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a Safepay payment tracker.
     *
     * Amount must be in minor units.
     *
     * Example:
     * PKR 500 = 50000
     */
    public function createTracker(
        int $amount,
        string $currency,
        array $metadata = []
    ): array {
        $response = Http::timeout(30)
            ->withHeaders([
                'X-SFPY-MERCHANT-SECRET' => $this->merchantSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                $this->baseUrl.'/order/payments/v3/',
                [
                    'merchant_api_key' => $this->apiKey,
                    'intent' => 'CYBERSOURCE',
                    'mode' => 'payment',
                    'entry_mode' => 'flex',
                    'currency' => $currency,
                    'amount' => $amount,
                    'metadata' => $metadata,
                ]
            );

        return $this->handleResponse($response);
    }

    /**
     * Generate capture context.
     *
     * The capture context is used by the client-side
     * Safepay/Cybersource Flex checkout.
     */
    public function generateCaptureContext(
        string $tracker
    ): array {
        $response = Http::timeout(30)
            ->withHeaders([
                'X-SFPY-MERCHANT-SECRET' => $this->merchantSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $tracker,
                [
                    'payload' => [
                        'origin' => config('app.url'),
                    ],
                ]
            );

        return $this->handleResponse($response);
    }

    /**
     * Process the transient token returned by
     * the Cybersource Flex card fields.
     */
    public function processTransientToken(
        string $tracker,
        string $transientToken
    ): array {
        $response = Http::timeout(30)
            ->withHeaders([
                'X-SFPY-MERCHANT-SECRET' => $this->merchantSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $tracker,
                [
                    'payload' => [
                        'payment_method' => [
                            'flex' => [
                                'transient_token_jwt' => $transientToken,
                            ],
                        ],
                    ],
                ]
            );

        return $this->handleResponse($response);
    }

    /**
     * Authorize and capture payment.
     */
    public function authorize(
        string $tracker,
        bool $doCapture = true
    ): array {
        $response = Http::timeout(30)
            ->withHeaders([
                'X-SFPY-MERCHANT-SECRET' => $this->merchantSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $tracker,
                [
                    'payload' => [
                        'authorization' => [
                            'do_capture' => $doCapture,
                        ],
                    ],
                ]
            );

        return $this->handleResponse($response);
    }

    /**
     * Retrieve/process an existing tracker.
     */
    public function getTracker(string $tracker): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'X-SFPY-MERCHANT-SECRET' => $this->merchantSecret,
                'Accept' => 'application/json',
            ])
            ->get(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $tracker
            );

        return $this->handleResponse($response);
    }

    private function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json();
        }

        throw new RuntimeException(
            'Safepay API error: '.
                $response->status().
                ' '.
                $response->body()
        );
    }
}
