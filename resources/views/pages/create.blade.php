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
    <span>انشاء صفحة</span>
@endsection

@php
    use App\Models\Pixel;
@endphp

@section('content')
    <div class="space-y-6">

        <form action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data" id="page-form">
            @csrf
            {{-- ================= BASIC INFO ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">البيانات الأساسية</h3>
                </div>

                <div class="card-body p-6 space-y-4">
                    <div class="grid md:grid-cols-2 gap-4 mb-2">
                        <div>
                            <label class="form-label">الاسم *</label>
                            <input name="name" class="input" required>
                        </div>

                        <div>
                            <label class="form-label">عنوان الصفحة *</label>
                            <input name="slug" class="input" required>
                        </div>

                        <div>
                            <label class="form-label">عنوان صفحة البيع (header)</label>
                            <input name="title" class="input" required>
                        </div>

                        <div>
                            <label class="form-label">الموقع / الدومين *</label>
                            <select name="domain_id" class="input w-full" required>
                                <option value="">اختر الدومين</option>
                                @foreach ($domains as $domain)
                                    <option value="{{ $domain->id }}">
                                        {{ $domain->domain }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">المنتج *</label>
                            <select name="product_id" class="input w-full" id="product-select">
                                <option value="">اختر المنتج</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-image="{{ $product->image ? asset($product->image) : asset('images/productDefault.webp') }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div>
                            <label class="form-label">لون الصفحة *</label>
                            <input type="color" name="theme_color" class="input w-24 h-10 p-0 border rounded"
                                value="#0d6efd">
                        </div>

                        <div>
                            <label class="form-label">رقم واتساب (اختياري)</label>
                            <input name="whatsapp_phone" class="input w-full" value="{{ old('whatsapp_phone') }}"
                                placeholder="مثال: +971501234567">
                        </div>

                        <div>
                            <label class="form-label">السعر الأصلي</label>
                            <input type="number" step="0.01" name="original_price" id="original_price"
                                class="input w-full">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">الوصف *</label>
                        <textarea name="description" class="input w-full" rows="12"></textarea>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked>
                        نشر الصفحة
                    </label>

                    {{-- Pixels Section --}}
                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="form-label font-bold mb-0">بكسلات التتبع (Tracking Pixels)</h4>
                            <a href="{{ route('pixels.create') }}" class="btn btn-sm btn-light" target="_blank">
                                + إضافة بكسل جديد
                            </a>
                        </div>

                        @if ($pixels->count() > 0)
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach ($pixels as $pixel)
                                    <label
                                        class="border p-4 rounded cursor-pointer hover:bg-gray-50 flex items-start gap-3">
                                        <input type="checkbox" name="pixels[]" value="{{ $pixel->id }}"
                                            {{ is_array(old('pixels')) && in_array($pixel->id, old('pixels')) ? 'checked' : '' }}
                                            class="mt-1">
                                        <div>
                                            <div class="font-semibold">{{ $pixel->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ Pixel::getTypes()[$pixel->type] ?? $pixel->type }} -
                                                {{ $pixel->pixel_id }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p>لا توجد بكسلات مضافة. </p>
                                <a href="{{ route('pixels.create') }}" class="text-blue-600 hover:underline"
                                    target="_blank">
                                    أضف بكسل جديد
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- ================= SALE ================= --}}
                    <div class="card bg-white shadow rounded-lg">
                        <div class="card-header">
                            <h3 class="card-title">بيانات الخصم والعروض</h3>
                        </div>

                        <div class="card-body p-6 space-y-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="has-sale">
                                تفعيل الخصم
                            </label>

                            <div id="sale-fields" class="hidden grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">سعر البيع</label>
                                    <input type="number" step="0.01" name="sale_price" id="sale_price"
                                        class="input w-full">
                                </div>

                                <div>
                                    <label class="form-label">نسبة الخصم %</label>
                                    <input type="number" name="sale_percent" id="sale_percent" class="input w-full">
                                </div>

                                <div>
                                    <label class="form-label">انتهاء العرض</label>
                                    <input type="date" name="sale_ends_at" class="input w-full">
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
                                    {{-- Offers will be added here --}}
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
                                <input type="number" name="items_sold_count" class="input w-full" value="0">
                            </div>

                            <div>
                                <label class="form-label">عدد التقييمات</label>
                                <input type="number" name="reviews_count" class="input w-full" value="0">
                            </div>
                        </div>
                    </div>

                    {{-- ================= FEATURES ================= --}}
                    <div class="card">
                        <div class="card-header">
                            <h3>المميزات</h3>
                        </div>

                        <div class="p-4 grid grid-cols-2 gap-3">

                            <label class="border p-3 rounded cursor-pointer">
                                <input type="checkbox" name="features[]" value="cod">
                                الدفع عند الاستلام
                            </label>

                            <label class="border p-3 rounded cursor-pointer">
                                <input type="checkbox" name="features[]" value="free_shipping">
                                شحن مجاني
                            </label>

                            <label class="border p-3 rounded cursor-pointer">
                                <input type="checkbox" name="features[]" value="replace">
                                استبدال خلال 7 ايام
                            </label>

                            <label class="border p-3 rounded cursor-pointer">
                                <input type="checkbox" name="features[]" value="support">
                                خدمة 24/7
                            </label>

                            <label class="border p-3 rounded cursor-pointer">
                                <input type="checkbox" name="features[]" value="warranty">
                                ضمان سنة
                            </label>

                            <label class="border p-3 rounded cursor-pointer">
                                <input type="checkbox" name="features[]" value="same_day">
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
                            </div>
                        </div>
                    </div>

                    {{-- ================= REVIEWS ================= --}}
                    <div class="card bg-white shadow rounded-lg">
                        <div class="card-header flex justify-between items-center">
                            <h3 class="card-title">التقييمات</h3>
                            <button type="button" class="btn btn-sm btn-primary" id="add-review">
                                إضافة تقييم
                            </button>
                        </div>

                        <div class="card-body p-6 space-y-4" id="reviews-wrapper"></div>
                    </div>

                    {{-- ================= UPSELL PRODUCTS ================= --}}
                    <div class="card bg-white shadow rounded-lg">
                        <div class="card-header flex justify-between items-center">
                            <h3 class="card-title">منتجات إضافية (Upsell)</h3>
                            <button type="button" class="btn btn-sm btn-primary" id="add-upsell-product">
                                إضافة منتج
                            </button>
                        </div>

                        <div class="card-body p-6 space-y-4">

                            <p class="text-sm text-gray-600">
                                اختر المنتجات التي ستظهر بعد إتمام الطلب وقم بتخصيصها مع الصور والأسعار والعروض
                            </p>

                            {{-- Upsell Products Container --}}
                            <div id="upsell-products-container" class="space-y-4">
                                {{-- Products will be added here dynamically --}}
                            </div>

                        </div>
                    </div>


                    {{-- ================= ACTIONS ================= --}}
                    <div class="flex gap-3">
                        <button class="btn btn-primary">حفظ</button>
                        <a href="{{ route('pages.index') }}" class="btn btn-secondary">رجوع</a>
                    </div>

        </form>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        let filesList = [];

        const input = document.getElementById('images-input');
        const preview = document.getElementById('preview');

        input.addEventListener('change', e => {
            filesList = Array.from(e.target.files);
            renderPreview();
        });

        function renderPreview() {
            preview.innerHTML = '';

            filesList.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = `
                flex items-center gap-4
                border rounded p-2 bg-gray-50
                cursor-move
            `;
                    div.file = file;

                    div.innerHTML = `
                <!-- ORDER -->
                <span class="w-8 h-8 flex items-center justify-center
                             bg-blue-600 text-white text-sm font-bold rounded-full">
                    ${index + 1}
                </span>

                <!-- IMAGE -->
                <img src="${e.target.result}"
                     class="w-20 h-20 object-cover rounded border">

                <!-- NAME (optional but useful) -->
                <span class="text-sm text-gray-600 truncate">
                    ${file.name}
                </span>
            `;

                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }


        new Sortable(preview, {
            animation: 150,
            onEnd() {
                filesList = [...preview.children].map(div => div.file);
                renderPreview();
            }
        });

        document.getElementById('page-form').addEventListener('submit', () => {
            const dataTransfer = new DataTransfer();
            filesList.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        });

        let order = [];

        filesList.forEach((file, index) => {
            order.push(index);
        });

        document.getElementById('images-order').value = JSON.stringify(order);
    </script>

    <script>
        document.getElementById('has-sale').addEventListener('change', e => {
            document.getElementById('sale-fields')
                .classList.toggle('hidden', !e.target.checked);
        });
    </script>

    <script>
        const originalPrice = document.getElementById('original_price');
        const salePrice = document.getElementById('sale_price');
        const salePercent = document.getElementById('sale_percent');

        function calculateDiscount() {
            const original = parseFloat(originalPrice.value);
            const sale = parseFloat(salePrice.value);

            if (!original || !sale || sale >= original) {
                salePercent.value = '';
                return;
            }

            const percent = ((original - sale) / original) * 100;
            salePercent.value = percent.toFixed(0);
        }

        originalPrice.addEventListener('input', calculateDiscount);
        salePrice.addEventListener('input', calculateDiscount);
    </script>

    <script>
        let offerIndex = 0;

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

            offerDiv.querySelector('.remove-offer-btn').addEventListener('click', () => {
                offerDiv.remove();
            });

            const imageInput = offerDiv.querySelector('.offer-image-input');
            const imagePreview = offerDiv.querySelector('.offer-image-preview');

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
    </script>

    <script>
        document.getElementById('add-review').addEventListener('click', () => {
            const wrapper = document.getElementById('reviews-wrapper');

            const div = document.createElement('div');
            div.className = 'border rounded p-4 space-y-3 bg-gray-50 flex flex-col gap-4';

            div.innerHTML = `
        <div class="flex justify-between items-center">
            <strong>تقييم #${reviewIndex + 1}</strong>
            <button type="button" class="text-red-600 remove-review">حذف</button>
        </div>

        <input type="text"
               name="reviews[${reviewIndex}][reviewer_name]"
               class="input w-full m-2"
               placeholder="اسم العميل" required>

        <textarea
            name="reviews[${reviewIndex}][comment]"
            class="input w-full m-2 p-2"
            placeholder="التعليق" required></textarea>

        <select name="reviews[${reviewIndex}][stars]" class="input w-full m-2" required>
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

            wrapper.appendChild(div);

            div.querySelector('.remove-review').addEventListener('click', () => {
                div.remove();
            });

            reviewIndex++;
        });
    </script>

    <script>
        let upsellIndex = 0;
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

        {{-- Product Selection --}}
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

        {{-- Product Name --}}
        <div>
            <label class="form-label">اسم المنتج *</label>
            <input type="text" name="upsell_products[${upsellIndex}][name]" class="input w-full product-name" placeholder="سيتم ملؤه تلقائياً" required>
        </div>

        {{-- Product Image --}}
        <div>
            <label class="form-label">صورة المنتج</label>
            <div class="flex gap-3">
                <input type="file" name="upsell_products[${upsellIndex}][image]" class="input w-full product-image-file" accept="image/*">
                <img class="product-image-preview w-20 h-20 rounded border object-cover" src="https://via.placeholder.com/80" alt="Product Image">
            </div>
        </div>

        {{-- Product Price --}}
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
    </script>
@endsection
