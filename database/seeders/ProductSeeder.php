<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Core test products with promotions
            [
                'sku' => 'A',
                'name' => 'Fresh Apple',
                'unit_price' => 0.50,
                'is_active' => true,
            ],
            [
                'sku' => 'B',
                'name' => 'Banana',
                'unit_price' => 0.30,
                'is_active' => true,
            ],
            [
                'sku' => 'C',
                'name' => 'Carrot',
                'unit_price' => 0.20,
                'is_active' => true,
            ],
            [
                'sku' => 'D',
                'name' => 'Donut',
                'unit_price' => 0.10,
                'is_active' => true,
            ],

            // Additional products for demo
            [
                'sku' => 'BREAD',
                'name' => 'Whole Wheat Bread',
                'unit_price' => 2.50,
                'is_active' => true,
            ],
            [
                'sku' => 'MILK',
                'name' => 'Organic Milk (1L)',
                'unit_price' => 3.99,
                'is_active' => true,
            ],
            [
                'sku' => 'EGGS',
                'name' => 'Free Range Eggs (12 pack)',
                'unit_price' => 4.50,
                'is_active' => true,
            ],
            [
                'sku' => 'CHEESE',
                'name' => 'Cheddar Cheese (500g)',
                'unit_price' => 6.99,
                'is_active' => true,
            ],
            [
                'sku' => 'COFFEE',
                'name' => 'Premium Coffee Beans (250g)',
                'unit_price' => 8.99,
                'is_active' => true,
            ],
            [
                'sku' => 'PASTA',
                'name' => 'Italian Pasta (500g)',
                'unit_price' => 1.99,
                'is_active' => true,
            ],
            [
                'sku' => 'RICE',
                'name' => 'Basmati Rice (1kg)',
                'unit_price' => 5.49,
                'is_active' => true,
            ],

            // Inactive product for testing
            [
                'sku' => 'INACTIVE',
                'name' => 'Inactive Product',
                'unit_price' => 9.99,
                'is_active' => false,
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
