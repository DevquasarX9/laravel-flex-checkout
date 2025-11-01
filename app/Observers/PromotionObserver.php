<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Promotion;
use Illuminate\Support\Facades\Cache;

final class PromotionObserver
{
    public function saved(Promotion $promotion): void
    {
        $this->clearCache();
    }

    public function deleted(Promotion $promotion): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        Cache::forget('products.checkout');
    }
}
