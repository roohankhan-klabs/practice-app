<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SafePayService
{
    private string $baseUrl;

    private string $apiKey;

    private string $merchantSecret;

    private string $checkoutOrigin;

    private string $checkoutSuccessUrl;

    private string $checkoutCancelUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('safepay.base_url'),
            '/'
        );

        $this->apiKey = (string) config(
            'safepay.api_key'
        );

        $this->merchantSecret = (string) config(
            'safepay.merchant_secret'
        );

        $this->checkoutOrigin = $this->normalizeOrigin(
            (string) config(
                'safepay.checkout_origin',
                config('app.url')
            )
        );

        $this->checkoutSuccessUrl = rtrim(
            (string) config(
                'safepay.checkout_success_url'
            ),
            '/'
        );

        $this->checkoutCancelUrl = rtrim(
            (string) config(
                'safepay.checkout_cancel_url'
            ),
            '/'
        );
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  array{subtotal:mixed,shipping_fees:mixed,tax:mixed,discount:mixed,total_amount:mixed}  $bill
     * @param  array<int, int>  $cartItemIds
     * @return array<string, mixed>
     */
    public function initializePayment(
        Request $request,
        Payment $payment,
        Collection $orders,
        array $bill,
        array $cartItemIds
    ): array {
        Log::info('Safepay payment initialization started.', [
            'payment_id' => $payment->id,
            'order_ids' => $orders->pluck('id')->all(),
            'reference' => $orders->first()?->reference,
        ]);

        try {
            $safepayAmount = $this->toMinorAmount(
                $bill['total_amount']
            );

            if ($safepayAmount <= 0) {
                throw new RuntimeException(
                    'Invalid order amount.'
                );
            }

            $trackerResponse = $this->createTracker(
                $request,
                $safepayAmount,
                'PKR'
            );

            $tracker = data_get(
                $trackerResponse,
                'data.tracker.token'
            );

            if (! is_string($tracker) || $tracker === '') {
                throw new RuntimeException(
                    'Safepay tracker token missing.'
                );
            }

            $payment->update([
                'tracker' => $tracker,
                'status' => PaymentStatus::PROCESSING->value,
                'response' => [
                    'tracker' => $trackerResponse,
                    'checkout' => [
                        'cart_item_ids' => $cartItemIds,
                    ],
                ],
            ]);

            Log::info('Safepay tracker created.', [
                'payment_id' => $payment->id,
                'tracker' => $tracker,
            ]);

            return [
                'success' => true,
                'message' => 'Payment initialized successfully.',
                'payment_id' => $payment->id,
                'checkout_url' => route('pay', [
                    'payment_id' => $payment->id,
                ]),
                'reference' => $orders->first()?->reference,
                'tracker' => $tracker,
            ];
        } catch (Throwable $exception) {
            Log::error('Safepay payment initialization failed.', [
                'payment_id' => $payment->id,
                'order_ids' => $orders->pluck('id')->all(),
                'message' => $exception->getMessage(),
            ]);

            $payment->update([
                'status' => PaymentStatus::FAILED->value,
                'response' => array_merge(
                    $payment->response ?? [],
                    [
                        'initialization_error' => [
                            'message' => $exception->getMessage(),
                        ],
                    ]
                ),
            ]);

            return [
                'success' => false,
                'message' => 'Unable to initialize payment.',
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function generateCaptureContext(
        Request $request,
        Payment $payment
    ): array {
        if (! is_string($payment->tracker) || $payment->tracker === '') {
            throw new RuntimeException(
                'Payment tracker is missing.'
            );
        }

        $payload = [];
        $origin = $this->resolveCheckoutOrigin($request);

        if ($origin !== '') {
            $payload = [
                'payload' => [
                    'origin' => $origin,
                ],
            ];
        }

        Log::info('Safepay capture context generation started.', [
            'payment_id' => $payment->id,
            'tracker' => $payment->tracker,
            'origin' => $origin,
        ]);

        $response = $this->baseRequest()
            ->post(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $payment->tracker,
                $payload
            );

        $result = $this->handleResponse($response);

        $payment->update([
            'response' => array_merge(
                $payment->response ?? [],
                [
                    'capture_context' => $result,
                ]
            ),
        ]);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function processTransientToken(
        Payment $payment,
        string $transientToken
    ): array {
        if (! is_string($payment->tracker) || $payment->tracker === '') {
            throw new RuntimeException(
                'Payment tracker is missing.'
            );
        }

        Log::info('Safepay transient token processing started.', [
            'payment_id' => $payment->id,
            'tracker' => $payment->tracker,
        ]);

        $response = $this->baseRequest()
            ->withToken(
                $this->generatePassportToken()
            )
            ->post(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $payment->tracker,
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

        $result = $this->handleResponse($response);

        $payment->update([
            'response' => array_merge(
                $payment->response ?? [],
                [
                    'transient_token' => $result,
                ]
            ),
        ]);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function authorize(
        string $tracker,
        bool $doCapture = true
    ): array {
        $response = $this->baseRequest()
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
     * @return array<string, mixed>
     */
    public function capture(
        string $tracker
    ): array {
        $response = $this->baseRequest()
            ->withToken(
                $this->generatePassportToken()
            )
            ->post(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $tracker,
                []
            );

        return $this->handleResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    public function getTracker(string $tracker): array
    {
        $response = $this->baseRequest()
            ->get(
                $this->baseUrl.
                    '/order/payments/v3/'.
                    $tracker
            );

        return $this->handleResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    public function finalizeSuccessfulCallback(
        string $tracker
    ): array {
        $payment = Payment::where('tracker', $tracker)
            ->with('orders')
            ->first();

        if (! $payment) {
            throw new RuntimeException(
                'Payment not found.'
            );
        }

        if ($payment->status === PaymentStatus::SUCCESS->value) {
            return [
                'successful' => true,
                'payment' => $payment,
                'tracker' => data_get(
                    $payment->response,
                    'verified_tracker'
                ),
                'already_processed' => true,
            ];
        }

        Log::info('Safepay payment verification started.', [
            'payment_id' => $payment->id,
            'tracker' => $tracker,
        ]);

        $trackerResponse = $this->getTracker($tracker);
        $trackerData = (array) data_get(
            $trackerResponse,
            'data.tracker',
            []
        );

        $nextAction = (string) data_get(
            $trackerData,
            'next_actions.CYBERSOURCE.kind',
            ''
        );

        if ($nextAction === 'AUTHORIZATION') {
            $trackerResponse = $this->authorize(
                $tracker,
                true
            );
            $trackerData = (array) data_get(
                $trackerResponse,
                'data.tracker',
                $trackerData
            );
        } elseif ($nextAction === 'CAPTURE') {
            $trackerResponse = $this->capture(
                $tracker
            );
            $trackerData = (array) data_get(
                $trackerResponse,
                'data.tracker',
                $trackerData
            );
        }

        $state = (string) data_get(
            $trackerData,
            'state',
            ''
        );

        if ($state !== 'TRACKER_ENDED') {
            $this->markUnsuccessfulPayment(
                $payment,
                $trackerResponse,
                $state
            );

            return [
                'successful' => false,
                'payment' => $payment->fresh('orders'),
                'tracker' => $trackerData,
                'already_processed' => false,
            ];
        }

        DB::transaction(function () use (
            $payment,
            $trackerResponse
        ) {
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->with('orders')
                ->firstOrFail();

            if ($lockedPayment->status === PaymentStatus::SUCCESS->value) {
                return;
            }

            $lockedPayment->update([
                'status' => PaymentStatus::SUCCESS->value,
                'paid_at' => Carbon::now(),
                'response' => array_merge(
                    $lockedPayment->response ?? [],
                    [
                        'verified_tracker' => $trackerResponse,
                    ]
                ),
            ]);

            $lockedPayment->orders()
                ->update([
                    'status' => Order::PENDING,
                ]);

            $this->clearCheckedOutCartItems(
                $lockedPayment
            );

            Log::info('Safepay payment state transitioned to success.', [
                'payment_id' => $lockedPayment->id,
                'order_ids' => $lockedPayment->orders->pluck('id')->all(),
            ]);
        });

        return [
            'successful' => true,
            'payment' => $payment->fresh('orders'),
            'tracker' => $trackerData,
            'already_processed' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function finalizeFailedCallback(
        string $tracker
    ): array {
        $payment = Payment::where('tracker', $tracker)
            ->with('orders')
            ->first();

        if (! $payment) {
            throw new RuntimeException(
                'Payment not found.'
            );
        }

        if ($payment->status === PaymentStatus::SUCCESS->value) {
            return [
                'successful' => true,
                'payment' => $payment,
                'tracker' => data_get(
                    $payment->response,
                    'verified_tracker.data.tracker'
                ),
            ];
        }

        $trackerResponse = $this->getTracker($tracker);
        $trackerData = (array) data_get(
            $trackerResponse,
            'data.tracker',
            []
        );

        $this->markUnsuccessfulPayment(
            $payment,
            $trackerResponse,
            (string) data_get($trackerData, 'state', '')
        );

        return [
            'successful' => false,
            'payment' => $payment->fresh('orders'),
            'tracker' => $trackerData,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function createTracker(
        Request $request,
        int $amount,
        string $currency
    ): array {
        $pendingRequest = $this->baseRequest()
            ->withHeaders(array_filter([
                'X-SFPY-IP-ADDRESS' => $request->ip(),
                'X-SFPY-USER-AGENT' => $request->userAgent(),
            ]));

        $response = $pendingRequest->post(
            $this->baseUrl.'/order/payments/v3/',
            [
                'merchant_api_key' => $this->apiKey,
                'intent' => 'CYBERSOURCE',
                'mode' => 'payment',
                'entry_mode' => 'flex',
                'currency' => $currency,
                'amount' => $amount,
            ]
        );

        return $this->handleResponse($response);
    }

    private function baseRequest()
    {
        return Http::timeout(30)
            ->connectTimeout(10)
            ->withHeaders([
                'X-SFPY-MERCHANT-SECRET' => $this->merchantSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function handleResponse(
        Response $response
    ): array {
        if ($response->successful()) {
            return (array) $response->json();
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
        $response = $this->baseRequest()
            ->post(
                $this->baseUrl.
                    '/client/passport/v1/token'
            );

        $result = $this->handleResponse($response);
        $token = data_get($result, 'data');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException(
                'Safepay time-based token missing.'
            );
        }

        return $token;
    }

    private function markUnsuccessfulPayment(
        Payment $payment,
        array $trackerResponse,
        string $state
    ): void {
        $status = in_array($state, [
            'TRACKER_CANCELLED',
            'TRACKER_EXPIRED',
        ], true)
            ? PaymentStatus::CANCELLED
            : PaymentStatus::FAILED;

        $payment->update([
            'status' => $status->value,
            'response' => array_merge(
                $payment->response ?? [],
                [
                    'verified_tracker' => $trackerResponse,
                ]
            ),
        ]);

        Log::info('Safepay payment marked unsuccessful.', [
            'payment_id' => $payment->id,
            'tracker' => $payment->tracker,
            'state' => $state,
            'status' => $status->value,
        ]);
    }

    private function clearCheckedOutCartItems(
        Payment $payment
    ): void {
        $cartItemIds = data_get(
            $payment->response,
            'checkout.cart_item_ids',
            []
        );

        if (! is_array($cartItemIds) || $cartItemIds === []) {
            return;
        }

        $userId = $payment->orders->first()?->user_id;

        if (! $userId) {
            return;
        }

        CartItem::whereIn('id', $cartItemIds)
            ->whereHas('cart', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->delete();
    }

    private function toMinorAmount(
        mixed $amount
    ): int {
        return (int) round((float) $amount * 100);
    }

    private function normalizeOrigin(
        string $url
    ): string {
        $parts = parse_url($url);

        if (
            ! is_array($parts) ||
            ! isset($parts['scheme'], $parts['host'])
        ) {
            return '';
        }

        $origin =
            $parts['scheme'].
            '://'.
            $parts['host'];

        if (isset($parts['port'])) {
            $origin .= ':'.$parts['port'];
        }

        return $origin;
    }

    private function resolveCheckoutOrigin(
        Request $request
    ): string {
        $originHeader = $request->headers->get('Origin');

        if (is_string($originHeader) && $originHeader !== '') {
            return $this->normalizeOrigin($originHeader);
        }

        $refererHeader = $request->headers->get('Referer');

        if (is_string($refererHeader) && $refererHeader !== '') {
            return $this->normalizeOrigin($refererHeader);
        }

        return $this->checkoutOrigin;
    }
}
