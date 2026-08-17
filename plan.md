# Task: Fix and Complete Safepay Hosted Checkout Integration

I am integrating Safepay Sandbox into an existing Laravel application.

The desired payment flow is:

```text
User checkout
    ↓
Create all required shop orders
    ↓
Calculate ONE total for the entire checkout
    ↓
Create ONE Payment record
    ↓
Create ONE Safepay tracker
    ↓
Redirect user to Safepay hosted checkout
    ↓
User completes payment on Safepay
    ↓
Safepay redirects back to Laravel
    ↓
Laravel verifies the Safepay payment server-to-server
    ↓
If verified:
    Payment = PAID
    All associated orders = PAID/appropriate paid state
    ↓
Return/display success
```

If payment fails/cancels:

```text
Safepay
    ↓
Laravel failed route
    ↓
Do NOT mark the payment as paid
    ↓
Display failure
```

## Current problem

When Laravel redirects the user to Safepay Sandbox, Safepay redirects to:

```text
https://sandbox.api.getsafepay.com/checkout/external/error?error=Required%20environment%20is%20missing.%20Please%20close%20this%20window%20and%20try%20again
```

Investigate the existing Safepay integration and determine the correct cause rather than blindly changing parameters.

## Important instruction

Before modifying code:

1. Inspect the existing Laravel project structure.
2. Inspect the current checkout/order/payment architecture.
3. Inspect the existing Safepay service.
4. Inspect `config/safepay.php`.
5. Inspect `.env.example` and relevant environment configuration.
6. Inspect migrations/models for:

   * `orders`
   * `payments`
   * `payment_orders`
   * `payment_methods`
7. Inspect `OrderService`.
8. Inspect payment-related enums.
9. Inspect API routes and web routes.
10. Search the project for all usages of:

    * `SafePayService`
    * `PaymentOrder`
    * `PaymentStatus`
    * `PaymentMethod::SAFEPAY`
    * `checkout_success_url`
    * `checkout_cancel_url`
    * `SAFEPAY_`

Do not rewrite unrelated application code.

---

# Existing Safepay implementation

The current service has approximately this structure:

```php
class SafePayService
{
    private const SANDBOX_CHECKOUT_URL =
        'https://sandbox.api.getsafepay.com/checkout/pay';

    private const PRODUCTION_CHECKOUT_URL =
        'https://getsafepay.com/checkout/pay';

    // ...

    public function pay(Request $request, Order $order)
    {
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

        // ...

        $checkoutUrl = $this->buildCheckoutUrl(
            tracker: $tracker,
            order: $order
        );

        // ...
    }
}
```

The tracker is currently created through:

```php
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
```

The current checkout URL is manually constructed approximately as:

```php
$isSandbox = str_contains(
    $this->baseUrl,
    'sandbox'
);

$checkoutBaseUrl = $isSandbox
    ? self::SANDBOX_CHECKOUT_URL
    : self::PRODUCTION_CHECKOUT_URL;

$query = http_build_query([
    'env' => $isSandbox ? 'sandbox' : 'production',
    'tracker' => $tracker,
    'source' => 'hosted',
    'redirect_url' => $this->checkoutSuccessUrl,
    'cancel_url' => $this->checkoutCancelUrl,
]);

$checkoutUrl = $checkoutBaseUrl.'?'.$query;
```

Determine whether this is actually the correct Safepay Sandbox hosted-checkout URL/parameter format. If the current implementation is outdated or incorrect, replace it with the correct approach based on the current Safepay API/documentation.

Do not assume that `env=sandbox` is sufficient just because the URL contains `sandbox`.

---

# Current configuration

Current `.env` contains:

```env
SAFEPAY_BASE_URL=
SAFEPAY_API_KEY=
SAFEPAY_MERCHANT_SECRET=
SAFEPAY_WEBHOOK_SECRET=
```

These are placeholders/redacted and must NOT be hardcoded.

The config currently resembles:

```php
return [
    'base_url' => env(
        'SAFEPAY_BASE_URL',
        'https://sandbox.api.getsafepay.com'
    ),

    'api_key' => env('SAFEPAY_API_KEY'),

    'merchant_secret' => env('SAFEPAY_MERCHANT_SECRET'),

    'webhook_secret' => env('SAFEPAY_WEBHOOK_SECRET'),

    'checkout_success_url' => env(
        'SAFEPAY_CHECKOUT_SUCCESS_URL',
        rtrim(env('APP_URL', 'http://localhost'), '/').'/safepay/success'
    ),

    'checkout_cancel_url' => env(
        'SAFEPAY_CHECKOUT_CANCEL_URL',
        rtrim(env('APP_URL', 'http://localhost'), '/').'/safepay/failed'
    ),
];
```

Make configuration robust and environment-aware.

Do not expose secrets in logs.

---

# Current routes

The current routes are approximately:

