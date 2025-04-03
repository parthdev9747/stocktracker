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
            'name' => 'required|string|max:255|unique:products,name,' . ($this->product ? $this->product->id : ''),
            'print_name' => 'required|string|max:255|unique:products,print_name,' . ($this->product ? $this->product->id : ''),
            'barcode' => 'required|string|max:255|unique:products,barcode,' . ($this->product ? $this->product->id : ''),
            'tax_id' => 'nullable|exists:taxes,id',
            'category_id' => 'required|numeric',
            'brand_id' => 'nullable|exists:brands,id',
            'rack_id' => 'required|exists:racks,id',
            'hsn_code' => 'nullable|string|max:255',
            'stock' => 'required|numeric|min:0',
            'stock_required' => 'required|in:yes,no',
            'minimum_stock' => 'required_if:stock_required,yes|numeric|min:' . ($this->stock_required == 'yes' ? '1' : '0'),
            'sales_rate' => 'required|numeric|min:0',
            'purchase_rate' => 'nullable|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
        ];
    }
}
