<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'other_number' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:male,female,other',
            'birthdate' => 'nullable|date|before_or_equal:today',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ];
    }
}
