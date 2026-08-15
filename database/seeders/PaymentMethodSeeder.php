<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder{
    public function run(): void {
        $paymentMethods = [
            [
                'name' => 'Visa',
                'code' => 'visa',
                'icon' => 'visa.png',
                'fee_type' => 'percentage',
                'fee_value' => '1.5',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Mastercard',
                'code' => 'mastercard',
                'icon' => 'mastercard.png',
                'fee_type' => 'percentage',
                'fee_value' => '1.5',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'American Express',
                'code' => 'american_express',
                'icon' => 'american_express.png',
                'fee_type' => 'percentage',
                'fee_value' => '2.0',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Discover',
                'code' => 'discover',
                'icon' => 'discover.png',
                'fee_type' => 'percentage',
                'fee_value' => '1.8',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Cash on Delivery',
                'code' => 'cash_on_delivery',
                'icon' => 'cash_on_delivery.png',
                'fee_type' => 'fixed',
                'fee_value' => '0.00',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'icon' => 'paypal.png',
                'fee_type' => 'percentage',
                'fee_value' => '2.5',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];
        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::query()->updateOrCreate(
                ['code' => $paymentMethod['code']],
                $paymentMethod,
            );
        }
    }
}
