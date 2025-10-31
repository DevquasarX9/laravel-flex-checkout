<?php

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create test products
    $productA = Product::create(['sku' => 'A', 'name' => 'Product A', 'unit_price' => 0.50, 'is_active' => true]);
    $productB = Product::create(['sku' => 'B', 'name' => 'Product B', 'unit_price' => 0.30, 'is_active' => true]);
    Product::create(['sku' => 'C', 'name' => 'Product C', 'unit_price' => 0.20, 'is_active' => true]);
    Product::create(['sku' => 'D', 'name' => 'Product D', 'unit_price' => 0.10, 'is_active' => true]);

    // Create promotions
    Promotion::create([
        'product_id' => $productA->id,
        'quantity' => 3,
        'special_price' => 1.30,
        'is_active' => true,
    ]);

    Promotion::create([
        'product_id' => $productB->id,
        'quantity' => 2,
        'special_price' => 0.45,
        'is_active' => true,
    ]);
});

test('authenticated user can access checkout page', function () {
    $response = $this->actingAs($this->user)
        ->get(route('checkout.index'));

    $response->assertStatus(200);
});

test('guest cannot access checkout page', function () {
    $response = $this->get(route('checkout.index'));

    $response->assertRedirect(route('login'));
});

test('user can submit valid checkout', function () {
    $response = $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [
                ['sku' => 'A', 'quantity' => 3],
                ['sku' => 'B', 'quantity' => 2],
                ['sku' => 'C', 'quantity' => 1],
            ],
        ]);

    $response->assertStatus(200);
});

test('checkout creates sale record', function () {
    $items = [['sku' => 'A', 'quantity' => 3]];

    $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => $items,
        ]);

    $sale = Sale::where('user_id', $this->user->id)->first();

    expect($sale)->not->toBeNull();
    expect((float) $sale->total_amount)->toBe(1.30);
    expect($sale->items_input)->toBe($items);
});

test('checkout creates sale items', function () {
    $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [
                ['sku' => 'A', 'quantity' => 3],
                ['sku' => 'B', 'quantity' => 2],
            ],
        ]);

    $sale = Sale::where('user_id', $this->user->id)->first();

    expect($sale->items)->toHaveCount(2);
    expect($sale->items->pluck('sku')->toArray())->toContain('A', 'B');
});

test('checkout with invalid sku returns error', function () {
    $response = $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [
                ['sku' => 'X', 'quantity' => 1],
                ['sku' => 'Y', 'quantity' => 1],
                ['sku' => 'Z', 'quantity' => 1],
            ],
        ]);

    $response->assertSessionHasErrors('items');
});

test('checkout with empty items array returns validation error', function () {
    $response = $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [],
        ]);

    $response->assertSessionHasErrors('items');
});

test('checkout with invalid quantity returns validation error', function () {
    $response = $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [
                ['sku' => 'A', 'quantity' => 0],
            ],
        ]);

    $response->assertSessionHasErrors('items.0.quantity');
});

test('checkout with missing sku returns validation error', function () {
    $response = $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [
                ['quantity' => 1],
            ],
        ]);

    $response->assertSessionHasErrors('items.0.sku');
});

test('checkout calculates correct total with promotions', function () {
    $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [
                ['sku' => 'A', 'quantity' => 3],
                ['sku' => 'B', 'quantity' => 2],
            ],
        ]);

    $sale = Sale::where('user_id', $this->user->id)->first();

    expect((float) $sale->total_amount)->toBe(1.75);
});

test('checkout handles mixed products correctly', function () {
    $this->actingAs($this->user)
        ->post(route('checkout.store'), [
            'items' => [
                ['sku' => 'D', 'quantity' => 1],
                ['sku' => 'A', 'quantity' => 3],
                ['sku' => 'B', 'quantity' => 2],
            ],
        ]);

    $sale = Sale::where('user_id', $this->user->id)->first();

    expect((float) $sale->total_amount)->toBe(1.85);
});
