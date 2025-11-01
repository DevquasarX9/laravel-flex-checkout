<?php

declare(strict_types=1);

use App\Exceptions\InvalidSkuException;

test('it creates exception for single sku', function () {
    $exception = InvalidSkuException::forSku('INVALID');

    expect($exception)->toBeInstanceOf(InvalidSkuException::class)
        ->and($exception->getMessage())->toBe("Product with SKU 'INVALID' not found or is inactive.");
});

test('it creates exception for multiple skus', function () {
    $exception = InvalidSkuException::forMultipleSkus(['SKU1', 'SKU2', 'SKU3']);

    expect($exception)->toBeInstanceOf(InvalidSkuException::class)
        ->and($exception->getMessage())->toBe('The following SKUs are invalid: SKU1, SKU2, SKU3');
});

test('exception can be thrown and caught', function () {
    expect(fn () => throw InvalidSkuException::forSku('TEST'))
        ->toThrow(InvalidSkuException::class, "Product with SKU 'TEST' not found or is inactive.");
});

test('exception extends base exception class', function () {
    $exception = InvalidSkuException::forSku('TEST');

    expect($exception)->toBeInstanceOf(Exception::class);
});
