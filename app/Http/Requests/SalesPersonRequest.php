<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesPersonRequest extends FormRequest
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
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:sales_people,name,' . ($this->sales_person ? $this->sales_person->id : ''),
            'initial' => 'required|string|max:10|unique:sales_people,initial,' . ($this->sales_person ? $this->sales_person->id : ''),
        ];

        return $rules;
    }
}
