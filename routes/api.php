<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('profile', [AuthController::class, 'profile'])->middleware('auth:api');
    Route::post('update-profile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');
});
