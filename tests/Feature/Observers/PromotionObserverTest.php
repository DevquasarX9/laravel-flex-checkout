<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Support\Facades\Cache;

uses()->group('observers');

beforeEach(function () {
    Cache::flush();

    $this->product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);
});

test('it clears cache when promotion is created', function () {
    // Set cache
    Cache::put('products.checkout', 'test-data', 60);
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Create promotion
    Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});

test('it clears cache when promotion is updated', function () {
    $promotion = Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    // Set cache
    Cache::put('products.checkout', 'test-data', 60);
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Update promotion
    $promotion->update(['quantity' => 5]);

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});

test('it clears cache when promotion is deleted', function () {
    $promotion = Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    // Set cache
    Cache::put('products.checkout', 'test-data', 60);
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Delete promotion
    $promotion->delete();

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});

test('cache invalidation is triggered on promotion activation', function () {
    $oldPromotion = Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 2,
        'special_price' => 15.00,
    ]);

    Cache::flush();
    Cache::put('products.checkout', 'test-data', 60);

    // Deactivate old promotion (simulating what ActivatePromotion does)
    $oldPromotion->update(['is_active' => false]);

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});
