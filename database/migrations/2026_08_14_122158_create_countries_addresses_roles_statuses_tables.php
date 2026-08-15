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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('iso_code');
            $table->string('phone_code');
            $table->string('phone_number_digits')->default(10);
            $table->string('country_code');
            $table->string('currency');
            $table->string('currency_code');
            $table->string('currency_symbol');
            $table->string('currency_exchange_rate');
            $table->string('currency_exchange_rate_date');
            $table->timestamps();
        });

        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('shop_name');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('logo')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address_id')->nullable();
            $table->string('shipping_policy')->nullable();
            $table->string('refund_policy')->nullable();
            $table->string('return_policy')->nullable();
            $table->string('privacy_policy')->nullable();
            $table->string('terms_of_service')->nullable();
            $table->string('google_maps_link')->nullable();
            $table->string('avg_rating')->nullable();
            $table->string('total_reviews')->nullable();
            $table->string('status')->nullable();
            $table->string('is_featured')->nullable();
            $table->string('shipping_fee_type')->nullable();
            $table->string('shipping_fee_amount')->nullable();
            $table->string('estimated_delivery_time')->nullable();
            $table->string('commission_percentage')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('tiktok')->nullable();
            $table->timestamps();
        });
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('preffered_contact_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city_id');
            $table->string('state_id');
            $table->string('country_id');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('shops');
    }
};
