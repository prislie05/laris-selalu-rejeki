<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCheckoutRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:cash,qris,transfer'],
            'product_id' => ['nullable', 'exists:products,id'], // If buy-now directly
            'quantity' => ['nullable', 'integer', 'min:1'],      // If buy-now directly
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
