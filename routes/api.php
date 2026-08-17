<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\InitController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // auth
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // general
    Route::get('/cities/{stateId}', [CityController::class, 'cities']);
    Route::get('/states/{countryId}', [StateController::class, 'states']);
    Route::get('/countries', [CountryController::class, 'countries']);

    // catalog
    Route::post('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::get('subcategories', [SubCategoryController::class, 'index']);
    Route::get('subcategories/{id}', [SubCategoryController::class, 'show']);

    // settings
    Route::post('settings', [SettingController::class, 'index']);
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

    Route::prefix('shops')->group(function () {
        Route::get('/', [ShopController::class, 'index']);
        Route::get('/{shopId}', [ShopController::class, 'show']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{productId}', [ProductController::class, 'show']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/init', [InitController::class, 'index']);
        // user
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);

        Route::post('/reviews', [ProductController::class, 'review']);

        Route::prefix('addresses')->group(function () {
            Route::get('/', [AddressController::class, 'index']);
            Route::post('/', [AddressController::class, 'store']);
            Route::get('/{addressId}', [AddressController::class, 'show']);
            Route::post('/{addressId}', [AddressController::class, 'update']);
            Route::delete('/{addressId}', [AddressController::class, 'destroy']);
        });
        Route::prefix('wishlists')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/', [WishlistController::class, 'toggle']);
            Route::delete('/clear', [WishlistController::class, 'bulkDestroy']);
        });
        Route::prefix('carts')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/', [CartController::class, 'store']);
            Route::put('/{cartItemId}', [CartController::class, 'update']);
            Route::delete('/{cartItemId}', [CartController::class, 'destroy']);
            Route::delete('/clear', [CartController::class, 'clearCart']);
        });

        Route::post('ready-for-checkout', [OrderController::class, 'readyForCheckout']);
        Route::post('checkout', [OrderController::class, 'checkout']);
        Route::post('safepay/transient-token', [PaymentController::class, 'handleTransientToken']);

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::get('/{orderId}', [OrderController::class, 'show']);
        });

        Route::prefix('payments')->group(function () {
            Route::get('/{paymentId}', [PaymentController::class, 'show']);
        });
    });
});
