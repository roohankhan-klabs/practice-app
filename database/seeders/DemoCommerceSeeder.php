<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\City;
use App\Models\Device;
use App\Models\DeviceUser;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\ReviewReply;
use App\Models\SearchHistory;
use App\Models\Shop;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\Variant;
use App\Models\VariantOption;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = User::query()
            ->whereIn('email', ['vendor1@gmail.com', 'vendor2@gmail.com'])
            ->get()
            ->keyBy('email');

        $customers = User::query()
            ->whereIn('email', [
                'customer1@gmail.com',
                'customer2@gmail.com',
                'customer3@gmail.com',
                'customer4@gmail.com',
            ])
            ->get()
            ->keyBy('email');

        $paymentMethods = PaymentMethod::query()->pluck('id', 'code');
        $variantOptionIds = VariantOption::query()
            ->join('variant_types', 'variant_types.id', '=', 'variant_options.variant_type_id')
            ->select('variant_options.id', 'variant_options.name', 'variant_types.key')
            ->get()
            ->mapWithKeys(fn ($variantOption) => [
                $variantOption->key.':'.$variantOption->name => $variantOption->id,
            ]);

        $shops = collect([
            [
                'vendor_email' => 'vendor1@gmail.com',
                'shop_name' => 'Alpha Gadgets',
                'description' => 'Consumer electronics and smart accessories.',
                'contact_number' => '03001234567',
                'whatsapp_number' => '03001234567',
                'country' => 'PK',
                'city' => 'Karachi',
                'state' => 'Sindh',
                'address_line_1' => '12 Tech Street',
                'address_line_2' => 'Floor 2',
                'postal_code' => '75500',
                'shipping_fee_type' => 'fixed',
                'shipping_fee_amount' => '250',
                'estimated_delivery_time' => '2-4 days',
                'commission_percentage' => '12',
            ],
            [
                'vendor_email' => 'vendor2@gmail.com',
                'shop_name' => 'Desert Living',
                'description' => 'Home, beauty, wellness, and travel essentials.',
                'contact_number' => '39440011',
                'whatsapp_number' => '39440011',
                'country' => 'BH',
                'city' => 'Manama',
                'state' => 'Capital Governorate',
                'address_line_1' => '88 Commerce Avenue',
                'address_line_2' => null,
                'postal_code' => '322',
                'shipping_fee_type' => 'percentage',
                'shipping_fee_amount' => '5',
                'estimated_delivery_time' => '1-3 days',
                'commission_percentage' => '10',
            ],
        ])->mapWithKeys(function (array $shopData) use ($vendors) {
            $vendor = $vendors->get($shopData['vendor_email']);

            $shop = Shop::query()->updateOrCreate(
                ['shop_name' => $shopData['shop_name']],
                [
                    'user_id' => $vendor->id,
                    'description' => $shopData['description'],
                    'cover_image' => Str::slug($shopData['shop_name']).'-cover.jpg',
                    'logo' => Str::slug($shopData['shop_name']).'-logo.jpg',
                    'whatsapp_number' => $shopData['whatsapp_number'],
                    'contact_number' => $shopData['contact_number'],
                    'shipping_policy' => 'Orders ship within 24 hours of confirmation.',
                    'refund_policy' => 'Refunds are reviewed within 3 business days.',
                    'return_policy' => 'Returns accepted within 7 days for unopened items.',
                    'privacy_policy' => 'Customer data is handled under platform policy.',
                    'terms_of_service' => 'Orders are subject to stock availability.',
                    'google_maps_link' => 'https://maps.example.com/'.Str::slug($shopData['shop_name']),
                    'avg_rating' => '4.8',
                    'total_reviews' => '24',
                    'status' => 'active',
                    'is_featured' => '1',
                    'shipping_fee_type' => $shopData['shipping_fee_type'],
                    'shipping_fee_amount' => $shopData['shipping_fee_amount'],
                    'estimated_delivery_time' => $shopData['estimated_delivery_time'],
                    'commission_percentage' => $shopData['commission_percentage'],
                    'instagram' => '@'.Str::snake($shopData['shop_name']),
                    'facebook' => Str::headline($shopData['shop_name']),
                    'tiktok' => '@'.Str::slug($shopData['shop_name'], ''),
                ],
            );

            $address = $this->upsertAddress(
                shop: $shop,
                lineOne: $shopData['address_line_1'],
                lineTwo: $shopData['address_line_2'],
                postalCode: $shopData['postal_code'],
                countryCode: $shopData['country'],
                stateName: $shopData['state'],
                cityName: $shopData['city'],
                phone: $shopData['contact_number'],
            );

            $shop->forceFill(['address_id' => (string) $address->id])->save();

            return [$shopData['shop_name'] => $shop];
        });

        $products = [
            [
                'shop_name' => 'Alpha Gadgets',
                'category' => 'Electronics',
                'sub_category' => 'Mobiles',
                'name' => 'Nova X Pro Phone',
                'slug' => 'nova-x-pro-phone',
                'price' => 32999,
                'shipping_price' => 250,
                'stock' => 40,
                'is_featured' => true,
                'variants' => [
                    ['values' => ['color' => 'Red', 'storage' => '128GB'], 'price' => 32999, 'stock' => 18],
                    ['values' => ['color' => 'Blue', 'storage' => '256GB'], 'price' => 36999, 'stock' => 12],
                ],
            ],
            [
                'shop_name' => 'Alpha Gadgets',
                'category' => 'Electronics',
                'sub_category' => 'Laptops',
                'name' => 'Orbit Air Laptop',
                'slug' => 'orbit-air-laptop',
                'price' => 124999,
                'shipping_price' => 500,
                'stock' => 15,
                'is_featured' => true,
                'variants' => [
                    ['values' => ['ram' => '8GB', 'storage' => '256GB'], 'price' => 124999, 'stock' => 8],
                    ['values' => ['ram' => '16GB', 'storage' => '512GB'], 'price' => 149999, 'stock' => 5],
                ],
            ],
            [
                'shop_name' => 'Alpha Gadgets',
                'category' => 'Electronics',
                'sub_category' => 'Tablets',
                'name' => 'Pixel Slate Go',
                'slug' => 'pixel-slate-go',
                'price' => 45999,
                'shipping_price' => 300,
                'stock' => 25,
                'is_featured' => false,
                'variants' => [
                    ['values' => ['color' => 'Green', 'storage' => '128GB'], 'price' => 45999, 'stock' => 10],
                    ['values' => ['color' => 'Blue', 'storage' => '256GB'], 'price' => 51999, 'stock' => 9],
                ],
            ],
            [
                'shop_name' => 'Desert Living',
                'category' => 'Beauty',
                'sub_category' => 'Perfumes',
                'name' => 'Amber Dusk Perfume',
                'slug' => 'amber-dusk-perfume',
                'price' => 8900,
                'shipping_price' => 150,
                'stock' => 60,
                'is_featured' => true,
                'variants' => [
                    ['values' => ['pack_size' => '1 Pack'], 'price' => 8900, 'stock' => 30],
                    ['values' => ['pack_size' => '2 Pack'], 'price' => 16900, 'stock' => 14],
                ],
            ],
            [
                'shop_name' => 'Desert Living',
                'category' => 'Health',
                'sub_category' => 'Supplements',
                'name' => 'Daily Balance Supplements',
                'slug' => 'daily-balance-supplements',
                'price' => 4200,
                'shipping_price' => 100,
                'stock' => 70,
                'is_featured' => false,
                'variants' => [
                    ['values' => ['pack_size' => '1 Pack'], 'price' => 4200, 'stock' => 35],
                    ['values' => ['pack_size' => '3 Pack'], 'price' => 11900, 'stock' => 18],
                ],
            ],
            [
                'shop_name' => 'Desert Living',
                'category' => 'Travel',
                'sub_category' => 'Outdoor',
                'name' => 'TrailLite Carry Pack',
                'slug' => 'traillite-carry-pack',
                'price' => 11500,
                'shipping_price' => 175,
                'stock' => 28,
                'is_featured' => false,
                'variants' => [
                    ['values' => ['color' => 'Red', 'size' => 'Medium'], 'price' => 11500, 'stock' => 12],
                    ['values' => ['color' => 'Blue', 'size' => 'Large'], 'price' => 12400, 'stock' => 8],
                ],
            ],
        ];

        $seededProducts = collect();

        foreach ($products as $productData) {
            $shop = $shops->get($productData['shop_name']);
            $category = Category::query()->where('name', $productData['category'])->firstOrFail();
            $subCategory = SubCategory::query()
                ->where('category_id', $category->id)
                ->where('name', $productData['sub_category'])
                ->firstOrFail();

            $product = Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'shop_id' => $shop->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $productData['name'],
                    'description' => 'Demo product seeded for local development and API exploration.',
                    'specifications' => json_encode([
                        'brand' => Str::before($productData['name'], ' '),
                        'warranty' => '1 year',
                        'support' => 'Local support available',
                    ]),
                    'price' => $productData['price'],
                    'shipping_price' => $productData['shipping_price'],
                    'stock' => $productData['stock'],
                    'low_stock_threshold' => 5,
                    'discount_type' => 'percentage',
                    'discount_value' => 10,
                    'show_in_app' => true,
                    'is_featured' => $productData['is_featured'],
                    'status' => 'active',
                    'reviewed_by' => 'seed-bot',
                    'reviewed_at' => now()->subDay(),
                    'rejection_reason' => null,
                ],
            );

            foreach ([1, 2] as $sortOrder) {
                ProductImage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'sort_order' => $sortOrder,
                    ],
                    [
                        'image' => $product->slug."-{$sortOrder}.jpg",
                        'alt_text' => $product->name.' image '.$sortOrder,
                        'is_thumbnail' => $sortOrder === 1,
                    ],
                );
            }

            foreach ($productData['variants'] as $variantData) {
                $optionIds = collect($variantData['values'])
                    ->map(fn (string $optionName, string $variantTypeKey) => $variantOptionIds->get($variantTypeKey.':'.$optionName))
                    ->sort()
                    ->values()
                    ->all();

                Variant::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variant_option_ids' => $optionIds,
                    ],
                    [
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ],
                );
            }

            $seededProducts->push($product->fresh());
        }

        $devices = collect([
            'customer1@gmail.com' => ['device_id' => 'ios-customer-1', 'device_type' => 'phone', 'device_name' => 'iPhone 15', 'device_os' => 'iOS', 'device_os_version' => '18.0'],
            'customer2@gmail.com' => ['device_id' => 'android-customer-2', 'device_type' => 'phone', 'device_name' => 'Galaxy S24', 'device_os' => 'Android', 'device_os_version' => '15'],
            'customer3@gmail.com' => ['device_id' => 'ipad-customer-3', 'device_type' => 'tablet', 'device_name' => 'iPad Air', 'device_os' => 'iPadOS', 'device_os_version' => '18.0'],
            'customer4@gmail.com' => ['device_id' => 'web-customer-4', 'device_type' => 'web', 'device_name' => 'Chrome Desktop', 'device_os' => 'Windows', 'device_os_version' => '11'],
        ])->mapWithKeys(function (array $deviceData, string $email) use ($customers) {
            $customer = $customers->get($email);
            $device = Device::query()->updateOrCreate(
                ['device_id' => $deviceData['device_id']],
                [
                    'fingerprint' => sha1($deviceData['device_id']),
                    'app_version' => '1.0.0',
                    'device_os' => $deviceData['device_os'],
                    'device_os_version' => $deviceData['device_os_version'],
                    'device_type' => $deviceData['device_type'],
                    'device_name' => $deviceData['device_name'],
                    'device_manufacturer' => $deviceData['device_os'] === 'Android' ? 'Samsung' : 'Apple',
                    'is_mobile' => $deviceData['device_type'] !== 'web',
                    'device_token' => 'token-'.$deviceData['device_id'],
                    'last_ip_address' => '127.0.0.1',
                    'last_activity_at' => now()->subMinutes(15),
                ],
            );

            DeviceUser::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'device_id' => $device->id,
                ],
                [],
            );

            return [$email => $device];
        });

        $productIndex = $seededProducts->keyBy('slug');

        $cartDefinitions = [
            ['email' => 'customer1@gmail.com', 'status' => 'active', 'slug' => 'nova-x-pro-phone', 'variant_index' => 0, 'quantity' => 1],
            ['email' => 'customer2@gmail.com', 'status' => 'active', 'slug' => 'amber-dusk-perfume', 'variant_index' => 1, 'quantity' => 2],
            ['email' => 'customer3@gmail.com', 'status' => 'saved', 'slug' => 'daily-balance-supplements', 'variant_index' => 0, 'quantity' => 1],
            ['email' => 'customer4@gmail.com', 'status' => 'active', 'slug' => 'traillite-carry-pack', 'variant_index' => 1, 'quantity' => 1],
        ];

        foreach ($cartDefinitions as $cartDefinition) {
            $customer = $customers->get($cartDefinition['email']);
            $device = $devices->get($cartDefinition['email']);
            $product = $productIndex->get($cartDefinition['slug']);
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

        $orders = [
            ['reference' => 'ORD-1001', 'email' => 'customer1@gmail.com', 'slug' => 'nova-x-pro-phone', 'variant_index' => 0, 'payment_method' => 'visa', 'status' => 'completed', 'quantity' => 1],
            ['reference' => 'ORD-1002', 'email' => 'customer2@gmail.com', 'slug' => 'amber-dusk-perfume', 'variant_index' => 1, 'payment_method' => 'paypal', 'status' => 'processing', 'quantity' => 2],
            ['reference' => 'ORD-1003', 'email' => 'customer3@gmail.com', 'slug' => 'orbit-air-laptop', 'variant_index' => 0, 'payment_method' => 'cash_on_delivery', 'status' => 'pending', 'quantity' => 1],
            ['reference' => 'ORD-1004', 'email' => 'customer4@gmail.com', 'slug' => 'traillite-carry-pack', 'variant_index' => 1, 'payment_method' => 'visa', 'status' => 'delivered', 'quantity' => 1],
        ];

        foreach ($orders as $orderData) {
            $customer = $customers->get($orderData['email']);
            $device = $devices->get($orderData['email']);
            $product = $productIndex->get($orderData['slug']);
            $variant = $product->variants()->orderBy('id')->get()[$orderData['variant_index']];
            $shop = $product->shop;
            $address = Address::query()->where('user_id', $customer->id)->where('is_default', true)->firstOrFail();

            $payment = null;

            if ($orderData['payment_method'] !== 'cash_on_delivery') {
                $payment = Payment::query()->updateOrCreate(
                    ['transaction_id' => 'txn-'.$orderData['reference']],
                    [
                        'user_id' => $customer->id,
                        'payment_method_id' => $paymentMethods[$orderData['payment_method']],
                        'reference_type' => 'order',
                        'reference_id' => $orderData['reference'],
                        'tap_charge_id' => 'charge-'.$orderData['reference'],
                        'tap_tracking_id' => 'track-'.$orderData['reference'],
                        'status' => in_array($orderData['status'], ['completed', 'processing', 'delivered'], true) ? 'paid' : 'pending',
                        'paid_at' => in_array($orderData['status'], ['completed', 'delivered'], true) ? now()->subDays(2) : now()->subDay(),
                    ],
                );
            }

            $subtotal = $variant->price * $orderData['quantity'];
            $shippingFees = (float) $product->shipping_price;
            $tax = round($subtotal * 0.05, 2);
            $discount = $orderData['status'] === 'completed' ? round($subtotal * 0.1, 2) : 0.0;
            $total = $subtotal + $shippingFees + $tax - $discount;

            $order = Order::query()->updateOrCreate(
                ['reference' => $orderData['reference']],
                [
                    'user_id' => $customer->id,
                    'shop_id' => $shop->id,
                    'address_id' => $address->id,
                    'subtotal' => $subtotal,
                    'shipping_fees' => $shippingFees,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total_amount' => $total,
                    'payment_method_id' => $paymentMethods[$orderData['payment_method']],
                    'payment_id' => $payment?->id,
                    'paid_at' => $payment?->paid_at,
                    'status' => $orderData['status'],
                ],
            );

            if ($payment) {
                $payment->forceFill(['reference_id' => (string) $order->id])->save();

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
                        'payload' => json_encode(['reference' => $order->reference]),
                        'response' => json_encode(['status' => $payment->status]),
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
                    'is_read' => in_array($order->status, ['completed', 'delivered'], true),
                    'sent_at' => now()->subHours(12),
                    'read_at' => in_array($order->status, ['completed', 'delivered'], true) ? now()->subHours(6) : null,
                ],
            );

            if (in_array($order->status, ['completed', 'delivered'], true)) {
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

    private function upsertAddress(
        string $lineOne,
        ?string $lineTwo,
        string $postalCode,
        string $countryCode,
        string $stateName,
        string $cityName,
        string $phone,
        ?User $user = null,
        ?Shop $shop = null,
    ): Address {
        $city = City::query()
            ->where('name', $cityName)
            ->whereHas('state', fn ($query) => $query->where('name', $stateName))
            ->whereHas('country', fn ($query) => $query->where('code', $countryCode))
            ->firstOrFail();

        return Address::query()->updateOrCreate(
            [
                'user_id' => $user?->id,
                'shop_id' => $shop?->id,
                'address_line_1' => $lineOne,
            ],
            [
                'address_line_2' => $lineTwo,
                'preffered_contact_number' => $phone,
                'postal_code' => $postalCode,
                'city_id' => $city->id,
                'state_id' => $city->state_id,
                'country_id' => $city->country_id,
                'is_default' => true,
            ],
        );
    }
}
