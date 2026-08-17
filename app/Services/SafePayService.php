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
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SafePayService
{
    private const SANDBOX_CHECKOUT_URL =
        'https://sandbox.api.getsafepay.com/checkout/pay';

    private const PRODUCTION_CHECKOUT_URL =
        'https://getsafepay.com/checkout/pay';

    private string $baseUrl;

    private string $apiKey;

    private string $merchantSecret;

    private string $checkoutSuccessUrl;

    private string $checkoutCancelUrl;

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

        $this->checkoutSuccessUrl = rtrim(
            (string) config('safepay.checkout_success_url'),
            '/'
        );

        $this->checkoutCancelUrl = rtrim(
            (string) config('safepay.checkout_cancel_url'),
            '/'
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
            $checkoutUrl = $this->buildCheckoutUrl(
                tracker: $tracker,
                order: $order
            );

            $newPayment->update([
                'tracker' => $tracker,
                'status' => PaymentStatus::PROCESSING,
                'response' => [
                    'tracker' => $result,
                    'checkout_url' => $checkoutUrl,
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
                'checkout_url' => $checkoutUrl,
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
                    'entry_mode' => 'raw',
                    'currency' => $currency,
                    'amount' => $amount,
                    'metadata' => $metadata,
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

    private function generatePassportToken(): string
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'X-SFPY-MERCHANT-SECRET' => $this->merchantSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                $this->baseUrl.
                    '/client/passport/v1/token'
            );

        $result = $this->handleResponse($response);

        $token = data_get($result, 'data');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException(
                'Safepay passport token missing.'
            );
        }

        return $token;
    }

    private function buildCheckoutUrl(
        string $tracker,
        Order $order
    ): string {
        $isSandbox = str_contains(
            $this->baseUrl,
            'sandbox'
        );

        $checkoutBaseUrl = $isSandbox
            ? self::SANDBOX_CHECKOUT_URL
            : self::PRODUCTION_CHECKOUT_URL;

        if (
            ! str_starts_with($this->checkoutSuccessUrl, 'https://') ||
            ! str_starts_with($this->checkoutCancelUrl, 'https://')
        ) {
            Log::warning('Safepay checkout is using non-HTTPS return URLs.', [
                'success_url' => $this->checkoutSuccessUrl,
                'cancel_url' => $this->checkoutCancelUrl,
            ]);
        }

        $query = http_build_query([
            'env' => $isSandbox ? 'sandbox' : 'production',
            'tracker' => $tracker,
            'source' => 'hosted',
            'redirect_url' => $this->checkoutSuccessUrl,
            'cancel_url' => $this->checkoutCancelUrl,
        ]);

        $checkoutUrl = $checkoutBaseUrl.'?'.$query;

        Log::info('Safepay hosted checkout URL generated.', [
            'order_id' => $order->id,
            'order_reference' => $order->reference,
            'tracker' => $tracker,
            'checkout_url' => $checkoutUrl,
        ]);

        return $checkoutUrl;
    }
}
