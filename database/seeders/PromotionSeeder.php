<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

final class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get products
        $productA = Product::where('sku', 'A')->first();
        $productB = Product::where('sku', 'B')->first();
        $apple = Product::where('sku', 'APPLE')->first();
        $bread = Product::where('sku', 'BREAD')->first();
        $milk = Product::where('sku', 'MILK')->first();
        $eggs = Product::where('sku', 'EGGS')->first();

        // Create promotions
        $promotions = [];

        // Core test promotions (needed for tests)
        if ($productA) {
            $promotions[] = [
                'product_id' => $productA->id,
                'quantity' => 3,
                'special_price' => 1.30,
                'is_active' => true,
            ];
        }

        if ($productB) {
            $promotions[] = [
                'product_id' => $productB->id,
                'quantity' => 2,
                'special_price' => 0.45,
                'is_active' => true,
            ];
        }

        // Additional realistic promotions
        if ($apple) {
            $promotions[] = [
                'product_id' => $apple->id,
                'quantity' => 4,
                'special_price' => 4.00, // Buy 4 apples for $4 (regular: $5.00)
                'is_active' => true,
            ];
        }

        if ($bread) {
            $promotions[] = [
                'product_id' => $bread->id,
                'quantity' => 2,
                'special_price' => 4.00, // 2 loaves for $4 (regular: $5.00)
                'is_active' => true,
            ];
        }

        if ($milk) {
            $promotions[] = [
                'product_id' => $milk->id,
                'quantity' => 3,
                'special_price' => 10.00, // 3 bottles for $10 (regular: $11.97)
                'is_active' => true,
            ];
        }

        if ($eggs) {
            $promotions[] = [
                'product_id' => $eggs->id,
                'quantity' => 2,
                'special_price' => 8.00, // 2 packs for $8 (regular: $9.00)
                'is_active' => false, // Inactive promotion for testing
            ];
        }

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}
