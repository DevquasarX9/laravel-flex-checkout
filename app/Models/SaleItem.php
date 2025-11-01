<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'sku',
        'product_name',
        'quantity',
        'line_total',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }

    /**
     * Transform sale item to Inertia array representation.
     */
    public function toInertiaArray(): array
    {
        // Use eager-loaded product if available, otherwise query
        $product = $this->relationLoaded('product')
            ? $this->product
            : Product::where('sku', $this->sku)->first();

        $unitPrice = $product ? (float) $product->unit_price : 0;
        $regularTotal = $unitPrice * $this->quantity;
        $savings = max(0, $regularTotal - (float) $this->line_total);
        $promotionApplied = $savings > 0;

        // Get current active promotion if exists
        $promotion = null;
        if ($product && $promotionApplied) {
            $activePromotion = $product->relationLoaded('activePromotion')
                ? $product->activePromotion
                : $product->activePromotion()->first();

            if ($activePromotion) {
                $promotion = $activePromotion->toPromotionArray();
            }
        }

        return [
            'sku' => $this->sku,
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'unit_price' => $unitPrice,
            'regular_total' => $regularTotal,
            'line_total' => (float) $this->line_total,
            'savings' => $savings,
            'promotion_applied' => $promotionApplied,
            'promotion' => $promotion,
        ];
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'line_total' => 'decimal:2',
        ];
    }
}
