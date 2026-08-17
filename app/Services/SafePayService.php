<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentOrder;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class SafePayService
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

    public function pay(Request $request, Order $order)
    {
        try {
            $newPayment = Payment::create([
                'payment_method_id' => PaymentMethod::SAFEPAY,
                'amount' => $order->total_amount,
                'currency' => 'PKR',
                'status' => PaymentStatus::PENDING,
            ]);
            PaymentOrder::create([
                'payment_id' => $newPayment->id,
                'order_id' => $order->id,
            ]);

            $safepayAmount = (int) round(
                (float) $order->total_amount * 100
            );

            if ($safepayAmount <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid order amount.',
                ];
            }
            $result = $this->createTracker(
                amount: $safepayAmount,
                currency: 'PKR',
                metadata: [
                    'order_id' => (string) $order->id,
                ],
            );
            $tracker = data_get(
                $result,
                'data.tracker.token'
            );
            if (! $tracker) {
                throw new RuntimeException(
                    'Safepay tracker token/client missing.'
                );
            }
            $captureContext =
                $this->generateCaptureContext(
                    $tracker
                );

            $newPayment->update([
                'tracker' => $tracker,
                'status' => PaymentStatus::PROCESSING,
                'response' => [
                    'tracker' => $result,
                    'capture_context' => $captureContext,
                ],
            ]);

            return [
                'success' => true,
                'message' => 'Safepay payment initialized.',
                'payment' => [
                    'id' => $newPayment->id,
                    'order_id' => $order->id,
                    'amount' => $safepayAmount / 100,
                    'currency' => 'PKR',
                    'status' => $newPayment->status,
                    'tracker' => $tracker,
                ],
                'capture_context' => data_get(
                    $captureContext,
                    'data'
                ),
            ];
        } catch (Throwable $e) {

            report($e);

            return [
                'success' => false,
                'message' => 'Unable to initialize payment.',
                'error' => $e->getMessage(),
            ];
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
            ->withBody('{}', 'application/json')
            ->post(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $tracker
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
