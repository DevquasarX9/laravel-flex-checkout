<?php

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

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
