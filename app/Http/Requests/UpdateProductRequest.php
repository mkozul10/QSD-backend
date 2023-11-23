<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'id' => ['integer', 'min:1', 'exists:products,id'],
            'name' => ['required', 'string', 'max:55', 'unique:products,name'],
            'price' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'gender' => ['required', 'string', 'in:woman,man,children'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'color_id' => ['required', 'integer', 'exists:colors,id'],
            'images' => ['array', 'max:5'],
            'categories' => ['required','array'],
            'categories.*' => ['required', 'integer', 'exists:categories,id'],
            'sizes' => ['required','array'],
            'sizes.*.amount' => ['required','integer','min:0'],
            'sizes.*.size_id' => ['required','integer','exists:sizes,id']
        ];
    }
}
