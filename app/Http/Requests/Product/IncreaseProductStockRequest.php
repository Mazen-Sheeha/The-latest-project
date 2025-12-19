<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IncreaseProductStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('access-products');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'numeric']
        ];
    }

    /**
     *
     * @return messages[] 
     */
    public function messages(): array
    {
        return [
            "quantity.required" => 'لابد من إدخال قيمة الزيادة',
            "quantity.numeric" => "لابد أن تكون الزيادة في المخزون رقم"
        ];
    }
}
