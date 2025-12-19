<?php

namespace App\Http\Requests\Adset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAdsetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows("access-ads");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('adset');
        return [
            'name' => ['required', 'min:3', 'max:50', 'unique:adsets,name,' . $id . ',id']
        ];
    }

    /**
     * 
     * @return messages[]
     */
    public function messages(): array
    {
        return [
            'name.required' => "لابد من إضافة اسم المجلد",
            'name.min' => "لابد أن يكون اسم المجلد 3 أحرف على الأقل",
            'name.max' => "لا بد أن يكون اسم المجلد 50 حرفا على الأكثر",
            'name.unique' => "هذا المجلد موجود من قبل",
        ];
    }
}
