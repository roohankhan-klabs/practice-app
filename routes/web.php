<?php

use App\Http\Controllers\Api\SafePayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SafepayCallbackController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'home')->name('home');
Route::inertia('/welcome', 'welcome')->name('welcome');
Route::inertia('/cart', 'cart')->name('cart');
Route::inertia('/checkout', 'checkout')->name('checkout');

Route::get('/categories/{id}', function ($id) {
    return Inertia::render('category', [
        'id' => $id,
    ]);
})->name('category.show');
Route::get('/subcategories/{id}', function ($id) {
    return Inertia::render('subcategory', [
        'id' => $id,
    ]);
})->name('subcategory.show');
Route::get('/products/{id}', function ($id) {
    return Inertia::render('product', [
        'id' => $id,
    ]);
})->name('product.show');
Route::get('/shops/{id}', function ($id) {
    return Inertia::render('shop', [
        'id' => $id,
    ]);
})->name('shop.show');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

Route::inertia('pay','pay')->name('pay');
Route::match(['get', 'post'], '/safepay/success', [SafepayCallbackController::class, 'success'])->name('safepay.success');
Route::match(['get', 'post'], '/safepay/failed', [SafepayCallbackController::class, 'failed'])->name('safepay.failed');

// 4111 1111 1111 1111
// 5555 5555 5555 4444

// 4456 5300 0000 1096
// 5200 0000 0000 1096

// 4456 5300 0000 1104
// 5200 0000 0000 1104

// 405100 → insufficient balance
// 400400 → stolen card
// 405400 → expired card

require __DIR__.'/settings.php';