```php
Route::match(['get', 'post'], '/safepay/success', function (Request $request) {
    return Inertia::render('SafepaySuccess', [
        'tracker' => $request->input('tracker'),
        'signature' => $request->input('sig'),
        'reference' => $request->input('reference'),
    ]);
})->name('safepay.success');

Route::match(['get', 'post'], '/safepay/failed', function (Request $request) {
    return Inertia::render('SafepayFailed', [
        'tracker' => $request->input('tracker'),
    ]);
})->name('safepay.failed');
```

Do not trust the success redirect as proof of payment.

Create proper controller endpoints if necessary.

The success endpoint should:

1. Receive the Safepay tracker/reference.
2. Validate the input.
3. Retrieve the associated local Payment.
4. Verify the transaction with Safepay's server-to-server API.
5. Only after successful verification update the local payment/order state.
6. Prevent duplicate processing.
7. Then render the success page.

The failed endpoint should not mark the payment as successful.

---

# Current checkout architecture

The current controller approximately does:

```php
$orders = $this->orderService->placeOrder(
    $request,
    $validated,
    $cartItems
);

foreach ($orders as $order) {

    if ($validated['payment_method_id'] == PaymentMethod::CASH_ON_DELIVERY) {

        // COD

    } elseif ($validated['payment_method_id'] == PaymentMethod::SAFEPAY) {

        $response = $this->safepay->pay(
            $request,
            $order
        );

        if ($response && isset($response['success']) && $response['success'] == true) {
            return $this->formatResponse(
                $response['message'],
                $response
            );
        }

        // ...
    }
}
```

This is incorrect for the desired one-payment model because the `return` inside the loop means the first order can initiate the payment and the remaining orders are not represented by that Safepay payment.

Refactor this so that:

```text
multiple Orders
       ↓
one Payment
       ↓
one Safepay tracker
```

The existing `payment_orders` relationship/pivot should be reused if appropriate.

For example, conceptually:

```text
Payment #50
    ├── Order #101
    ├── Order #102
    └── Order #103

Payment #50
    └── Safepay tracker XYZ
```

Do not create one Safepay payment per order.

---

# Transaction and consistency requirements

The checkout process must be safe against partial state.

Consider using a database transaction where appropriate.

The system should not end up with:

```text
Order created
Payment failed to initialize
Some other orders missing
```

or:

```text
Payment marked PAID
Order update failed
```

Design the transaction boundaries carefully.

Do not hold a database transaction open while waiting for the user's Safepay interaction.

The correct lifecycle is roughly:

```text
DB transaction:
    create orders
    create local payment
    associate payment with orders
    commit

Then:
    initialize Safepay tracker
    store tracker on payment

Then:
    redirect user to Safepay

Later:
    verify payment
    update local state
```

If your existing architecture requires a different approach, explain why before implementing it.

---

# Payment amount

The Safepay amount must represent the entire checkout amount, not an individual shop order.

Use the application's authoritative total calculation.

Do not independently calculate totals in multiple places if an existing `OrderService` already calculates the checkout total.

Safepay's minor-unit requirements must be respected.

For PKR:

```text
PKR 500
→ 50000 minor units
```

But verify the current Safepay API requirement before implementing.

---

# Safepay verification

Inspect the current Safepay API implementation and determine the correct mechanism for:

1. Creating the payment tracker.
2. Redirecting to hosted checkout.
3. Receiving the return callback.
4. Verifying/retrieving the tracker.
5. Determining whether payment was actually successful.
6. Capturing/authorizing if required by the selected Safepay flow.
7. Handling failed/cancelled payments.
8. Handling duplicate callbacks.
9. Handling abandoned payments.

The existing service has methods resembling:

```php
createTracker()
processTransientToken()
authorize()
getTracker()
```

Determine which of these are actually necessary for a hosted checkout flow.

Do not leave unused payment flows in the service merely because they already exist.

---

# Security requirements

Do not:

* trust a browser redirect as payment confirmation
* trust arbitrary `tracker`, `reference`, or `status` values from the browser
* expose merchant secrets/API keys
* log secrets
* mark payment successful without server-side verification
* allow a user to verify another user's payment/order
* allow arbitrary payment amounts
* blindly trust a tracker supplied by a user

The local Payment should be associated with the authenticated user's orders.

If Safepay provides a signature/webhook mechanism, implement verification according to its current documentation.

---

# Idempotency

Payment verification must be idempotent.

For example, if Safepay calls:

```text
/safepay/success
```

multiple times, the system must not:

* create multiple payments
* duplicate payment-order associations
* duplicate order state transitions
* double-process inventory
* double-clear the cart
* double-send notifications

Use appropriate database constraints and/or state checks.

---

# Cart behavior

Inspect the current cart implementation.

For online payment:

Do not permanently delete cart items merely because the Safepay payment session was initialized.

Determine the correct point at which cart items should be removed.

A failed/cancelled payment should not incorrectly make the user's cart disappear unless that is an intentional business rule.

For a successful payment, cart cleanup should be idempotent.

---

# Return URL requirements

The application must support a publicly reachable HTTPS URL for Safepay callbacks during local development.

