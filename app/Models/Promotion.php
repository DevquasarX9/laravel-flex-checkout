<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\PromotionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(PromotionObserver::class)]
final class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'special_price',
        'is_active',
    ];

    /**
     * Get the product that owns this promotion.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Transform promotion to array representation.
     */
    public function toPromotionArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'special_price' => (float) $this->special_price,
        ];
    }

    /**
     * Scope to only include active promotions.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'special_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
