<?php

use App\Http\Controllers\Api\SafePayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

// Route::get('safepay/success', [SafePayController::class, 'success'])->name('safepay.success');
// Route::get('safepay/failed', [SafePayController::class, 'failed'])->name('safepay.failed');

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
