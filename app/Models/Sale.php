<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'items_input',
    ];

    /**
     * Get the user that owns this sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Transform sale to Inertia array representation.
     */
    public function toInertiaArray(): array
    {
        // Eager load products if not already loaded to prevent N+1
        if (! $this->relationLoaded('items.product.activePromotion')) {
            $this->load('items.product.activePromotion');
        }

        $items = $this->items->map->toInertiaArray();
        $regularTotal = $items->sum('regular_total');
        $totalSavings = $items->sum('savings');

        return [
            'id' => $this->id,
            'total_amount' => (float) $this->total_amount,
            'regular_total' => $regularTotal,
            'total_savings' => $totalSavings,
            'items_input' => $this->items_input,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'items' => $items,
        ];
    }

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'items_input' => 'array',
        ];
    }
}
