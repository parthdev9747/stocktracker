<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CaseCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:case_categories,name,' . ($this->case_category ? $this->case_category->id : ''),
            'p_id' => 'nullable|exists:case_categories,id',
            'sequence' => 'nullable|integer|min:0'
        ];

        // Prevent circular reference when updating
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $caseCategory = $this->route('caseCategory');
            if ($caseCategory) {
                $rules['p_id'] .= '|not_in:' . $this->route('caseCategory')->id;
            }
        }

        return $rules;
    }
}
