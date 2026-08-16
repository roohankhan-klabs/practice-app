<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Device;
use App\Models\Product;
use App\Models\SearchHistory;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCartSeeder extends Seeder
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

        $cartDefinitions = [
            ['email' => 'customer1@gmail.com', 'device_id' => 'ios-customer-1', 'status' => 'active', 'slug' => 'nova-x-pro-phone', 'variant_index' => 0, 'quantity' => 1],
            ['email' => 'customer2@gmail.com', 'device_id' => 'android-customer-2', 'status' => 'active', 'slug' => 'amber-dusk-perfume', 'variant_index' => 1, 'quantity' => 2],
            ['email' => 'customer3@gmail.com', 'device_id' => 'ipad-customer-3', 'status' => 'saved', 'slug' => 'daily-balance-supplements', 'variant_index' => 0, 'quantity' => 1],
            ['email' => 'customer4@gmail.com', 'device_id' => 'web-customer-4', 'status' => 'active', 'slug' => 'traillite-carry-pack', 'variant_index' => 1, 'quantity' => 1],
        ];

        foreach ($cartDefinitions as $cartDefinition) {
            $customer = $customers->get($cartDefinition['email']);
            $device = $devices->get($cartDefinition['device_id']);
            $product = Product::query()->where('slug', $cartDefinition['slug'])->firstOrFail();
            $variant = $product->variants()->orderBy('id')->get()[$cartDefinition['variant_index']];

            $cart = Cart::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'device_id' => $device->id,
                ],
                ['status' => $cartDefinition['status']],
            );

            CartItem::query()->updateOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                ],
                ['quantity' => $cartDefinition['quantity']],
            );

            Wishlist::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'device_id' => $device->id,
                    'product_id' => $product->id,
                ],
                [],
            );

            SearchHistory::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'device_id' => $device->id,
                    'term' => Str::of($product->name)->lower()->value(),
                ],
                [],
            );
        }
    }
}
