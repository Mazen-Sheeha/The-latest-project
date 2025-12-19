<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', Auth::user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route("admin");
        return [
            'name' => ['required', 'min:3', 'max:20', 'string', 'unique:users,name,' . $id . ',id'],
            'email' => ['required', 'email', 'regex:/@admin\.com$/', 'unique:users,email,' . $id . ',id'],
            'password' => ['nullable', 'string', 'min:6', 'max:30', 'confirmed'],
            'per_ids' => ['nullable', 'array']
        ];
    }

    /**
     * @return messages
     */
    public function messages()
    {
        return [
            'name.required' => 'اسم المدير مطلوب',
            'name.string' => "اسم المدير غير صالح",
            'name.min' => "اسم المدير يجب أن يكون 3 حروف على الأقل",
            'name.max' => "اسم المدير يجب أن يكون 20 حرفا على الأكثر",
            "name.unique" => "هذا الاسم موجود من قبل",

            'email.required' => "البريد الإكتروني مطلوب",
            'email.email' => "البريد الإلكتروني ليس صالحا",
            'email.regex' => 'لابد أن يكون البريد الإلكتروني بهذا الشكل : admin@admin.com',
            'email.unique' => "هذا البريد الإلكتروني موجود بالفعل",

            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => "لابد أن تكون كلمة المرور مكونة من 6 حروف على الأقل",
            'password.confirmed' => "لابد من تأكيد كلمة المرور بشكل صحيح",
        ];
    }
}
