<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Centralized cache key definitions.
 */
final class CacheKeys
{
    /**
     * Cache key for checkout products list.
     */
    public const string CHECKOUT_PRODUCTS = 'products.checkout';

    /**
     * TTL for checkout products cache (in minutes).
     */
    public const int CHECKOUT_PRODUCTS_TTL = 15;
}
