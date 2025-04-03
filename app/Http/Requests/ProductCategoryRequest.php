<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255|unique:product_categories,name,' . ($this->product_category ? $this->product_category->id : ''),
            'prefix' => 'required|string|max:255|unique:product_categories,prefix,' . ($this->product_category ? $this->product_category->id : ''),
            'p_id' => 'nullable|exists:product_categories,id',
            'sequence' => 'nullable|integer|min:0'
        ];

        // Prevent circular reference when updating
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $productCategory = $this->route('productCategory');
            if ($productCategory) {
                $rules['p_id'] .= '|not_in:' . $productCategory->id;
            }
        }

        return $rules;
    }
}
