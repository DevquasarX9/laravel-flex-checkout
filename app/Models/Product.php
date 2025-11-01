<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(ProductObserver::class)]
final class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'unit_price',
        'is_active',
    ];

    public function scopeForList(Builder $query): Builder
    {
        return $query->active()
            ->orderBy('sku')
            ->select(['id', 'sku', 'name', 'unit_price']);
    }

    /**
     * Get all promotions for this product.
     */
    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    /**
     * Get the active promotion for this product.
     */
    public function activePromotion(): HasOne
    {
        return $this->hasOne(Promotion::class)->where('is_active', true);
    }

    /**
     * Calculate the price for a given quantity considering promotions.
     */
    public function calculatePrice(int $quantity): float
    {
        $promotion = $this->activePromotion;

        if (! $promotion) {
            return $quantity * $this->unit_price;
        }

        $fullSets = intdiv($quantity, $promotion->quantity);
        $remainder = $quantity % $promotion->quantity;

        return ($fullSets * $promotion->special_price) + ($remainder * $this->unit_price);
    }

    /**
     * Transform product to check out array representation.
     */
    public function toCheckoutArray(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'unit_price' => (float) $this->unit_price,
            'promotion' => $this->activePromotion?->toPromotionArray(),
        ];
    }

    /**
     * Scope to only include active products.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
