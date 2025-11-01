<?php

use App\Models\Product;
use App\Models\Promotion;
use App\Services\CheckoutService;

uses()->group('checkout');

beforeEach(function () {
    // Create test products
    $productA = Product::factory()->withSku('A')->withPrice(0.50)->create(['name' => 'Product A']);
    $productB = Product::factory()->withSku('B')->withPrice(0.30)->create(['name' => 'Product B']);
    Product::factory()->withSku('C')->withPrice(0.20)->create(['name' => 'Product C']);
    Product::factory()->withSku('D')->withPrice(0.10)->create(['name' => 'Product D']);

    // Create promotions
    Promotion::factory()->forProduct($productA)->create([
        'quantity' => 3,
        'special_price' => 1.30,
    ]);

    Promotion::factory()->forProduct($productB)->create([
        'quantity' => 2,
        'special_price' => 0.45,
    ]);

    $this->service = new CheckoutService;
});

test('price 1xA returns 0.50', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 1]]);
    expect($result['total'])->toBe(0.50);
});

test('price 1xA, 1xB returns 0.80', function () {
    $result = $this->service->calculate([
        ['sku' => 'A', 'quantity' => 1],
        ['sku' => 'B', 'quantity' => 1],
    ]);
    expect($result['total'])->toBe(0.80);
});

test('price 1xC, 1xD, 1xB, 1xA returns 1.10', function () {
    $result = $this->service->calculate([
        ['sku' => 'C', 'quantity' => 1],
        ['sku' => 'D', 'quantity' => 1],
        ['sku' => 'B', 'quantity' => 1],
        ['sku' => 'A', 'quantity' => 1],
    ]);
    expect($result['total'])->toBe(1.10);
});

test('price 2xA returns 1.00', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 2]]);
    expect($result['total'])->toBe(1.00);
});

test('price 3xA returns 1.30 (promotion)', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 3]]);
    expect($result['total'])->toBe(1.30);
});

test('price 4xA returns 1.80', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 4]]);
    expect($result['total'])->toBe(1.80);
});

test('price 5xA returns 2.30', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 5]]);
    expect($result['total'])->toBe(2.30);
});

test('price 6xA returns 2.60 (2x promotion)', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 6]]);
    expect($result['total'])->toBe(2.60);
});

test('price 3xA, 1xB returns 1.60', function () {
    $result = $this->service->calculate([
        ['sku' => 'A', 'quantity' => 3],
        ['sku' => 'B', 'quantity' => 1],
    ]);
    expect($result['total'])->toBe(1.60);
});

test('price 3xA, 2xB returns 1.75 (both promotions)', function () {
    $result = $this->service->calculate([
        ['sku' => 'A', 'quantity' => 3],
        ['sku' => 'B', 'quantity' => 2],
    ]);
    expect($result['total'])->toBe(1.75);
});

test('price 3xA, 2xB, 1xD returns 1.85', function () {
    $result = $this->service->calculate([
        ['sku' => 'A', 'quantity' => 3],
        ['sku' => 'B', 'quantity' => 2],
        ['sku' => 'D', 'quantity' => 1],
    ]);
    expect($result['total'])->toBe(1.85);
});

test('price 1xD, 3xA, 2xB returns 1.85 (order independent)', function () {
    $result = $this->service->calculate([
        ['sku' => 'D', 'quantity' => 1],
        ['sku' => 'A', 'quantity' => 3],
        ['sku' => 'B', 'quantity' => 2],
    ]);
    expect($result['total'])->toBe(1.85);
});

test('handles invalid sku', function () {
    $this->service->calculate([['sku' => 'Z', 'quantity' => 1]]);
})->throws(Exception::class, "Product with SKU 'Z' not found or is inactive.");

test('calculation returns correct breakdown', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 3]]);

    expect($result)->toHaveKey('breakdown');
    expect($result)->toHaveKey('total');
    expect($result)->toHaveKey('items_input');
    expect($result['breakdown'])->toHaveCount(1);
    expect($result['breakdown'][0])->toMatchArray([
        'sku' => 'A',
        'quantity' => 3,
        'line_total' => 1.30,
    ]);
    expect($result['items_input'])->toBe([['sku' => 'A', 'quantity' => 3]]);
});

test('merges duplicate SKUs', function () {
    $result = $this->service->calculate([
        ['sku' => 'A', 'quantity' => 2],
        ['sku' => 'A', 'quantity' => 1],
    ]);

    expect($result['total'])->toBe(1.30); // 3xA with promotion
    expect($result['breakdown'])->toHaveCount(1);
    expect($result['breakdown'][0]['quantity'])->toBe(3);
});

// Savings calculation tests
test('calculates regular_total correctly for item without promotion', function () {
    $result = $this->service->calculate([['sku' => 'C', 'quantity' => 2]]);

    expect($result['regular_total'])->toBe(0.40); // 2 * 0.20
    expect($result['breakdown'][0]['regular_total'])->toBe(0.40);
});

test('calculates regular_total correctly for item with promotion', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 3]]);

    expect($result['regular_total'])->toBe(1.50); // 3 * 0.50
    expect($result['breakdown'][0]['regular_total'])->toBe(1.50);
});

test('calculates savings when promotion is applied', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 3]]);

    expect(round($result['total_savings'], 2))->toBe(0.20); // 1.50 - 1.30
    expect(round($result['breakdown'][0]['savings'], 2))->toBe(0.20);
    expect($result['breakdown'][0]['promotion_applied'])->toBeTrue();
});

test('calculates zero savings when no promotion is applied', function () {
    $result = $this->service->calculate([['sku' => 'C', 'quantity' => 2]]);

    expect($result['total_savings'])->toBe(0.0);
    expect($result['breakdown'][0]['savings'])->toBe(0.0);
    expect($result['breakdown'][0]['promotion_applied'])->toBeFalse();
    expect($result['breakdown'][0]['promotion'])->toBeNull();
});

test('calculates savings for multiple items with mixed promotions', function () {
    $result = $this->service->calculate([
        ['sku' => 'A', 'quantity' => 3], // promotion: saves 0.20
        ['sku' => 'B', 'quantity' => 2], // promotion: saves 0.15
        ['sku' => 'C', 'quantity' => 1], // no promotion
    ]);

    // Regular totals: 1.50 + 0.60 + 0.20 = 2.30
    expect(round($result['regular_total'], 2))->toBe(2.30);

    // Actual total: 1.30 + 0.45 + 0.20 = 1.95
    expect($result['total'])->toBe(1.95);

    // Total savings: 0.20 + 0.15 + 0.00 = 0.35
    expect(round($result['total_savings'], 2))->toBe(0.35);

    // Check individual items
    expect($result['breakdown'][0]['promotion_applied'])->toBeTrue(); // A
    expect($result['breakdown'][1]['promotion_applied'])->toBeTrue(); // B
    expect($result['breakdown'][2]['promotion_applied'])->toBeFalse(); // C
});

test('includes promotion details when promotion is applied', function () {
    $result = $this->service->calculate([['sku' => 'A', 'quantity' => 3]]);

    expect($result['breakdown'][0]['promotion'])->not()->toBeNull();
    expect($result['breakdown'][0]['promotion']['quantity'])->toBe(3);
    expect($result['breakdown'][0]['promotion']['special_price'])->toBe(1.30);
});
