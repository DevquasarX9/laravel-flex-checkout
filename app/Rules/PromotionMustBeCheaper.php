<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

final class PromotionMustBeCheaper implements DataAwareRule, ValidationRule
{
    /**
     * All the data under validation.
     */
    protected array $data = [];

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $productId = $this->data['product_id'] ?? null;
        $quantity = $this->data['quantity'] ?? null;

        if (! $productId || ! $quantity || ! $value) {
            return;
        }

        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $regularTotal = $product->unit_price * $quantity;

        if ($value >= $regularTotal) {
            $fail('The special price must be less than the regular price ('.number_format($regularTotal, 2).').');
        }
    }
}
