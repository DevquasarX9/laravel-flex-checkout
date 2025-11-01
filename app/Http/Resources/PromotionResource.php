<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PromotionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'special_price' => (float) $this->special_price,
            'is_active' => $this->when(
                isset($this->is_active),
                $this->is_active
            ),
            'product' => $this->when(
                $this->relationLoaded('product'),
                fn () => [
                    'id' => $this->product->id,
                    'sku' => $this->product->sku,
                    'name' => $this->product->name,
                    'unit_price' => (float) $this->product->unit_price,
                ]
            ),
        ];
    }
}
