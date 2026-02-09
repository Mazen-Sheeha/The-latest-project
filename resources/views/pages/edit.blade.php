@extends('layouts.app')

@section('url_pages')
    <span>
        <a href="{{ route('home') }}" style="color:rgb(114,114,255)">لوحة التحكم</a>
    </span>
    <i class="ki-filled ki-left"></i>
    <span>
        <a href="{{ route(name: 'pages.index') }}" style="color:rgb(114,114,255)">صفحات البيع</a>
    </span>
    <i class="ki-filled ki-left"></i>
    <span>تعديل صفحة {{ $page->name }}</span>
@endsection

@section('content')
    <div class="space-y-6">

        <form action="{{ route('pages.update', $page) }}" method="POST" enctype="multipart/form-data" id="page-form">
            @csrf
            @method('PUT')

            {{-- ================= BASIC INFO ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">البيانات الأساسية</h3>
                </div>

                <div class="card-body p-6 space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">الاسم *</label>
                            <input name="name" class="input w-full" required value="{{ old('name', $page->name) }}">
                        </div>

                        <div>
                            <label class="form-label">عنوان الصفحة *</label>
                            <input name="slug" class="input w-full" required value="{{ old('slug', $page->slug) }}">
                        </div>

                        <div>
                            <label class="form-label">الموقع / الدومين *</label>
                            <select name="domain_id" class="input w-full" required>
                                <option value="">اختر الدومين</option>
                                @foreach ($domains as $domain)
                                    <option value="{{ $domain->id }}"
                                        {{ $page?->domain?->id == $domain->id ? 'selected' : '' }}>
                                        {{ $domain->domain }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">لون الصفحة</label>
                            <input type="color" name="theme_color" class="input w-24 h-10 p-0 border rounded"
                                value="{{ old('theme_color', $page->theme_color ?? '#0d6efd') }}">
                        </div>

                        <div>
                            <label class="form-label">المنتج</label>
                            <select name="product_id" class="input w-full">
                                <option value="">اختر المنتج</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $page->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">رقم واتساب (اختياري)</label>
                            <input name="whatsapp_phone" class="input w-full"
                                value="{{ old('whatsapp_phone', $page->whatsapp_phone) }}"
                                placeholder="مثال: +971501234567">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="input w-full" rows="12">{{ old('description', $page->description) }}</textarea>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                        نشر الصفحة
                    </label>
                </div>
            </div>

            {{-- ================= SALE ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">بيانات الخصم والعروض</h3>
                </div>

                <div class="card-body p-6 space-y-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="has-sale" {{ $page->sale_price ? 'checked' : '' }}>
                        تفعيل الخصم
                    </label>

                    <div id="sale-fields" class="{{ $page->sale_price ? '' : 'hidden' }} grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">السعر الأصلي</label>
                            <input type="number" step="0.01" name="original_price" class="input w-full"
                                value="{{ old('original_price', $page->original_price) }}">
                        </div>

                        <div>
                            <label class="form-label">سعر البيع</label>
                            <input type="number" step="0.01" name="sale_price" class="input w-full"
                                value="{{ old('sale_price', $page->sale_price) }}">
                        </div>

                        <div>
                            <label class="form-label">نسبة الخصم %</label>
                            <input type="number" name="sale_percent" class="input w-full"
                                value="{{ old('sale_percent', $page->sale_percent) }}">
                        </div>

                        <div>
                            <label class="form-label">انتهاء العرض</label>
                            <input type="datetime-local" name="sale_ends_at" class="input w-full"
                                value="{{ old('sale_ends_at', optional($page->sale_ends_at)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    {{-- Custom Offers --}}
                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between items-center mb-3">
                            <label class="form-label font-bold">عروض مخصصة</label>
                            <button type="button" class="btn btn-sm btn-primary" id="add-offer-btn">
                                + إضافة عرض
                            </button>
                        </div>

                        <p class="text-xs text-gray-600 mb-3">
                            أضف عروض بكميات مختلفة - مثلاً: اشتري 1 ب100 ، اشتري 2 ب180 (توفير)
                        </p>

                        <div id="offers-container" class="space-y-3">
                            @if ($page->offers && is_array($page->offers))
                                @foreach ($page->offers as $index => $offer)
                                    <div class="border rounded p-4 space-y-3 bg-gray-50">
                                        <div class="flex justify-between items-center mb-2">
                                            <strong>عرض #{{ $index + 1 }}</strong>
                                            <button type="button"
                                                class="text-red-600 remove-offer-btn text-sm font-bold">حذف</button>
                                        </div>

                                        <div class="grid md:grid-cols-2 gap-3">
                                            <div class="flex-1">
                                                <label class="form-label text-xs font-bold">الكمية</label>
                                                <input type="number" min="1"
                                                    name="offers[{{ $index }}][quantity]" class="input w-full"
                                                    value="{{ $offer['quantity'] }}" required>
                                            </div>
                                            <div class="flex-1">
                                                <label class="form-label text-xs font-bold">السعر</label>
                                                <input type="number" step="0.01"
                                                    name="offers[{{ $index }}][price]" class="input w-full"
                                                    value="{{ $offer['price'] }}" required>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label text-xs font-bold">الوصف (اختياري)</label>
                                            <input type="text" name="offers[{{ $index }}][label]"
                                                class="input w-full" value="{{ $offer['label'] ?? '' }}"
                                                placeholder="مثلاً: أفضل عرض">
                                        </div>

                                        <div>
                                            <label class="form-label text-xs font-bold">صورة العرض (اختياري)</label>
                                            <div class="flex gap-3">
                                                <input type="file" name="offers[{{ $index }}][image]"
                                                    class="input w-full offer-image-input" accept="image/*">
                                                <img class="offer-image-preview w-20 h-20 rounded border object-cover"
                                                    src="{{ isset($offer['image']) && $offer['image'] ? asset($offer['image']) : 'https://via.placeholder.com/80' }}"
                                                    alt="Offer Image">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= STATS ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">الإحصائيات</h3>
                </div>

                <div class="card-body p-6 grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">عدد المبيعات</label>
                        <input type="number" name="items_sold_count" class="input w-full"
                            value="{{ old('items_sold_count', $page->items_sold_count ?? 0) }}">
                    </div>

                    <div>
                        <label class="form-label">عدد التقييمات</label>
                        <input type="number" name="reviews_count" class="input w-full"
                            value="{{ old('reviews_count', $page->reviews_count ?? 0) }}">
                    </div>
                </div>
            </div>

            {{-- ================= FEATURES ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">المميزات</h3>
                </div>

                <div class="card-body p-6 grid md:grid-cols-2 gap-4">

                    <label class="flex items-center gap-3 border rounded p-3 cursor-pointer">
                        <input type="checkbox" name="features[]" value="cod"
                            {{ in_array('cod', $page->features ?? []) ? 'checked' : '' }}>
                        الدفع عند الاستلام
                    </label>

                    <label class="flex items-center gap-3 border rounded p-3 cursor-pointer">
                        <input type="checkbox" name="features[]" value="free_shipping"
                            {{ in_array('free_shipping', $page->features ?? []) ? 'checked' : '' }}>
                        شحن مجاني
                    </label>

                    <label class="flex items-center gap-3 border rounded p-3 cursor-pointer">
                        <input type="checkbox" name="features[]" value="replace"
                            {{ in_array('replace', $page->features ?? []) ? 'checked' : '' }}>
                        استبدال خلال 7 ايام
                    </label>

                    <label class="flex items-center gap-3 border rounded p-3 cursor-pointer">
                        <input type="checkbox" name="features[]" value="support"
                            {{ in_array('support', $page->features ?? []) ? 'checked' : '' }}>
                        خدمة 24/7
                    </label>

                    <label class="flex items-center gap-3 border rounded p-3 cursor-pointer">
                        <input type="checkbox" name="features[]" value="warranty"
                            {{ in_array('warranty', $page->features ?? []) ? 'checked' : '' }}>
                        ضمان سنة
                    </label>

                    <label class="flex items-center gap-3 border rounded p-3 cursor-pointer">
                        <input type="checkbox" name="features[]" value="same_day"
                            {{ in_array('same_day', $page->features ?? []) ? 'checked' : '' }}>
                        التوصيل نفس اليوم
                    </label>

                </div>
            </div>

            {{-- ================= IMAGES ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">الصور (يمكنك ترتيبها)</h3>
                </div>

                <p class="text-sm text-gray-700 p-4 block">
                    اسحب الصور لتغيير ترتيب العرض
                </p>

                <div class="card-body p-6 space-y-4">
                    <input type="file" name="images[]" multiple accept="image/*" id="images-input">

                    {{-- hidden input will store order --}}
                    <input type="hidden" name="images_order" id="images-order">

                    <div id="preview" class="flex flex-col gap-4 cursor-move">
                        @foreach ($page->images ?? [] as $index => $img)
                            <div class="flex items-center justify-between border rounded p-4 bg-gray-50 cursor-move"
                                data-index="{{ $index }}">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="order-badge w-8 h-8 flex items-center justify-center
                                    bg-blue-600 text-white text-sm font-bold rounded-full">
                                        {{ $loop->iteration }}
                                    </span>

                                    <img src="{{ asset($img) }}" class="w-20 h-20 object-cover rounded border">

                                    <span class="text-sm text-gray-600 truncate">
                                        {{ basename($img) }}
                                    </span>
                                </div>

                                <button type="button" onclick="deleteImage({{ $loop->index }})"
                                    class="btn btn-danger self-start">حذف</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ================= REVIEWS ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header flex justify-between items-center">
                    <h3 class="card-title">التقييمات</h3>
                    <button type="button" id="add-review" class="btn btn-sm btn-primary">
                        إضافة تقييم جديد
                    </button>
                </div>

                <div class="card-body p-6 space-y-4 flex flex-col gap-4" id="reviews-wrapper">

                    {{-- EXISTING REVIEWS --}}
                    @foreach ($page->reviews as $index => $review)
                        <div class="border rounded p-4 space-y-3 bg-gray-50 review-item flex flex-col gap-4">

                            <input type="hidden" name="reviews[{{ $index }}][id]" value="{{ $review->id }}">

                            <div class="flex justify-between items-center">
                                <strong>تقييم #{{ $loop->iteration }}</strong>

                                <label class="flex items-center gap-2 text-red-600 text-sm">
                                    <input type="checkbox" name="reviews[{{ $index }}][_delete]">
                                    حذف
                                </label>
                            </div>

                            {{-- Name --}}
                            <input type="text" name="reviews[{{ $index }}][reviewer_name]"
                                class="input w-full" value="{{ $review->reviewer_name }}" placeholder="اسم العميل">

                            {{-- Comment --}}
                            <textarea name="reviews[{{ $index }}][comment]" class="input w-full p-2" placeholder="التعليق">{{ $review->comment }}</textarea>

                            {{-- Stars --}}
                            <select name="reviews[{{ $index }}][stars]" class="input w-full">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ $review->stars == $i ? 'selected' : '' }}>
                                        {{ str_repeat('★', $i) }}
                                    </option>
                                @endfor
                            </select>

                            {{-- Image --}}
                            <div class="flex items-center gap-3">
                                @if ($review->reviewer_image)
                                    <img src="{{ asset($review->reviewer_image) }}"
                                        class="w-12 h-12 rounded-full border object-cover">
                                @endif

                                <input type="file" name="reviews[{{ $index }}][reviewer_image]"
                                    accept="image/*">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ================= UPSELL PRODUCTS ================= --}}
            @if ($products->count())
                <div class="card bg-white shadow rounded-lg">
                    <div class="card-header flex justify-between items-center">
                        <h3 class="card-title">منتجات إضافية (Upsell)</h3>
                        <button type="button" class="btn btn-sm btn-primary" id="add-upsell-product">
                            إضافة منتج
                        </button>
                    </div>

                    <div class="card-body p-6 space-y-4">

                        <p class="text-sm text-gray-600">
                            اختر المنتجات التي ستظهر بعد إتمام الطلب وقم بتخصيصها مع الصور والأسعار
                        </p>

                        {{-- Upsell Products Container --}}
                        <div id="upsell-products-container" class="space-y-4">
                            {{-- Products will be added here dynamically --}}
                            @foreach ($page->upsellProducts as $product)
                                <div class="border rounded-lg p-4 space-y-4 bg-gray-50 flex flex-col gap-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <strong>منتج: {{ $product->pivot->name ?? $product->name }}</strong>
                                        <button type="button"
                                            class="text-red-600 remove-upsell-product text-sm font-bold hover:text-red-800">حذف</button>
                                    </div>

                                    <div>
                                        <label class="form-label">اختر المنتج *</label>
                                        <select name="upsell_products[${upsellIndex}][product_id]"
                                            class="input w-full product-select" required>
                                            <option value="{{ $product->id }}" selected>{{ $product->name }}</option>
                                            @foreach ($products as $p)
                                                @if ($p->id !== $product->id)
                                                    <option value="{{ $p->id }}" data-name="{{ $p->name }}"
                                                        data-price="{{ $p->price }}"
                                                        data-image="{{ $p->image ? asset($p->image) : asset('images/productDefault.webp') }}">
                                                        {{ $p->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label">اسم المنتج *</label>
                                        <input type="text" name="upsell_products[${upsellIndex}][name]"
                                            class="input w-full product-name"
                                            value="{{ $product->pivot->name ?? $product->name }}" required>
                                    </div>

                                    <div>
                                        <label class="form-label">صورة المنتج</label>
                                        <div class="flex gap-3">
                                            <input type="file" name="upsell_products[${upsellIndex}][image]"
                                                class="input w-full product-image-file" accept="image/*">
                                            <img class="product-image-preview w-20 h-20 rounded border object-cover"
                                                src="{{ $product->pivot->image ? asset($product->pivot->image) : asset('images/productDefault.webp') }}"
                                                alt="Product Image">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label">سعر المنتج *</label>
                                        <input type="number" step="0.01"
                                            name="upsell_products[${upsellIndex}][price]"
                                            class="input w-full product-price"
                                            value="{{ $product->pivot->price ?? $product->price }}" required>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            @endif



            {{-- ================= ACTIONS ================= --}}
            <div class="flex gap-3 mt-2">
                <button class="btn btn-primary">تحديث</button>
                <a href="{{ route('pages.index') }}" class="btn btn-secondary">رجوع</a>
            </div>

        </form>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        const preview = document.getElementById('preview');
        const orderInput = document.getElementById('images-order');
        const input = document.getElementById('images-input');

        let filesList = [];

        // ================= NEW IMAGES =================
        input.addEventListener('change', e => {
            filesList = Array.from(e.target.files);
            preview.innerHTML = '';

            filesList.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = ev => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-4 border rounded p-2 bg-gray-50 cursor-move';
                    div.dataset.new = index;
                    div.file = file;

                    div.innerHTML = `
                    <span class="order-badge w-8 h-8 flex items-center justify-center bg-blue-600 text-white text-sm font-bold rounded-full">
                        ${index + 1}
                    </span>
                    <img src="${ev.target.result}" class="w-20 h-20 object-cover rounded border">
                    <span class="text-sm text-gray-600 truncate">${file.name}</span>
                `;

                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        // ================= SORTABLE =================
        new Sortable(preview, {
            animation: 150,
            onSort() {
                updateOrder();
            }
        });

        function updateOrder() {
            const order = [];

            [...preview.children].forEach((div, i) => {
                div.querySelector('.order-badge').innerText = i + 1;

                if (div.dataset.index !== undefined) {
                    // old image
                    order.push(div.dataset.index);
                }
            });

            orderInput.value = JSON.stringify(order);
        }

        updateOrder();

        // ================= SUBMIT =================
        document.getElementById('page-form').addEventListener('submit', () => {
            if (!filesList.length) return;

            const dt = new DataTransfer();
            filesList.forEach(file => dt.items.add(file));
            input.files = dt.files;
        });

        // ================= SALE TOGGLE =================
        document.getElementById('has-sale').addEventListener('change', e => {
            document.getElementById('sale-fields')
                .classList.toggle('hidden', !e.target.checked);
        });

        // ================= CUSTOM OFFERS =================
        let offerIndex = document.querySelectorAll('#offers-container > div').length;

        document.getElementById('add-offer-btn').addEventListener('click', () => {
            const container = document.getElementById('offers-container');
            const offerDiv = document.createElement('div');
            offerDiv.className = 'border rounded p-4 space-y-3 bg-gray-50';

            offerDiv.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <strong>عرض #${offerIndex + 1}</strong>
            <button type="button" class="text-red-600 remove-offer-btn text-sm font-bold">حذف</button>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div class="flex-1">
                <label class="form-label text-xs font-bold">الكمية *</label>
                <input type="number" min="1" name="offers[${offerIndex}][quantity]"
                       class="input w-full" placeholder="1" required>
            </div>
            <div class="flex-1">
                <label class="form-label text-xs font-bold">السعر *</label>
                <input type="number" step="0.01" name="offers[${offerIndex}][price]"
                       class="input w-full" placeholder="0.00" required>
            </div>
        </div>

        <div>
            <label class="form-label text-xs font-bold">الوصف (اختياري)</label>
            <input type="text" name="offers[${offerIndex}][label]"
                   class="input w-full" placeholder="مثلاً: أفضل عرض، وفر 20%">
        </div>

        <div>
            <label class="form-label text-xs font-bold">صورة العرض (اختياري)</label>
            <div class="flex gap-3">
                <input type="file" name="offers[${offerIndex}][image]"
                       class="input w-full offer-image-input" accept="image/*">
                <img class="offer-image-preview w-20 h-20 rounded border object-cover"
                     src="https://via.placeholder.com/80" alt="Offer Image">
            </div>
        </div>
    `;

            const removeBtn = offerDiv.querySelector('.remove-offer-btn');
            const imageInput = offerDiv.querySelector('.offer-image-input');
            const imagePreview = offerDiv.querySelector('.offer-image-preview');

            removeBtn.addEventListener('click', () => {
                offerDiv.remove();
            });

            imageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        imagePreview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            container.appendChild(offerDiv);
            offerIndex++;
        });

        // Handle existing offer image previews
        document.querySelectorAll('.offer-image-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        e.target.closest('.flex').querySelector('.offer-image-preview').src = event
                            .target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Remove existing offers
        document.querySelectorAll('.remove-offer-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.border').remove();
            });
        });
    </script>

    <script>
        function deleteImage(index) {
            if (!confirm('هل أنت متأكد من حذف هذه الصورة؟')) return;

            fetch("{{ route('pages.image.delete', $page->id) }}", {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        index
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        alert(res.message);
                        location.reload(); // or remove the image element dynamically
                    } else {
                        alert(res.message);
                    }
                });
        }
    </script>

    <script>
        let reviewIndex = {{ $page->reviews->count() }};

        document.getElementById('add-review').addEventListener('click', () => {
            const wrapper = document.getElementById('reviews-wrapper');

            const div = document.createElement('div');
            div.className = 'border rounded p-4 space-y-3 bg-gray-50 review-item flex flex-col gap-4';

            div.innerHTML = `
        <div class="flex justify-between items-center">
            <strong>تقييم جديد</strong>
            <button type="button" class="text-red-600 remove-review">حذف</button>
        </div>

        <input type="text"
               name="reviews[${reviewIndex}][reviewer_name]"
               class="input w-full"
               placeholder="اسم العميل" required>

        <textarea
            name="reviews[${reviewIndex}][comment]"
            class="input w-full p-2"
            placeholder="التعليق" required></textarea>

        <select name="reviews[${reviewIndex}][stars]" class="input w-full" required>
            <option value="">عدد النجوم</option>
            <option value="5">★★★★★</option>
            <option value="4">★★★★</option>
            <option value="3">★★★</option>
            <option value="2">★★</option>
            <option value="1">★</option>
        </select>

        <input type="file"
               name="reviews[${reviewIndex}][reviewer_image]"
               accept="image/*">
    `;

            div.querySelector('.remove-review').onclick = () => div.remove();

            wrapper.appendChild(div);
            reviewIndex++;
        });
    </script>

    <script>
        let upsellIndex = {{ $page->upsellProducts->count() ?? 0 }};
        const allProducts = @json($products);

        document.getElementById('add-upsell-product').addEventListener('click', () => {
            const container = document.getElementById('upsell-products-container');

            const div = document.createElement('div');
            div.className = 'border rounded-lg p-4 space-y-4 bg-gray-50 flex flex-col gap-4';
            div.dataset.index = upsellIndex;

            div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <strong>منتج إضافي #${upsellIndex + 1}</strong>
            <button type="button" class="text-red-600 remove-upsell-product text-sm font-bold hover:text-red-800">حذف</button>
        </div>

        <div>
            <label class="form-label">اختر المنتج *</label>
            <select name="upsell_products[${upsellIndex}][product_id]" class="input w-full product-select" required>
                <option value="">اختر المنتج</option>
                ${allProducts.map(p => `
                                <option value="${p.id}"
                                        data-name="${p.name}"
                                        data-price="${p.price}"
                                        data-image="${p.image ? '{{ asset('') }}' + p.image : '{{ asset('images/productDefault.webp') }}'}">
                                    ${p.name}
                                </option>
                            `).join('')}
            </select>
        </div>

        <div>
            <label class="form-label">اسم المنتج *</label>
            <input type="text" name="upsell_products[${upsellIndex}][name]" class="input w-full product-name" placeholder="سيتم ملؤه تلقائياً" required>
        </div>

        <div>
            <label class="form-label">صورة المنتج</label>
            <div class="flex gap-3">
                <input type="file" name="upsell_products[${upsellIndex}][image]" class="input w-full product-image-file" accept="image/*">
                <img class="product-image-preview w-20 h-20 rounded border object-cover" src="https://via.placeholder.com/80" alt="Product Image">
            </div>
        </div>

        <div>
            <label class="form-label">سعر المنتج *</label>
            <input type="number" step="0.01" name="upsell_products[${upsellIndex}][price]" class="input w-full product-price" placeholder="0.00" required>
        </div>
    `;

            container.appendChild(div);

            // Setup event listeners
            const productSelect = div.querySelector('.product-select');
            const productName = div.querySelector('.product-name');
            const productPrice = div.querySelector('.product-price');
            const productImageFile = div.querySelector('.product-image-file');
            const productImagePreview = div.querySelector('.product-image-preview');

            productSelect.addEventListener('change', (e) => {
                const option = e.target.selectedOptions[0];
                if (option.value) {
                    productName.value = option.dataset.name;
                    productPrice.value = option.dataset.price;
                    productImagePreview.src = option.dataset.image;
                }
            });

            productImageFile.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        productImagePreview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            div.querySelector('.remove-upsell-product').addEventListener('click', () => {
                div.remove();
            });

            upsellIndex++;
        });

        // Setup event listeners for existing upsell products
        document.querySelectorAll('#upsell-products-container .product-select').forEach(select => {
            select.addEventListener('change', (e) => {
                const container = e.target.closest('.border');
                const option = e.target.selectedOptions[0];
                if (option.value) {
                    container.querySelector('.product-name').value = option.dataset.name;
                    container.querySelector('.product-price').value = option.dataset.price;
                    container.querySelector('.product-image-preview').src = option.dataset.image;
                }
            });
        });

        document.querySelectorAll('#upsell-products-container .product-image-file').forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        e.target.closest('.border').querySelector('.product-image-preview').src = event
                            .target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        document.querySelectorAll('#upsell-products-container .remove-upsell-product').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.border').remove();
            });
        });
    </script>
@endsection
