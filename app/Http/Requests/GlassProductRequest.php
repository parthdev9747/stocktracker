<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GlassProductRequest extends FormRequest
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
            'name' => 'required|string|max:255||unique:glass_products,name,' . ($this->glass_product ? $this->glass_product->id : ''),
            'category_id' => 'required|numeric',
            'brand_id' => 'nullable|numeric',
            'tax_id' => 'nullable|numeric',
        ];
    }
}
