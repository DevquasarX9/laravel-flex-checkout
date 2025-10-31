<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProductRequest extends FormRequest
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
        $productId = $this->route('product') ? $this->route('product')->id : null;

        $skuRules = [
            'required',
            'string',
            'max:50',
            'alpha_num',
        ];

        // Add unique rule with proper handling for create vs update
        if ($productId) {
            $skuRules[] = Rule::unique('products', 'sku')->ignore($productId);
        } else {
            $skuRules[] = 'unique:products,sku';
        }

        return [
            'sku' => $skuRules,
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0|decimal:0,2',
            'is_active' => 'boolean',
        ];
    }
}
