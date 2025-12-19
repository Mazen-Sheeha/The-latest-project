<?php

namespace App\Http\Requests\BlockedNumber;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateBlockedNumberRequest extends FormRequest
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
            'phone' => ['required', 'string', 'unique:blocked_numbers,phone']
        ];
    }

    /**
     * 
     * @return messages[]
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'لابد من وجود رقم الهاتف',
            'phone.string' => 'هذا الرقم غير صالح',
            'phone.unqiue' => 'هذا الرقم محظور بالفعل'
        ];
    }
}
