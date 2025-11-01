<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->lexify('???')),
            'name' => fake()->words(3, true),
            'unit_price' => fake()->randomFloat(2, 0.50, 50.00),
            'is_active' => true,
        ];
    }

    public function inactive(): ProductFactory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withSku(string $sku): ProductFactory
    {
        return $this->state(fn (array $attributes) => [
            'sku' => $sku,
        ]);
    }

    public function withPrice(float $price): ProductFactory
    {
        return $this->state(fn (array $attributes) => [
            'unit_price' => $price,
        ]);
    }
}
