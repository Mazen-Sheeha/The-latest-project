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
                            <input name="name" class="input w-full" required>
                        </div>

                        <div>
                            <label class="form-label">عنوان الصفحة *</label>
                            <input name="title" class="input w-full" required>
                        </div>

                        <div>
                            <label class="form-label">الموقع / الدومين *</label>
                            <select name="website_id" class="input w-full" required>
                                <option value="">اختر الدومين</option>
                                @foreach ($websites as $website)
                                    <option value="{{ $website->id }}">
                                        {{ $website->domain }}
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
                    </div>

                    <div>
                        <label class="form-label">الوصف *</label>
                        <textarea name="description" class="input w-full" rows="12"></textarea>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked>
                        نشر الصفحة
                    </label>
                </div>
            </div>

            {{-- ================= SALE ================= --}}
            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">بيانات الخصم</h3>
                </div>

                <div class="card-body p-6 space-y-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="has-sale">
                        تفعيل الخصم
                    </label>

                    <div id="sale-fields" class="hidden grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">السعر الأصلي</label>
                            <input type="number" step="0.01" name="original_price" id="original_price"
                                class="input w-full">
                        </div>

                        <div>
                            <label class="form-label">سعر البيع</label>
                            <input type="number" step="0.01" name="sale_price" id="sale_price" class="input w-full">
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
                <div class="card-header">
                    <h3 class="card-title">منتجات إضافية (Upsell)</h3>
                </div>

                <div class="card-body p-6 space-y-4">

                    <p class="text-sm text-gray-600">
                        اختر المنتجات التي ستظهر بعد إتمام الطلب
                    </p>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">

                        @foreach ($products as $product)
                            <label
                                class="flex gap-3 items-center border rounded-lg p-3 cursor-pointer hover:border-blue-500 transition">

                                <input type="checkbox" name="upsell_products[]" value="{{ $product->id }}"
                                    class="accent-blue-600">

                                <img src="{{ $product->image ? asset($product->image) : asset('images/productDefault.webp') }}"
                                    class="w-14 h-14 rounded object-cover border">

                                <div class="flex flex-col">
                                    <span class="font-bold text-sm">
                                        {{ $product->name }}
                                    </span>
                                    <span class="text-green-600 text-sm font-semibold">
                                        {{ number_format($product->price) }} د.إ
                                    </span>
                                </div>

                            </label>
                        @endforeach

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
        const productSelect = document.getElementById('product-select');

        productSelect.addEventListener('change', e => {
            const option = e.target.selectedOptions[0];

            if (!option || !option.value) return;

            const nameInput = document.querySelector('input[name="name"]');
            nameInput.value = option.dataset.name || '';

            const originalPriceInput = document.querySelector('input[name="original_price"]');
            originalPriceInput.value = option.dataset.price || '';
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
        let reviewIndex = 0;

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
@endsection
