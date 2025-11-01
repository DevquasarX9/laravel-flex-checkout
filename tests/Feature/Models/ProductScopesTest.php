<?php

declare(strict_types=1);

use App\Models\Product;

uses()->group('scopes');

beforeEach(function () {
    Product::factory()->create(['sku' => 'A', 'name' => 'Product A', 'unit_price' => 10.00]);
    Product::factory()->create(['sku' => 'C', 'name' => 'Product C', 'unit_price' => 30.00]);
    Product::factory()->create(['sku' => 'B', 'name' => 'Product B', 'unit_price' => 20.00]);
    Product::factory()->inactive()->create(['sku' => 'INACTIVE', 'name' => 'Inactive Product', 'unit_price' => 40.00]);
});

test('forList scope returns only active products', function () {
    $products = Product::forList()->get();

    expect($products)->toHaveCount(3)
        ->and($products->pluck('sku')->contains('INACTIVE'))->toBeFalse();
});

test('forList scope orders products by sku', function () {
    $products = Product::forList()->get();

    expect($products->pluck('sku')->toArray())->toBe(['A', 'B', 'C']);
});

test('forList scope selects only necessary columns', function () {
    $product = Product::forList()->first();

    expect($product)->toHaveKeys(['id', 'sku', 'name', 'unit_price'])
        ->and($product->getAttributes())->not->toHaveKey('is_active')
        ->and($product->getAttributes())->not->toHaveKey('created_at');
});

test('forList scope can be chained with other scopes', function () {
    $products = Product::forList()
        ->where('unit_price', '>', 15.00)
        ->get();

    expect($products)->toHaveCount(2)
        ->and($products->pluck('sku')->toArray())->toBe(['B', 'C']);
});

test('active scope works independently', function () {
    $products = Product::active()->get();

    expect($products)->toHaveCount(3);
});

test('forList is composable', function () {
    $query = Product::forList();

    expect($query)->toBeInstanceOf(Illuminate\Database\Eloquent\Builder::class);

    $result = $query->limit(2)->get();

    expect($result)->toHaveCount(2);
});
