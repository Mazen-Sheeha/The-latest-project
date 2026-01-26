<?php

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateDomainRequest extends FormRequest
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
        $domainId = $this->route('domain');

        return [
            'domain' => ['required', 'string', 'regex:/^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}(?:\.[a-zA-Z]{2,})?$/', 'unique:domains,domain,' . $domainId],
            'status' => ['required', 'in:active,inactive'],
            'verification_ip' => ['nullable', 'ip']
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
            'verification_ip.ip' => 'صيغة الـ IP غير صحيحة'
        ];
    }
}
