<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:55'],
            'price' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'color_id' => ['required', 'integer', 'exists:colors,id'],
            'images' => 'required',
            'images.*' => ['required', 'image', 'mimes:jpeg,jpg,jfif,png', 'max:2048'],
            'categories' => 'required',
            'categories.*' => ['required', 'integer', 'exists:categories,id'],
            'sizes' => 'required',
            'sizes.*.amount' => ['required','integer','min:0'],
            'sizes.*.size_id' => ['required','integer','exists:sizes,id']
        ];
    }
}
