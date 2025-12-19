<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3'],
            'price' => ['required', 'numeric'],
            'code' => ['required', 'string', 'unique:products,code'],
            'shipping_company_id' => ['required', 'exists:shipping_companies,id'],
            'stock' => ['numeric', 'required'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }

    /**
     * 
     * @return messages[]
     */
    public function messages(): array
    {
        return [
            'name.required' => 'لابد من إضافة اسم المنتوج',
            'name.string' => 'هذا الاسم غير صالح للاستخدام',
            'name.min' => 'لابد أن يكون اسم المنتوج 3 حروف على الأقل',

            'price.required' => 'لابد من إضافة سعر المنتوج',
            'price.numeric' => 'لابد من أن يكون سعر المنتوج رقم',

            'code.required' => 'لابد من إضافة كود المنتوج',
            'code.string' => 'هذا الكود غير صالح',
            'code.unique' => 'هذا الكود موجود من قبل',

            'shipping_company_id.required' => 'لابد من إضافة شركة الشحن',
            'shipping_company_id.exists' => 'هذه الشركة غير موجودة',

            'stock.required' => 'لابد من إضافة كمية المنتوج',
            'stock.numeric' => 'لابد من أن يكون كمية المنتوج رقم',

            'image.required' => 'لابد من إضافة صورة للمنتوج',
            'image.image' => 'من فضلك أضف صورة',
            'image.mimes' => "الامتدادات المتاحة : jpeg,png,jpg,gif,svg,webp"
        ];
    }
}
