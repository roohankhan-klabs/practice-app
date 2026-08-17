<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\SafePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct(
        private AddressService $addressService,
        private CartService $cartService,
        private OrderService $orderService,
        private readonly SafePayService $safepay
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->getUserOrders($request);

        return $this->formatResponse('Orders fetched successfully', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, $orderId): JsonResponse
    {
        $order = $this->orderService->getUserOrder($request, $orderId);
        if (! $order) {
            return $this->formatError('Order not found', 404);
        }

        return $this->formatResponse('Order fetched successfully', [
            'order' => $order,
        ]);
    }

    public function readyForCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_item_ids' => 'required|array',
            'cart_item_ids.*' => 'exists:cart_items,id',
        ]);
        $cartItems = $this->cartService->getUserCartItems($request, $validated['cart_item_ids']);
        if ($cartItems->isEmpty()) {
            return $this->formatError('Cart items not found', 404);
        }
        $addresses = Address::where('user_id', $request->user()->id)->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $bill = $this->orderService->calculateTotal($cartItems);

        return $this->formatResponse('Ready for checkout', [
            'addresses' => $addresses,
            'cart_items' => $cartItems,
            'payment_methods' => $paymentMethods,
            'bill' => $bill,
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'cart_item_ids' => 'required|array',
            'cart_item_ids.*' => 'exists:cart_items,id',
        ]);
        $address = $this->addressService->findUserAddress($request, $validated['address_id']);
        if (! $address) {
            return $this->formatError('Address not found', 404);
        }
        $paymentMethod = PaymentMethod::where('id', $validated['payment_method_id'])->where('is_active', true)->first();
        if (! $paymentMethod) {
            return $this->formatError('Payment method not found', 404);
        }

        $cartItems = $this->cartService->getUserCartItems($request, $validated['cart_item_ids']);
        if ($cartItems->isEmpty()) {
            return $this->formatError('Cart items not found', 404);
        }
        $bill = $this->orderService->calculateTotal($cartItems);
        $orders = $this->orderService->placeOrder($request, $validated, $cartItems);

        if ($validated['payment_method_id'] == PaymentMethod::CASH_ON_DELIVERY) {
            $payment = $this->payWithCashOnDelivery(
                $request,
                $orders,
                $validated,
                $bill
            );

            return $this->formatResponse(
                'Order placed successfully',
                [
                    'payment_id' => $payment->id,
                    'reference' => $orders->first()?->reference,
                ]
            );
        }

        if ($validated['payment_method_id'] == PaymentMethod::SAFEPAY) {
            $payment = $this->orderService->createPaymentForOrders(
                $orders,
                PaymentMethod::SAFEPAY,
                $bill
            );

            $response = $this->safepay->initializePayment(
                $request,
                $payment,
                $orders,
                $bill,
                $validated['cart_item_ids']
            );

            if ($response['success'] ?? false) {
                return $this->formatResponse(
                    $response['message'],
                    $response
                );
            }

            return $this->formatError(
                $response['message'] ?? 'Unable to initialize Safepay payment',
                400
            );
        }

        return $this->formatError('Unable to place order', 500);
    }

    public function payWithCashOnDelivery(Request $request, $orders, array $validated, array $bill)
    {
        $transaction_id = 'TXN-'.strtoupper(Str::random(12));
        $payment = $this->orderService->createPaymentForOrders(
            $orders,
            PaymentMethod::CASH_ON_DELIVERY,
            $bill,
            'PKR',
            PaymentStatus::COD_PENDING,
            $transaction_id
        );

        Order::whereIn('id', $orders->pluck('id'))
            ->update([
                'status' => Order::PENDING,
            ]);

        $this->cartService->clearCartItemsByIds(
            $request,
            $validated['cart_item_ids']
        );

        return $payment;
    }

    public function payWithJazzcash(Order $order, Payment $payment)
    {
        return false;
    }
}
