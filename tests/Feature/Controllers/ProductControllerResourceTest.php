<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('index returns products using resource', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    Promotion::factory()->forProduct($product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    $response = $this->get(route('products.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('products/index')
            ->has('products.data', 1)
            ->has('products.data.0', fn ($product) => $product
                ->has('id')
                ->has('sku')
                ->has('name')
                ->has('unit_price')
                ->has('is_active')
                ->has('promotion')
                ->where('promotion.quantity', 3)
                ->where('promotion.special_price', 25)
            )
        );
});

test('show returns product with promotions using resource', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    Promotion::factory()->forProduct($product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    $response = $this->get(route('products.show', $product));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('products/show')
            ->has('product', fn ($prod) => $prod
                ->has('id')
                ->has('sku')
                ->where('sku', 'TEST')
            )
        );
})->skip('To be implemented');

test('edit returns plain array for form compatibility', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);

    $response = $this->get(route('products.edit', $product));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('products/edit')
            ->has('product', fn ($prod) => $prod
                ->has('id')
                ->has('sku')
                ->has('name')
                ->has('unit_price')
                ->has('is_active')
                ->where('sku', 'TEST')
                ->where('unit_price', 10)
            )
        );
});
