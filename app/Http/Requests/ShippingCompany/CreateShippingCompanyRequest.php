<?php

namespace App\Http\Requests\ShippingCompany;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateShippingCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows("access-shipping-companies");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:50', 'string', 'unique:shipping_companies,name'],
            'price' => ['required', 'numeric']
        ];
    }

    /**
     * 
     * @return messages[]
     */
    public function messages(): array
    {
        return [
            'name.required' => "لابد من إضافة اسم شركة الشحن",
            'name.unique' => "هذا الاسم موجود من قبل",
            'name.string' => "هذا الاسم غير صالح",
            'name.min' => 'لابد أن يكون اسم الشركة على الأقل 3 أحرف',
            'name.max' => "لابد أن سيكون اسم الشركة على الأكثر 50 حرف",

            'price.numeric' => 'لابد أن يكون السعر رقم',
            'price.required' => "لابد من إضافة سعر شركة الشحن"
        ];
    }
}
