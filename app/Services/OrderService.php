<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Enums\PaymentStatus;

class OrderService
{
    public function getUserOrders(Request $request)
    {
        $orders = Order::with('orderItems')->where('user_id', $request->user()->id)->get();
        return $orders;
    }

    public function getUserOrder(Request $request, int $orderId)
    {
        $order = Order::with('orderItems.product', 'orderItems.variant')->where('user_id', $request->user()->id)->find($orderId);
        return $order;
    }

    public function calculateTotal(Collection $cartItems): array
    {
        $subtotal = 0;
        $shippingFees = 0;
        $discount = 0;
        $tax = 0.00;

        foreach ($cartItems as $cartItem) {
            $unitPrice = $cartItem->variant ? $cartItem->variant->price : $cartItem->product->price;
            $quantity = $cartItem->quantity;

            $discountAmountPerUnit = 0;
            $product = $cartItem->product;
            if ($product->discount_value > 0) {
                if ($product->discount_type === 'percentage') {
                    $discountAmountPerUnit = ($unitPrice * $product->discount_value) / 100;
                } elseif ($product->discount_type === 'fixed') {
                    $discountAmountPerUnit = $product->discount_value;
                }
                $discountAmountPerUnit = min($unitPrice, $discountAmountPerUnit);
            }

            $subtotal += $unitPrice * $quantity;
            $discount += $discountAmountPerUnit * $quantity;
            $shippingFees += ($product->shipping_price ?? 0) * $quantity;
        }

        $totalAmount = max(0, $subtotal - $discount + $shippingFees + $tax);

        return [
            'subtotal' => $subtotal,
            'shipping_fees' => $shippingFees,
            'tax' => $tax,
            'discount' => $discount,
            'total_amount' => $totalAmount,
        ];
    }
    /**
     * @param  array<string, mixed>  $validated
     * @param  Collection<int, CartItem>  $cartItems
     * @return Collection<int, Order>
     */
    public function placeOrder(Request $request, array $validated, Collection $cartItems): Collection
    {
        return DB::transaction(function () use ($request, $validated, $cartItems) {
            $cartItems->load(['product.shop', 'variant']);

            $reference = 'ORD-'.strtoupper(Str::random(12));
            /** @var Collection<int, Order> $orders */
            $orders = new Collection;

            /** @var \Illuminate\Support\Collection<int, Collection<int, CartItem>> $groupedItems */
            $groupedItems = $cartItems->groupBy(fn ($item) => $item->product->shop_id);

            foreach ($groupedItems as $shopId => $items) {
                $subtotal = 0;
                $shippingFees = 0;
                $discount = 0;
                $tax = 0.00;

                foreach ($items as $cartItem) {
                    $unitPrice = $cartItem->variant ? $cartItem->variant->price : $cartItem->product->price;
                    $quantity = $cartItem->quantity;

                    $discountAmountPerUnit = 0;
                    $product = $cartItem->product;
                    if ($product->discount_value > 0) {
                        if ($product->discount_type === 'percentage') {
                            $discountAmountPerUnit = ($unitPrice * $product->discount_value) / 100;
                        } elseif ($product->discount_type === 'fixed') {
                            $discountAmountPerUnit = $product->discount_value;
                        }
                        $discountAmountPerUnit = min($unitPrice, $discountAmountPerUnit);
                    }

                    $subtotal += $unitPrice * $quantity;
                    $discount += $discountAmountPerUnit * $quantity;
                    $shippingFees += ($product->shipping_price ?? 0) * $quantity;
                }

                $totalAmount = max(0, $subtotal - $discount + $shippingFees + $tax);

                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'shop_id' => $shopId,
                    'reference' => $reference,
                    'address_id' => $validated['address_id'],
                    'subtotal' => $subtotal,
                    'shipping_fees' => $shippingFees,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total_amount' => $totalAmount,
                    'status' => Order::INITIALIZED,
                ]);

                foreach ($items as $cartItem) {
                    $unitPrice = $cartItem->variant ? $cartItem->variant->price : $cartItem->product->price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'variant_id' => $cartItem->variant_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $unitPrice,
                    ]);
                }

                $orders->push($order);
            }
            return $orders;
        });
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @param  array{subtotal:mixed,shipping_fees:mixed,tax:mixed,discount:mixed,total_amount:mixed}  $bill
     */
    public function createPaymentForOrders(
        Collection $orders,
        int $paymentMethodId,
        array $bill,
        string $currency = 'PKR',
        PaymentStatus $status = PaymentStatus::PENDING,
        ?string $transactionId = null
    ): Payment {
        return DB::transaction(function () use (
            $orders,
            $paymentMethodId,
            $bill,
            $currency,
            $status,
            $transactionId
        ) {
            $payment = Payment::create([
                'payment_method_id' => $paymentMethodId,
                'transaction_id' => $transactionId,
                'amount' => (string) $bill['total_amount'],
                'currency' => $currency,
                'status' => $status->value,
            ]);

            foreach ($orders as $order) {
                PaymentOrder::create([
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                ]);
            }

            Order::whereIn('id', $orders->pluck('id'))
                ->update([
                    'payment_id' => $payment->id,
                ]);

            return $payment->load('orders');
        });
    }
}
