<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:20', 'string', 'unique:users,name'],
            'email' => ['required', 'email:filter', 'regex:/@admin\.com$/', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
            'per_ids' => ['array', 'required'],
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

            'per_ids.required' => "لابد من وجود صلاحية واحدة على الأقل"
        ];
    }
}
