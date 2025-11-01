<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InvalidSkuException;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class CheckoutService
{
    /**
     * Calculate the total price for the given items.
     *
     * @throws InvalidSkuException
     */
    public function calculate(array $items): array
    {
        $itemCounts = $this->buildItemCounts($items);
        $products = $this->fetchAndValidateProducts(array_keys($itemCounts));

        $breakdown = $this->buildBreakdown($itemCounts, $products);

        return [
            'breakdown' => $breakdown,
            'total' => array_sum(array_column($breakdown, 'line_total')),
            'regular_total' => array_sum(array_column($breakdown, 'regular_total')),
            'total_savings' => array_sum(array_column($breakdown, 'savings')),
            'items_input' => $items,
        ];
    }

    /**
     * Process the checkout: calculate and save the sale.
     *
     * @throws \Throwable
     */
    public function process(User $user, array $items): Sale
    {
        $calculation = $this->calculate($items);

        return DB::transaction(static function () use ($user, $calculation) {
            // Create the sale
            $sale = Sale::create([
                'user_id' => $user->id,
                'total_amount' => $calculation['total'],
                'items_input' => $calculation['items_input'],
            ]);

            // Create sale items
            foreach ($calculation['breakdown'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'sku' => $item['sku'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                ]);
            }

            return $sale->load('items');
        });
    }

    private function buildItemCounts(array $items): array
    {
        $counts = [];

        foreach ($items as $item) {
            $sku = strtoupper($item['sku']);
            $quantity = (int) $item['quantity'];
            $counts[$sku] = ($counts[$sku] ?? 0) + $quantity;
        }

        return $counts;
    }

    /**
     * Fetch products and validate all SKUs exist.
     *
     * @throws InvalidSkuException
     */
    private function fetchAndValidateProducts(array $skus): Collection
    {
        $products = Product::with('activePromotion')
            ->active()
            ->whereIn('sku', $skus)
            ->get()
            ->keyBy('sku');

        $invalidSkus = [];
        foreach ($skus as $sku) {
            if (! $products->has($sku)) {
                $invalidSkus[] = $sku;
            }
        }

        if (count($invalidSkus) > 0) {
            throw count($invalidSkus) === 1
                ? InvalidSkuException::forSku($invalidSkus[0])
                : InvalidSkuException::forMultipleSkus($invalidSkus);
        }

        return $products;
    }

    /**
     * Build breakdown of items with calculated prices.
     */
    private function buildBreakdown(array $itemCounts, Collection $products): array
    {
        $breakdown = [];

        foreach ($itemCounts as $sku => $quantity) {
            $product = $products[$sku];
            $regularTotal = $product->unit_price * $quantity;
            $actualTotal = $product->calculatePrice($quantity);
            $savings = $regularTotal - $actualTotal;
            $promotionApplied = $product->activePromotion && $savings > 0;

            $breakdown[] = [
                'sku' => $sku,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => (float) $product->unit_price,
                'regular_total' => $regularTotal,
                'line_total' => $actualTotal,
                'savings' => $savings,
                'promotion_applied' => $promotionApplied,
                'promotion' => $promotionApplied ? $product->activePromotion->toPromotionArray() : null,
            ];
        }

        return $breakdown;
    }
}
