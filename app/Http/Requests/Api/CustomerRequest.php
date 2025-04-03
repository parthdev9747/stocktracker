<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;

class CustomerRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
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
