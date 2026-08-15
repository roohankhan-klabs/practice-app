<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->nullable();
            $table->string('fingerprint')->nullable();
            $table->string('app_version')->nullable();
            $table->string('device_os')->nullable();
            $table->string('device_os_version')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_manufacturer')->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->string('device_token')->nullable();
            $table->ipAddress('last_ip_address')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
        Schema::create('device_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('shop_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('employee_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('shop_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('shop_name');
            $table->string('email');
            $table->string('country_code');
            $table->string('phone');
            $table->string('vat_registration_number')->nullable();
            $table->string('commercial_registration_number')->nullable();
            $table->string('status')->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order');
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order');
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->json('specifications');
            $table->unsignedInteger('price');
            $table->unsignedInteger('shipping_price')->default(0);
            $table->unsignedInteger('stock');
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->string('discount_type')->default('percentage');
            $table->float('discount_value')->nullable();
            $table->boolean('show_in_app')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('pending');
            $table->string('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_thumbnail')->default(false);
            $table->timestamps();
        });
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->json('values');
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock');
            $table->timestamps();
        });
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('status')->nullable();
            $table->timestamps();
        });
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('icon');
            $table->string('fee_type')->default('percentage');
            $table->float('fee_value')->default(0.00);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->string('reference_type');
            $table->string('reference_id');
            $table->string('transaction_id');
            $table->string('tap_charge_id')->nullable();
            $table->string('tap_tracking_id')->nullable();
            $table->string('status');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->nullable();
            $table->foreignId('address_id')->constrained()->cascadeOnDelete();
            $table->float('subtotal')->default(0.00);
            $table->float('shipping_fees')->default(0.00);
            $table->float('tax')->default(0.00);
            $table->float('discount')->default(0.00);
            $table->float('total_amount')->default(0.00);
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->float('price');
            $table->timestamps();
        });
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway');
            $table->string('charge_id');
            $table->string('event_type');
            $table->decimal('amount');
            $table->string('currency');
            $table->string('status');
            $table->json('payload');
            $table->json('response');
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->json('data');
            $table->string('reference_type')->nullable();
            $table->string('reference_id')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->timestamps();
        });
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();
        });
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('term');
            $table->timestamps();
        });
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value');
            $table->timestamps();
        });
        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('value');
            $table->string('hex_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_options');
        Schema::dropIfExists('variant_types');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('search_histories');
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('review_images');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('sub_categories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('shop_applications');
        Schema::dropIfExists('shop_staff');
        Schema::dropIfExists('device_users');
        Schema::dropIfExists('devices');
    }
};
