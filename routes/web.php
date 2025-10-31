<?php

declare(strict_types=1);

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', static function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', static function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::controller(CheckoutController::class)
        ->name('checkout.')
        ->group(function (): void {
            Route::get('checkout', 'index')->name('index');
            Route::post('checkout', 'store')->name('store');
        });

    // Product management routes
    Route::resource('products', ProductController::class);

    // Promotion management routes
    Route::resource('promotions', PromotionController::class);
});

require __DIR__.'/settings.php';
