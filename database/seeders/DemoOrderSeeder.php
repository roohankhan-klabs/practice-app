<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\ReviewReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::query()
            ->whereIn('email', [
                'customer1@gmail.com',
                'customer2@gmail.com',
                'customer3@gmail.com',
                'customer4@gmail.com',
            ])
            ->get()
            ->keyBy('email');

        $devices = Device::query()
            ->whereIn('device_id', [
                'ios-customer-1',
                'android-customer-2',
                'ipad-customer-3',
                'web-customer-4',
            ])
            ->get()
            ->keyBy('device_id');

        $paymentMethods = PaymentMethod::query()->pluck('id', 'code');

        $orders = [
            ['reference' => 'ORD-1001', 'email' => 'customer1@gmail.com', 'device_id' => 'ios-customer-1', 'slug' => 'nova-x-pro-phone', 'variant_index' => 0, 'payment_method' => 'visa', 'status' => Order::DELIVERED, 'quantity' => 1],
            ['reference' => 'ORD-1002', 'email' => 'customer2@gmail.com', 'device_id' => 'android-customer-2', 'slug' => 'amber-dusk-perfume', 'variant_index' => 1, 'payment_method' => 'safepay', 'status' => Order::PROCESSING, 'quantity' => 2],
            ['reference' => 'ORD-1003', 'email' => 'customer3@gmail.com', 'device_id' => 'ipad-customer-3', 'slug' => 'orbit-air-laptop', 'variant_index' => 0, 'payment_method' => 'cash_on_delivery', 'status' => Order::PENDING, 'quantity' => 1],
            ['reference' => 'ORD-1004', 'email' => 'customer4@gmail.com', 'device_id' => 'web-customer-4', 'slug' => 'traillite-carry-pack', 'variant_index' => 1, 'payment_method' => 'visa', 'status' => Order::DELIVERED, 'quantity' => 1],
        ];

        foreach ($orders as $orderData) {
            $customer = $customers->get($orderData['email']);
            $device = $devices->get($orderData['device_id']);
            $product = Product::query()->where('slug', $orderData['slug'])->firstOrFail();
            $variant = $product->variants()->orderBy('id')->get()[$orderData['variant_index']];
            $shop = $product->shop;
            $address = Address::query()->where('user_id', $customer->id)->where('is_default', true)->firstOrFail();

            $subtotal = $variant->price * $orderData['quantity'];
            $shippingFees = (float) $product->shipping_price;
            $tax = round($subtotal * 0.05, 2);
            $discount = $orderData['status'] === Order::DELIVERED ? round($subtotal * 0.1, 2) : 0.0;
            $total = $subtotal + $shippingFees + $tax - $discount;

            $order = Order::query()->firstOrNew(['reference' => $orderData['reference']]);
            $order->forceFill([
                'user_id' => $customer->id,
                'shop_id' => $shop->id,
                'address_id' => $address->id,
                'reference' => $orderData['reference'],
                'subtotal' => $subtotal,
                'shipping_fees' => $shippingFees,
                'tax' => $tax,
                'discount' => $discount,
                'total_amount' => $total,
                'status' => $orderData['status'],
            ])->save();

            $payment = null;

            if ($orderData['payment_method'] !== 'cash_on_delivery') {
                $paymentStatus = in_array($orderData['status'], [Order::PROCESSING, Order::DELIVERED], true) ? 'paid' : 'pending';
                $payment = Payment::query()->firstOrNew(['transaction_id' => 'txn-'.$orderData['reference']]);
                $payment->forceFill([
                    'payment_method_id' => $paymentMethods[$orderData['payment_method']],
                    'transaction_id' => 'txn-'.$orderData['reference'],
                    'amount' => $total,
                    'currency' => 'USD',
                    'status' => $paymentStatus,
                    'response' => ['status' => $paymentStatus, 'reference' => $order->reference],
                    'paid_at' => $orderData['status'] === Order::DELIVERED ? now()->subDays(2) : now()->subDay(),
                ])->save();

                $order->payment_id = $payment->id;
                $order->save();

                PaymentLog::query()->updateOrCreate(
                    [
                        'payment_id' => $payment->id,
                        'order_id' => $order->id,
                        'event_type' => 'charge.captured',
                    ],
                    [
                        'gateway' => 'tap',
                        'charge_id' => 'charge-'.$orderData['reference'],
                        'amount' => $total,
                        'currency' => 'USD',
                        'status' => $payment->status,
                        'payload' => ['reference' => $order->reference],
                        'response' => ['status' => $payment->status],
                        'ip_address' => '127.0.0.1',
                        'paid_at' => $payment->paid_at,
                    ],
                );
            }

            OrderItem::query()->updateOrCreate(
                [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                ],
                [
                    'quantity' => $orderData['quantity'],
                    'price' => $variant->price,
                ],
            );

            Notification::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'device_id' => $device->id,
                    'title' => 'Order '.$order->reference,
                ],
                [
                    'type' => 'order',
                    'data' => json_encode([
                        'order_reference' => $order->reference,
                        'status' => $order->status,
                    ]),
                    'reference_type' => 'order',
                    'reference_id' => (string) $order->id,
                    'is_sent' => true,
                    'is_read' => $order->status === Order::DELIVERED,
                    'sent_at' => now()->subHours(12),
                    'read_at' => $order->status === Order::DELIVERED ? now()->subHours(6) : null,
                ],
            );

            if ($order->status === Order::DELIVERED) {
                $review = Review::query()->updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'user_id' => $customer->id,
                        'shop_id' => $shop->id,
                        'rating' => 5,
                        'title' => 'Excellent purchase',
                        'comment' => 'Seeded review for development and QA flows.',
                    ],
                );

                ReviewImage::query()->updateOrCreate(
                    [
                        'review_id' => $review->id,
                        'image' => $product->slug.'-review.jpg',
                    ],
                    [],
                );

                ReviewReply::query()->updateOrCreate(
                    [
                        'review_id' => $review->id,
                        'user_id' => $shop->user_id,
                    ],
                    ['message' => 'Thanks for your order and feedback.'],
                );
            }
        }
    }
}
