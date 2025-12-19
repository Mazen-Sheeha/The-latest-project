<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateWebsiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows("access-websites");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'unique:websites,key'],
            'domain' => ['required', 'string', 'regex:/^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}(?:\.[a-zA-Z]{2,})?$/', 'unique:websites,domain']
        ];
    }

    /**
     * 
     * @return messages[]
     */
    public function messages(): array
    {
        return [
            'key.required' => "لابد من إدخال ال key الخاص بالدومين",
            'key.unique' => "هذا ال key موجود من قبل",
            'domain.required' => "لابد من إدخال الدومين",
            'domain.regex' => "هذا الدومين غير صالح",
            'domain.unique' => "هذا الدومين موجود من قبل"
        ];
    }
}
