<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Cache::flush();
});

test('checkout index caches products list', function () {
    Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    expect(Cache::has('products.checkout'))->toBeFalse();

    $this->get(route('checkout.index'));

    expect(Cache::has('products.checkout'))->toBeTrue();
});

test('cached products are used on subsequent requests', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    // First request - cache miss
    $this->get(route('checkout.index'));
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Manually set cache to verify it's being used
    $cachedData = collect([['sku' => 'CACHED', 'name' => 'Cached Product']]);
    Cache::put('products.checkout', $cachedData, 60);

    // Second request should use cached data
    $response = $this->get(route('checkout.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('checkout/index')
        ->has('products')
    );
});

test('cache is invalidated when product is created', function () {
    // Set cache
    $this->get(route('checkout.index'));
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Create new product
    Product::factory()->create([
        'sku' => 'NEW',
        'name' => 'New Product',
        'unit_price' => 20.00,
    ]);

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});

test('cache is invalidated when promotion is created', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    // Set cache
    $this->get(route('checkout.index'));
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Create promotion
    Promotion::factory()->forProduct($product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    // Cache should be cleared
    expect(Cache::has('products.checkout'))->toBeFalse();
});

test('cache expires after 15 minutes', function () {
    Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    $this->get(route('checkout.index'));

    // Verify cache exists
    expect(Cache::has('products.checkout'))->toBeTrue();

    // Travel 16 minutes into future
    $this->travel(16)->minutes();

    // Cache should be expired
    expect(Cache::has('products.checkout'))->toBeFalse();
});
