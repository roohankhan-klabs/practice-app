<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Models\PaymentMethod;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private AddressService $addressService,
        private CartService $cartService,
        private PaymentService $paymentService,
        private OrderService $orderService
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
        $orders = $this->orderService->placeOrder($request, $validated, $cartItems);
        $payment = $this->paymentService->createPayment($request, $validated, $orders);

        return $this->formatResponse('Order placed successfully', [
            'address' => $address,
            'orders' => $orders,
            'payment' => $payment,
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
}
