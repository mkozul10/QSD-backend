<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
            'payment_method' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'total_price' => ['required', 'integer', 'min:0'],
            'products' => ['required', 'array'],
            'products.*.products_id' => ['required', 'min:1', 'exists:products,id'],
            'products.*.sizes_id' => ['required', 'min:1', 'exists:sizes,id'],
            'products.*.quantity' => ['required', 'min:1'],
            'guest_email' => ['email',Rule::unique('users', 'email')],
        ];
    }
}
