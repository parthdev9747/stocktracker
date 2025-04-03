<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GlassCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:glass_categories,name,' . ($this->glass_category ? $this->glass_category->id : ''),
            'p_id' => 'nullable|exists:glass_categories,id',
            'sequence' => 'nullable|integer|min:0'
        ];

        // Prevent circular reference when updating
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $glassCategory = $this->route('glassCategory');
            if ($glassCategory) {
                $rules['p_id'] .= '|not_in:' . $this->route('glassCategory')->id;
            }
        }

        return $rules;
    }
}
