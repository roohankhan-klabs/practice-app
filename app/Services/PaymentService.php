<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private readonly SafepayService $safepay
    ) {}

    /**
     * @param  Collection<int, Order>  $orders
     */
    public function createPayment(Request $request, array $validated, Collection $orders)
    {
        $payment = new Payment;
        foreach ($orders as $order) {
            if ($validated['payment_method_id'] == PaymentMethod::CASH_ON_DELIVERY) {
                return $this->payWithCashOnDelivery($order, $payment, $validated);
            } elseif ($validated['payment_method_id'] == PaymentMethod::JAZZCASH) {
                return $this->payWithJazzcash($order, $payment);
            } elseif ($validated['payment_method_id'] == PaymentMethod::SAFEPAY) {
                return $this->safepay->pay($request, $order, $payment);
            }
        }
    }

    public function payWithCashOnDelivery(Order $order, Payment $payment, array $validated)
    {
        $transaction_id = 'TXN-' . strtoupper(Str::random(12));
        $payment = $payment::create([
            'order_id' => $order->id,
            'payment_method_id' => PaymentMethod::CASH_ON_DELIVERY,
            'transaction_id' => $transaction_id,
            'amount' => $order->total_amount,
            'currency' => 'PKR',
            'status' => PaymentStatus::PENDING,
        ]);
        $order->update([
            'payment_id' => $payment->id,
            'status' => Order::PENDING,
        ]);

        CartItem::whereIn('id', $validated['cart_item_ids'])->delete();

        return $payment;
    }

    public function payWithJazzcash(Order $order, Payment $payment)
    {
        return false;
    }
}
