<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->product = Product::factory()->create([
        'sku' => 'TEST',
        'name' => 'Test Product',
        'unit_price' => 10.00,
    ]);
});

test('store uses ActivatePromotion action to create promotion', function () {
    $response = $this->post(route('promotions.store'), [
        'product_id' => $this->product->id,
        'quantity' => 3,
        'special_price' => 25.00,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('promotions.index'))
        ->assertSessionHas('success', 'Promotion created successfully.');

    expect(Promotion::count())->toBe(1);

    $promotion = Promotion::first();
    expect($promotion->product_id)->toBe($this->product->id)
        ->and($promotion->quantity)->toBe(3)
        ->and((float) $promotion->special_price)->toBe(25.0);
});

test('store deactivates old promotions via action', function () {
    $oldPromotion = Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 2,
        'special_price' => 15.00,
    ]);

    $this->post(route('promotions.store'), [
        'product_id' => $this->product->id,
        'quantity' => 3,
        'special_price' => 25.00,
        'is_active' => true,
    ]);

    $oldPromotion->refresh();
    expect($oldPromotion->is_active)->toBeFalse();

    $newPromotion = Promotion::where('quantity', 3)->first();
    expect($newPromotion->is_active)->toBeTrue();
});

test('index returns promotions using resource', function () {
    Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    $response = $this->get(route('promotions.index'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('promotions/index')
            ->has('promotions.data', 1)
            ->has('promotions.data.0', fn ($promo) => $promo
                ->has('id')
                ->has('quantity')
                ->has('special_price')
                ->has('is_active')
                ->has('product')
                ->where('quantity', 3)
            )
        );
});

test('edit returns plain array for form compatibility', function () {
    $promotion = Promotion::factory()->forProduct($this->product)->create([
        'quantity' => 3,
        'special_price' => 25.00,
    ]);

    $response = $this->get(route('promotions.edit', $promotion));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('promotions/edit')
            ->has('promotion', fn ($promo) => $promo
                ->has('id')
                ->has('product_id')
                ->has('quantity')
                ->has('special_price')
                ->has('is_active')
                ->where('product_id', $this->product->id)
            )
        );
});
