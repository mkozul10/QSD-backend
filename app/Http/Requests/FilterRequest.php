<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
            'min_price' => ['numeric', 'min:0', 'max:5000'],
            'max_price' => ['numeric', "min:0", 'max:5000'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'brands.*' => ['integer', 'exists:brands,id'],
            'colors.*' => ['integer', 'exists:colors,id'],
            'sizes.*' => ['integer', 'exists:sizes,id'],
            'genders.*' => ['string', 'in:woman,man,children']
        ];
    }
}
