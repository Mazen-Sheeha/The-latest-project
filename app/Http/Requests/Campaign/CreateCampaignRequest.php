<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateCampaignRequest extends FormRequest
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
        return [
            'adset_id' => ['required', 'exists:adsets,id'],
            'campaign' => ['required', 'min:3', 'max:50', 'unique:campaigns,campaign'],
            'source' => ['required', 'min:2', 'max:50'],
            'url' => ['required', 'url']
        ];
    }

    /**
     * 
     * @return messages[]
     */
    public function messages(): array
    {
        return [
            'campaign.required' => 'لا بد من إضافة اسم الحملة',
            'campaign.min' => 'لا يمكن أن يقل اسم الحملة عن 3 حروف',
            'campaign.max' => 'لا يمكن أن يزيد اسم الحملة عن 50 حرفا',
            'campaign.unique' => 'هذا الاسم موجود من قبل',

            'source.required' => "لابد من إضافة مصدر الحملة",

            'url.required' => "لا بد من إضافة رابط المنتوج",
            'url.url' => "رابط المنتوج غير صالح"
        ];
    }
}
