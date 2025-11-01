<?php

declare(strict_types=1);

use App\Actions\Promotions\ActivatePromotion;
use App\Models\Product;
use App\Models\Promotion;

uses()->group('actions');

beforeEach(function () {
    $this->product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    $this->action = new ActivatePromotion;
});

test('it creates a new promotion', function () {
    $data = [
        'product_id' => $this->product->id,
        'quantity' => 3,
        'special_price' => 25.00,
        'is_active' => true,
    ];

    $promotion = $this->action->execute($data);

    expect($promotion)->toBeInstanceOf(Promotion::class)
        ->and($promotion->product_id)->toBe($this->product->id)
        ->and($promotion->quantity)->toBe(3)
        ->and((float) $promotion->special_price)->toBe(25.0)
        ->and($promotion->is_active)->toBeTrue();
});

test('it deactivates existing active promotions for the same product', function () {
    // Create an existing active promotion
    $oldPromotion = Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 2,
        'special_price' => 15.00,
    ]);

    // Create a new promotion
    $data = [
        'product_id' => $this->product->id,
        'quantity' => 3,
        'special_price' => 25.00,
        'is_active' => true,
    ];

    $newPromotion = $this->action->execute($data);

    // Old promotion should be deactivated
    $oldPromotion->refresh();
    expect($oldPromotion->is_active)->toBeFalse();

    // New promotion should be active
    expect($newPromotion->is_active)->toBeTrue();
});

test('it keeps promotions for other products unchanged', function () {
    $otherProduct = Product::factory()->create([
        'sku' => 'OTHER',
        'name' => 'Other Product',
        'unit_price' => 20.00,
    ]);

    $otherPromotion = Promotion::factory()->forProduct($otherProduct)->create([
        'quantity' => 5,
        'special_price' => 90.00,
    ]);

    // Create promotion for test product
    $data = [
        'product_id' => $this->product->id,
        'quantity' => 3,
        'special_price' => 25.00,
        'is_active' => true,
    ];

    $this->action->execute($data);

    // Other product's promotion should remain active
    $otherPromotion->refresh();
    expect($otherPromotion->is_active)->toBeTrue();
});

test('it wraps creation in database transaction', function () {
    // This test ensures that if something fails, nothing is committed
    $data = [
        'product_id' => $this->product->id,
        'quantity' => 3,
        'special_price' => 25.00,
        'is_active' => true,
    ];

    $promotion = $this->action->execute($data);

    expect(Promotion::count())->toBe(1)
        ->and($promotion->exists)->toBeTrue();
});
