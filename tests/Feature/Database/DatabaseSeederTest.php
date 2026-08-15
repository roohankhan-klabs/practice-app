<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Device;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use App\Models\Variant;
use App\Models\VariantOption;
use App\Models\Wishlist;
use Database\Seeders\DatabaseSeeder;

test('database seeder creates complete commerce demo data', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBeGreaterThanOrEqual(8)
        ->and(Shop::count())->toBeGreaterThanOrEqual(2)
        ->and(Product::count())->toBeGreaterThanOrEqual(6)
        ->and(Variant::count())->toBeGreaterThanOrEqual(12)
        ->and(Device::count())->toBeGreaterThanOrEqual(4)
        ->and(Cart::count())->toBeGreaterThanOrEqual(4)
        ->and(CartItem::count())->toBeGreaterThanOrEqual(4)
        ->and(Payment::count())->toBeGreaterThanOrEqual(2)
        ->and(Order::count())->toBeGreaterThanOrEqual(4)
        ->and(OrderItem::count())->toBeGreaterThanOrEqual(4)
        ->and(Wishlist::count())->toBeGreaterThanOrEqual(4)
        ->and(Review::count())->toBeGreaterThanOrEqual(2);

    $shop = Shop::query()->first();
    $product = Product::query()->first();
    $order = Order::query()->whereNotNull('payment_id')->first();
    $wishlist = Wishlist::query()->whereNotNull('user_id')->first();
    $cart = Cart::query()->whereNotNull('user_id')->first();

    expect($shop)->not->toBeNull()
        ->and($shop->user_id)->not->toBeNull()
        ->and($shop->address_id)->not->toBeNull()
        ->and($product)->not->toBeNull()
        ->and($product->shop_id)->toBe($shop->id)
        ->and($product->sub_category_id)->not->toBeNull()
        ->and($order)->not->toBeNull()
        ->and($order->user_id)->not->toBeNull()
        ->and($order->shop_id)->not->toBeNull()
        ->and($order->address_id)->not->toBeNull()
        ->and($order->payment_method_id)->not->toBeNull()
        ->and($order->payment_id)->not->toBeNull()
        ->and($wishlist)->not->toBeNull()
        ->and($wishlist->product_id)->not->toBeNull()
        ->and($cart)->not->toBeNull()
        ->and($cart->status)->not->toBeNull();
});

test('database seeder stores seeded variant option ids instead of legacy values json', function () {
    $this->seed(DatabaseSeeder::class);

    $variant = Variant::query()
        ->whereHas('product', fn ($query) => $query->where('slug', 'nova-x-pro-phone'))
        ->orderBy('id')
        ->firstOrFail();

    $optionIds = $variant->variant_option_ids;

    expect($optionIds)->toBeArray()
        ->and($optionIds)->toHaveCount(2)
        ->and($optionIds)->each->toBeInt();

    $optionNames = VariantOption::query()
        ->whereIn('id', $optionIds)
        ->pluck('name')
        ->sort()
        ->values()
        ->all();

    expect($optionNames)->toBe(['128GB', 'Red']);
});
