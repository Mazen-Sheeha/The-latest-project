<?php

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows("access-domains");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'domain' => ['required', 'string', 'regex:/^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}(?:\.[a-zA-Z]{2,})?$/', 'unique:domains,domain'],
            'status' => ['required', 'in:active,inactive'],
            'verification_ip' => ['nullable', 'ip'],
            'setup_type' => ['required', 'in:wildcard,dns_record'],
            'dns_record' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'domain.required' => 'الدومين مطلوب',
            'domain.regex' => 'صيغة الدومين غير صحيحة',
            'domain.unique' => 'هذا الدومين موجود بالفعل',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة يجب أن تكون نشطة أو معطلة',
            'verification_ip.ip' => 'صيغة الـ IP غير صحيحة',
            'setup_type.required' => 'نوع الإعداد مطلوب',
            'setup_type.in' => 'نوع الإعداد غير صحيح'
        ];
    }
}
