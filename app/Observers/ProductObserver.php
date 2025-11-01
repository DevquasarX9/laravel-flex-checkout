<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;

final class ProductObserver
{
    public function saved(Product $product): void
    {
        $this->clearCache();
    }

    public function deleted(Product $product): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        Cache::forget(CacheKeys::CHECKOUT_PRODUCTS);
    }
}
