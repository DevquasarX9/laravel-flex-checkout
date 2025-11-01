<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Models\Promotion;
use Illuminate\Support\Facades\DB;

final class ActivatePromotion
{
    /**
     * @throws \Throwable
     */
    public function execute(array $data): Promotion
    {
        return DB::transaction(static function () use ($data) {
            // Deactivate existing active promotions for this product
            Promotion::where('product_id', $data['product_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Create and activate new promotion
            return Promotion::create($data);
        });
    }
}
