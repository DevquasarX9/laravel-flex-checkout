<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'unit_price' => (float) $this->unit_price,
            'is_active' => $this->is_active,
            'promotion' => $this->whenLoaded(
                'activePromotion',
                fn () => $this->activePromotion
                    ? new PromotionResource($this->activePromotion)
                    : null
            ),
        ];
    }
}
