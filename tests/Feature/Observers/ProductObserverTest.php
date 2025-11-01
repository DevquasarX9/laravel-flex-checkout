<?php

declare(strict_types=1);

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

uses()->group('observers');

beforeEach(function () {
    Cache::flush();
});

test('it clears cache when product is created', function () {
    // Set a cache value
    Cache::put('products.checkout', 'test-data', 60);
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Create a product
    Product::factory()->create([
        'sku' => 'NEW',
        'name' => 'New Product',
        'unit_price' => 15.00,
    ]);

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});

test('it clears cache when product is updated', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    // Set cache
    Cache::put('products.checkout', 'test-data', 60);
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Update product
    $product->update(['name' => 'Updated Product']);

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});

test('it clears cache when product is deleted', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    // Set cache
    Cache::put('products.checkout', 'test-data', 60);
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Delete product
    $product->delete();

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});
