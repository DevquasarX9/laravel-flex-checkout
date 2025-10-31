<?php

use App\Models\Product;
use App\Models\Promotion;

test('calculatePrice returns unit price times quantity without promotion', function () {
    $product = new Product;
    $product->unit_price = 0.50;
    // No promotion set

    $price = $product->calculatePrice(2);

    expect($price)->toBe(1.00);
});

test('calculatePrice applies promotion for exact quantity', function () {
    $product = new Product;
    $product->unit_price = 0.50;

    $promotion = new Promotion;
    $promotion->quantity = 3;
    $promotion->special_price = 1.30;

    // Manually set the relation
    $product->setRelation('activePromotion', $promotion);

    $price = $product->calculatePrice(3);

    expect($price)->toBe(1.30);
});

test('calculatePrice handles quantity less than promotion threshold', function () {
    $product = new Product;
    $product->unit_price = 0.50;

    $promotion = new Promotion;
    $promotion->quantity = 3;
    $promotion->special_price = 1.30;

    $product->setRelation('activePromotion', $promotion);

    // Buy 2, but promotion requires 3
    $price = $product->calculatePrice(2);

    expect($price)->toBe(1.00); // 2 × 0.50
});

test('calculatePrice handles quantity with remainder', function () {
    $product = new Product;
    $product->unit_price = 0.50;

    $promotion = new Promotion;
    $promotion->quantity = 3;
    $promotion->special_price = 1.30;

    $product->setRelation('activePromotion', $promotion);

    // Buy 4: 1 set of 3 + 1 remainder
    $price = $product->calculatePrice(4);

    expect($price)->toBe(1.80); // 1.30 + 0.50
});

test('calculatePrice handles multiple promotion sets', function () {
    $product = new Product;
    $product->unit_price = 0.50;

    $promotion = new Promotion;
    $promotion->quantity = 3;
    $promotion->special_price = 1.30;

    $product->setRelation('activePromotion', $promotion);

    // Buy 6: 2 sets of 3
    $price = $product->calculatePrice(6);

    expect($price)->toBe(2.60); // 2 × 1.30
});

test('calculatePrice handles multiple sets with remainder', function () {
    $product = new Product;
    $product->unit_price = 0.50;

    $promotion = new Promotion;
    $promotion->quantity = 3;
    $promotion->special_price = 1.30;

    $product->setRelation('activePromotion', $promotion);

    // Buy 7: 2 sets of 3 + 1 remainder
    $price = $product->calculatePrice(7);

    expect($price)->toBe(3.10); // (2 × 1.30) + 0.50
});

test('calculatePrice handles zero quantity', function () {
    $product = new Product;
    $product->unit_price = 0.50;

    $price = $product->calculatePrice(0);

    expect($price)->toBe(0.00);
});

test('calculatePrice with different promotion example', function () {
    $product = new Product;
    $product->unit_price = 0.30;

    $promotion = new Promotion;
    $promotion->quantity = 2;
    $promotion->special_price = 0.45;

    $product->setRelation('activePromotion', $promotion);

    // Buy 5: 2 sets of 2 + 1 remainder
    $price = $product->calculatePrice(5);

    expect($price)->toBe(1.20); // (2 × 0.45) + 0.30
});
