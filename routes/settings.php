<?php

declare(strict_types=1);

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function (): void {
    Route::redirect('settings', '/settings/profile');

    Route::controller(ProfileController::class)
        ->name('profile.')->prefix('settings/profile')
        ->group(function (): void {
            Route::get('', 'edit')->name('edit');
            Route::patch('', 'update')->name('update');
            Route::delete('', 'destroy')->name('destroy');
        });

    Route::controller(PasswordController::class)
        ->name('user-password.')->prefix('settings/password')
        ->group(function (): void {
            Route::get('', 'edit')->name('edit');

            Route::put('', 'update')
                ->middleware('throttle:6,1')
                ->name('update');
        });

    Route::get('settings/appearance', static function () {
        return Inertia::render('settings/appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');
});
