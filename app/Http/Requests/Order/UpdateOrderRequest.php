<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('access-orders');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'phone' => ['required', 'string'],
            'city' => ['required', 'string', 'min:3'],
            'address' => ['required', 'string', 'min:5'],
            'tracking_number' => ['nullable', 'string'],
            'shipping_price' => ['required', 'numeric'],
            'url' => ['nullable', 'url'],
            'product_ids' => ['array', 'required'],
            'product_ids.*' => ['exists:products,id'],
            'prices' => ['required', 'array'],
            'prices.*' => ['numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['numeric', 'min:1'],
            'campaign_id' => ['nullable', 'exists:campaigns,id']
        ];
    }

    /**
     * 
     * @return messages[]
     */
    public function messages(): array
    {
        return [
            'name.required' => 'لابد من إدخال اسم المشتري',
            'name.string' => 'هذا الاسم غير صالح',
            'name.min' => 'لابد أن يكون اسم المشتري أكبر من 3 حروف',
            'name.max' => 'لابد أن يكون اسم المشتري أقل من 50 حرف',

            'phone.required' => 'لابد من إدخال رقم هاتف المشتري',
            'phone.string' => 'هذا الرقم غير صالح',

            'city.required' => 'لابد من إدخال المدينة',
            'city.string' => 'اسم المدينة غير صالح',
            'city.min' => 'لابد أن يكون اسم المدينة أكبر من 3 حروف',

            'address.required' => 'لابد من إدخال العنوان',
            'address.string' => 'هذا العنوان غير صالح',
            'address.min' => 'لابد أن يكون العنوان أكبر من 5 حروف',

            'tracking_number.string' => 'رمز التتبع غير صالح',

            'shipping_price.required' => 'لابد من وجود سعر لشركة الشحن',

            'url.url' => 'رابط الطلب غير صالح',

            'product_ids.required' => 'لابد من إضافة منتوج',

            'product_ids.*.exists' => 'هناك منتوج غير موجود',

            'prices.required' => 'يجب تحديد السعر لكل منتوج',
            'prices.*.numeric' => 'السعر يجب أن يكون رقمًا',
            'prices.*.min' => 'السعر لا يمكن أن يكون أقل من 0',

            'quantities.required' => 'يجب تحديد الكمية لكل منتوج',
            'quantities.*.numeric' => 'الكمية يجب أن تكون رقمًا',
            'quantities.*.min' => 'الكمية يجب أن تكون على الأقل 1',
        ];
    }
}