Do not hardcode localhost as a production callback.

Use configuration such as:

```env
SAFEPAY_CHECKOUT_SUCCESS_URL=https://your-public-domain/safepay/success
SAFEPAY_CHECKOUT_CANCEL_URL=https://your-public-domain/safepay/failed
```

If `APP_URL` is used as a fallback, make sure the behavior is explicit and safe.

---

# Environment handling

Make sandbox/production configuration explicit.

Prefer a dedicated environment variable such as:

```env
SAFEPAY_ENV=sandbox
```

if appropriate for the current Safepay API.

Do not infer the environment solely from whether `base_url` contains the word `sandbox` unless the Safepay documentation explicitly recommends that.

The final implementation should make it difficult to accidentally send sandbox credentials to production or production credentials to sandbox.

---

# API response

The checkout API should return enough information for the frontend to redirect the user.

For example:

```json
{
    "success": true,
    "message": "Payment initialized successfully.",
    "data": {
        "payment_id": 123,
        "checkout_url": "https://...",
        "reference": "...",
        "tracker": "..."
    }
}
```

Follow the application's existing `formatResponse()` structure instead of introducing an incompatible response format.

The frontend should redirect to `checkout_url`.

---

# Database changes

Inspect the existing schema before making migrations.

Only create migrations if they are actually required.

If changes are required, make them backwards-compatible where practical.

The final data model should support:

```text
one Payment
    ↓
many Orders

one Payment
    ↓
one Safepay tracker
```

Do not unnecessarily redesign the existing payment schema.

---

# Testing

Add/update tests for at least:

### 1. Checkout with one order

```text
1 order
1 payment
1 Safepay tracker
```

### 2. Checkout with multiple orders

```text
3 orders
1 payment
1 Safepay tracker
3 payment_order associations
```

### 3. Safepay initialization failure

Ensure orders/payment are left in a recoverable state.

### 4. Successful callback

Ensure:

```text
Payment → PAID
Orders → appropriate paid state
```

### 5. Failed callback

Ensure payment is not marked paid.

### 6. Duplicate callback

Calling the success endpoint twice must not duplicate processing.

### 7. Unauthorized tracker

A user must not be able to verify another user's payment.

### 8. Invalid/missing tracker

Return an appropriate error.

### 9. Invalid Safepay response

Do not mark anything paid.

### 10. Sandbox configuration

Ensure the generated checkout URL uses the correct Safepay Sandbox configuration.

Mock external Safepay HTTP calls in tests rather than making real API calls.

---

# Documentation research

Before implementing the Safepay-specific portion, consult the current official Safepay documentation/API references if internet access is available.

Verify:

* current Sandbox API base URL
* current production API base URL
* hosted checkout URL
* required checkout parameters
* tracker creation endpoint
* authentication headers
* amount format
* currency format
* redirect/callback behavior
* success/failure parameters
* payment verification endpoint
* capture/authorization requirements
* webhook behavior
* signature verification
* sandbox credentials/environment requirements

Do not rely on outdated examples found in random tutorials.

If the existing code conflicts with current official documentation, prefer the current official API.

Record the relevant API assumptions in code comments or a short project documentation file where useful.

---

# Logging

Add useful structured logs around:

```text
payment initialization started
tracker created
checkout URL generated
callback received
payment verification started
payment verification result
payment state transition
```

But NEVER log:

```text
API key
merchant secret
webhook secret
authorization credentials
full sensitive payment information
```

---

# Error handling

Use clear exceptions/error responses.

Do not swallow exceptions and return misleading generic success responses.

The current service catches `Throwable` and returns:

```php
[
    'success' => false,
    'message' => 'Unable to initialize payment.',
    'error' => $e->getMessage(),
]
```

Review whether exposing `$e->getMessage()` to the API client is appropriate.

Do not expose internal Safepay/API details to end users in production.

Log the technical error server-side.

---

# Important implementation constraint

Do not modify unrelated features.

Do not change the frontend unless required for the payment redirect/callback flow.

Do not rewrite the entire order system.

Prefer small, well-defined changes to:

* `SafePayService`
* `OrderController`
* payment callback controller
* routes
* config
* migrations/models only if necessary
* tests

---

# Deliverables

After implementation, provide me with:

1. A summary of the root cause of the current:

```text
Required environment is missing
```

error.

2. A list of files changed.

3. Explanation of the new payment lifecycle.

4. Explanation of how one payment is associated with multiple orders.

5. Exact `.env` variable names required, with placeholder values only.

6. Any required Artisan commands.

7. Test commands.

8. Example checkout API response.

9. Example success/failure callback flow.

10. Any Safepay dashboard configuration that I need to perform manually.

11. Any assumptions made because Safepay documentation/API behavior could not be verified.

Before finishing, run the relevant Laravel tests/static checks and fix any issues introduced by your changes.

Do not claim the integration works unless you have actually verified the relevant code/tests. If an actual Safepay sandbox transaction cannot be performed from the environment, clearly distinguish code-level verification from live sandbox verification.
