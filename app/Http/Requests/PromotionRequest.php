<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

final class PromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:2',
            'special_price' => 'required|numeric|min:0|decimal:0,2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->product_id && $this->quantity && $this->special_price) {
                $product = Product::find($this->product_id);

                if ($product) {
                    $regularTotal = $product->unit_price * $this->quantity;

                    if ($this->special_price >= $regularTotal) {
                        $validator->errors()->add(
                            'special_price',
                            'The special price must be less than the regular price ('.number_format($regularTotal, 2).').'
                        );
                    }
                }
            }
        });
    }
}
