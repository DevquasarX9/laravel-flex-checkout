<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

final class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'unit_price',
        'is_active',
    ];

    /**
     * Get active products formatted for list display.
     */
    public static function getActiveForList(): Collection
    {
        return self::active()
            ->orderBy('sku')
            ->get()
            ->map->toListArray();
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
     * Transform product to list array representation.
     */
    public function toListArray(): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'unit_price' => (float) $this->unit_price,
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
