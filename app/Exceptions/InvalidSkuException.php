<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class InvalidSkuException extends Exception
{
    public static function forSku(string $sku): self
    {
        return new self("Product with SKU '{$sku}' not found or is inactive.");
    }

    public static function forMultipleSkus(array $skus): self
    {
        $skuList = implode(', ', $skus);

        return new self("The following SKUs are invalid: {$skuList}");
    }
}
