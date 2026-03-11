<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pageId = $this->route('page')->id;

        return [
            // ================= BASIC INFO =================
            'name' => ['sometimes', 'string', 'max:255', "unique:pages,name,{$pageId}"],
            'slug' => ['sometimes', 'string', 'max:255', "unique:pages,slug,{$pageId}"],
            'title' => ['sometimes', 'string', 'max:255'],
            'domain_id' => ['sometimes', 'exists:domains,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'theme_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'product_id' => ['nullable', 'exists:products,id'],

            // ================= SALE =================
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:original_price'],
            'sale_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sale_ends_at' => ['nullable', 'date'],

            // ================= STATS =================
            'items_sold_count' => ['nullable', 'integer', 'min:0'],
            'reviews_count' => ['nullable', 'integer', 'min:0'],
            'stock_count' => ['nullable', 'integer', 'min:0'],

            // ================= IMAGES =================
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,avif,jfif,bmp,tiff,tif', 'max:5120'],
            'images_order' => ['nullable', 'string'],

            // ================= REVIEWS =================
            'reviews' => ['nullable', 'array'],
            'reviews.*.id' => ['nullable', 'integer', 'exists:page_reviews,id'],
            'reviews.*._delete' => ['nullable', 'boolean'],
            'reviews.*.reviewer_name' => ['required_without:reviews.*._delete', 'string', 'max:255'],
            'reviews.*.comment' => ['required_without:reviews.*._delete', 'string'],
            'reviews.*.stars' => ['required_without:reviews.*._delete', 'integer', 'min:1', 'max:5'],
            'reviews.*.reviewer_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,avif,jfif,bmp,tiff,tif', 'max:2048'],

            // ================= OFFERS =================
            'offers' => ['nullable', 'array'],
            'offers.*.quantity' => ['nullable', 'integer', 'min:1'],
            'offers.*.price' => ['nullable', 'numeric', 'min:0'],
            'offers.*.label' => ['nullable', 'string', 'max:255'],
            'offers.*.image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,avif,jfif,bmp,tiff,tif', 'max:5120'],
            'offers.*.selected' => ['nullable', 'boolean'],
            'offers.*.sentence' => ['nullable', 'string', 'max:500'],

            // ================= UPSELL PRODUCTS =================
            'upsell_products' => ['nullable', 'array'],
            'upsell_products.*.product_id' => ['required', 'exists:products,id'],
            'upsell_products.*.name' => ['required', 'string', 'max:255'],
            'upsell_products.*.price' => ['required', 'numeric', 'min:0'],
            'upsell_products.*.image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,avif,jfif,bmp,tiff,tif', 'max:5120'],

            // ================= TEXT =================
            'moving_banner_text' => ['nullable', 'array'],
            'moving_banner_text.*' => ['nullable', 'string', 'max:255'],
            'top_feature_text' => ['nullable', 'array'],
            'top_feature_text.*' => ['nullable', 'string', 'max:255'],

            // ================= MISC =================
            'features_active' => ['nullable', 'array'],
            'features_active.*' => ['string'],
            'features_labels' => ['nullable', 'array'],
            'features_labels.*' => ['nullable', 'string', 'max:255'],

            'whatsapp_phone' => ['nullable', 'string', 'max:32'],
            'whatsapp_label' => ['nullable', 'string', 'max:255'],

            // ================= PIXELS =================
            'pixels' => ['nullable', 'array'],
            'pixels.*' => ['integer', 'exists:pixels,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'اسم الصفحة مستخدم مسبقاً',
            'slug.unique' => 'عنوان الصفحة مستخدم مسبقاً',
            'theme_color.regex' => 'لون الصفحة يجب أن يكون بصيغة HEX صحيحة',
            'sale_price.lte' => 'سعر البيع يجب أن يكون أقل من أو يساوي السعر الأصلي',
            'images.*.mimes' => 'صيغة الصورة غير مدعومة',
            'images.*.max' => 'حجم الصورة يجب أن لا يتجاوز 5MB',
            'pixels.*.exists' => 'أحد البكسلات المختارة غير موجود',
        ];
    }
}
