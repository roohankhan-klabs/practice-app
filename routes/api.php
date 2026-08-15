<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\ShopController;

Route::prefix('api/v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::get('/cities/{stateId}', [CityController::class, 'cities']);
    Route::get('/states/{countryId}', [StateController::class, 'states']);
    Route::get('/countries', [CountryController::class, 'countries']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);

        Route::prefix('address')->group(function () {
            Route::get('/', [AddressController::class, 'index']);
            Route::post('/', [AddressController::class, 'store']);
            Route::get('/{id}', [AddressController::class, 'show']);
            Route::post('/{id}', [AddressController::class, 'update']);
            Route::delete('/{id}', [AddressController::class, 'destroy']);
        });
        Route::prefix('shop')->group(function(){
            Route::get('/', [ShopController::class, 'index']);
            Route::get('/{id}', [ShopController::class, 'show']);
        });
        Route::prefix('product')->group(function(){
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::post('/{id}/review', [ProductController::class, 'review']);
        });
        Route::prefix('wishlist')->group(function(){
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/', [WishlistController::class, 'toggle']);
            Route::delete('/{id}', [WishlistController::class, 'destroy']);
            Route::delete('/clear', [WishlistController::class, 'bulkDestroy']);
        });
    });
});
