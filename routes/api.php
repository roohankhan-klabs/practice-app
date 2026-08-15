<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function () {
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

    // settings
    Route::post('settings', [SettingController::class, 'index']);
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

    Route::prefix('shop')->group(function () {
        Route::get('/', [ShopController::class, 'index']);
        Route::get('/{shopId}', [ShopController::class, 'show']);
    });

    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{productId}', [ProductController::class, 'show']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        // user
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);

        Route::post('/review', [ProductController::class, 'review']);

        Route::prefix('address')->group(function () {
            Route::get('/', [AddressController::class, 'index']);
            Route::post('/', [AddressController::class, 'store']);
            Route::get('/{addressId}', [AddressController::class, 'show']);
            Route::post('/{addressId}', [AddressController::class, 'update']);
            Route::delete('/{addressId}', [AddressController::class, 'destroy']);
        });
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/', [WishlistController::class, 'toggle']);
            Route::delete('/clear', [WishlistController::class, 'bulkDestroy']);
        });
        Route::prefix('carts')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/', [CartController::class, 'store']);
            Route::post('/{cartItemId}', [CartController::class, 'update']);
            Route::delete('/{cartItemId}', [CartController::class, 'destroy']);
            Route::delete('/clear', [CartController::class, 'clearCart']);
        });

        Route::post('checkout', [OrderController::class, 'checkout']);
    });
});
