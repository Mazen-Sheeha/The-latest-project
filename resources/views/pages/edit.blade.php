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
                            <input name="title" class="input w-full" required value="{{ old('title', $page->title) }}">
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
                    <h3 class="card-title">بيانات الخصم</h3>
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
                    <div class="card-header">
                        <h3 class="card-title">منتجات Upsell (مقترحة بعد الشراء)</h3>
                    </div>

                    <div class="card-body p-6 space-y-4">
                        <p class="text-sm text-gray-600">
                            اختر المنتجات التي ستظهر للعميل بعد إتمام الطلب
                        </p>

                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach ($products as $product)
                                <label class="flex items-center gap-3 border rounded p-3 cursor-pointer">
                                    <input type="checkbox" name="upsell_products[]" value="{{ $product->id }}"
                                        {{ !empty($upsellProducts) && $upsellProducts->contains($product->id) ? 'checked' : '' }}>

                                    <div>
                                        <strong class="block">{{ $product->name }}</strong>
                                        <span class="text-sm text-gray-500">
                                            {{ number_format($product->price, 2) }} ج.م
                                        </span>
                                    </div>
                                </label>
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
@endsection
