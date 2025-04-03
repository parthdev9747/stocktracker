<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;

class AppUserRequest extends BaseApiRequest
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
            'code' => 'required|string|max:6',
            'device_name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'os' => 'required|integer|in:1,2',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    // public function messages()
    // {
    //     return [
    //         'device_name.required' => 'Device name is required',
    //         'device_id.required' => 'Device ID is required',
    //         'model_name.required' => 'Model name is required',
    //         'os.required' => 'Operating system is required',
    //         'os.in' => 'Operating system must be either Android (1) or iOS (2)',
    //     ];
    // }
}
