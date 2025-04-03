<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CustomerEyeInfoRequest extends BaseApiRequest
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

    // ... existing code ...

    public function rules()
    {
        return [
            'type' => 'required|in:us,old,doctor',
            'ipd' => 'nullable|numeric',
            'left_dist_sph' => 'required|numeric',
            'left_near_sph' => 'nullable|numeric',
            'left_add_sph' => 'nullable|numeric',
            'left_cl_sph' => 'nullable|numeric',
            'left_cyl' => 'required|numeric',
            'left_axis' => 'required|numeric|min:0|max:180',
            'right_dist_sph' => 'required|numeric',
            'right_near_sph' => 'nullable|numeric',
            'right_add_sph' => 'nullable|numeric',
            'right_cl_sph' => 'nullable|numeric',
            'right_cyl' => 'required|numeric',
            'right_axis' => 'required|numeric|min:0|max:180',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'left_dist_sph' => 'left eye distance SPH',
            'left_near_sph' => 'left eye near SPH',
            'left_add_sph' => 'left eye add SPH',
            'left_cl_sph' => 'left eye contact lens SPH',
            'left_cyl' => 'left eye cylinder',
            'left_axis' => 'left eye axis',
            'right_dist_sph' => 'right eye distance SPH',
            'right_near_sph' => 'right eye near SPH',
            'right_add_sph' => 'right eye add SPH',
            'right_cl_sph' => 'right eye contact lens SPH',
            'right_cyl' => 'right eye cylinder',
            'right_axis' => 'right eye axis',
            'ipd' => 'IPD',
            'type' => 'prescription type',
        ];
    }
}
