<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\PromotionMustBeCheaper;
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
            'special_price' => ['required', 'numeric', 'min:0', 'decimal:0,2', new PromotionMustBeCheaper],
            'is_active' => 'boolean',
        ];
    }
}
