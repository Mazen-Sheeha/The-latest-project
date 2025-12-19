@extends('layouts.app')
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
@endsection
@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">
            لوحة التحكم
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        <a href="{{ route('orders.index') }}" style="color:rgb(114, 114, 255);">
            الطلبات
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        <a href="{{ route('orders.show', $order->id) }}" style="color:rgb(114, 114, 255);">
            {{ $order->name }}
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        تعديل
    </span>
@endsection

@section('content')
    <div class="card pb-2 5">
        <form class="card-body grid gap-5" action="{{ route('orders.update', $order->id) }}" method="post" id="form">
            @csrf
            @method('PUT')
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">اسم المشتري</label>
                <input type="text" class="input" name="name" value="{{ old('name') ? old('name') : $order->name }}"
                    placeholder="الاسم">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">رقم الهاتف</label>
                <input type="text" class="input" name="phone" placeholder="رقم الهاتف"
                    value="{{ old('phone') ? old('phone') : $order->phone }}">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">المدينة</label>
                <input type="text" class="input" name="city" value="{{ old('city') ? old('city') : $order->city }}"
                    placeholder="المدينة">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">العنوان</label>
                <textarea class="textarea" name="address"rows="5" placeholder="العنوان">{{ old('address') ? old('address') : $order->address }}</textarea>
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">رقم التتبع (اختياري)</label>
                <input type="text" class="input" name="tracking_number"
                    value="{{ old('tracking_number') ? old('tracking_number') : $order->tracking_number }}"
                    placeholder="رقم التتبع">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">الرابط</label>
                <input class="input" name="url" placeholder="الرابط"
                    value="{{ old('url') ? old('url') : $order->url }}" />
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">سعر شركة الشحن</label>
                <input type="number" class="input" min="0" name="shipping_price" id="shipping" placeholder="السعر"
                    value="{{ old('shipping_price') ? old('shipping_price') : number_format($order->shipping_price, 2) }}" />
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">ملاحظات</label>
                <textarea class="textarea" name="notes" placeholder="دون ملاحظات على الطلب" id="notes" rows="6">{{ old('notes') ?? $order->notes }}</textarea>
            </div>
            @if ($campaigns->count() > 0)
                <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                    <label for="select-beast" class="text-nowrap">أضف ال UTM (اختياري)</label>
                    <select name="campaign_id" id="select-beast" autocomplete="off" style="width: 100%">
                        <option disabled selected>اختر الحملة الإعلانية</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}" @selected($order->campaign_id == $campaign->id)>اسم الحملة :
                                {{ $campaign->campaign }} | المصدر :
                                {{ $campaign->source }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if ($products->count() > 0)
                <div class="products">
                    @php
                        $curProductsIds = old('product_ids') ? old('product_ids') : $order->products->pluck('id');
                        $curProducts = \App\Models\OrderProduct::where('order_id', $order->id)
                            ->get()
                            ->keyBy('product_id');
                    @endphp
                    @foreach ($curProductsIds as $index => $productId)
                        <div class="product">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                                @if ($index > 0)
                                    <span
                                        class="cursor-pointer rounded-full w-5 h-5 flex items-center justify-center bg-red-500 text-white hover:bg-red-600 delete-product"
                                        style="border: 1px solid gray;" onclick="handleDeleteProduct()">&times;</span>
                                @endif
                                <label class="form-label" style="width:75px; align-self: center">المنتوج</label>
                                <div class="w-full mb-3">
                                    <select name="product_ids[]" class="w-full data-attr mb-3">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" @selected($productId == $product->id)
                                                data-src='{{ $product->image() }}'
                                                shippingPrice="{{ $product->shipping_company->price }}">
                                                {{ $product->name }} - {{ $product->code }} - {{ $product->price }}AED
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="lg:flex gap-2 pro-data">
                                        <div class="flex gap-2 w-full">
                                            <label>الكمية</label>
                                            <input type="number" class="input mb-3 quantity"
                                                name="quantities[{{ $productId ?? '' }}]"
                                                value="{{ old('quantities') ? old('quantities.' . ($productId ?? '')) : $curProducts[$productId]->quantity }}"
                                                placeholder="الكمية">
                                        </div>
                                        <div class=" flex gap-2 w-full">
                                            <label>السعر</label>
                                            <input type="text" class="input w-56 price"
                                                name="prices[{{ $productId ?? '' }}]"
                                                value="{{ old('quantities') ? old('prices.' . ($productId ?? '')) : $curProducts[$productId]->price }}"
                                                placeholder="السعر">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="add-product"
                    class="btn btn-light hover:bg-blue-600 text-white px-4 py-2 rounded-md md:ml-auto transition-colors duration-200"
                    style="width:fit-content">
                    أضف منتوج<i class="fas fa-plus me-2 mr-5"></i>
                </div>
            @endif
            <button class="btn btn-primary flex justify-center">
                تعديل
            </button>
        </form>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        function initTomSelects() {
            if (document.querySelector(".data-attr")) {
                document.querySelectorAll('.data-attr:not(.tomselected)').forEach(select => {
                    new TomSelect(select, {
                        render: {
                            option: function(data, escape) {
                                return `<div><img loading="lazy" class="me-2" width="50" height="50" style="max-height:50px; max-width:50px;" src="${data.src}">${data.text}</div>`;
                            },
                            item: function(item, escape) {
                                return `<div><img loading="lazy" class="me-2" src="${item.src}" height="50" width="50" style="max-height:50px; max-width:50px;">${item.text}</div>`;
                            }
                        },
                        onInitialize: function() {
                            this.wrapper.classList.add('tomselected');
                        }
                    });
                });
            }
            if (document.getElementById("select-beast")) {
                new TomSelect("#select-beast", {
                    create: true,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initTomSelects();
            document.querySelectorAll(".price").forEach(inp => {
                inp.addEventListener("input", (e) => {
                    const input = e.target;
                    input.value = input.value.replace(/[^0-9.]/g, '');
                    if ((input.value.match(/\./g) || []).length > 1) {
                        input.value = input.value.substr(0, input.value.length - 1);
                    }
                })
            })
        });

        const addProBtn = document.getElementById('add-product');

        addProBtn.addEventListener('click', () => {
            const div = document.createElement("div");
            div.innerHTML = `
                <div class="product">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                        <span class="cursor-pointer rounded-full w-5 h-5 flex items-center justify-center" style="border: 1px solid gray;" onclick="handleDeleteProduct()">&times;</span>
                        <label class="form-label" style="width:75px; align-self: center"> اختر المنتوج</label>
                        <div class="w-full mb-3">
                            <select name="product_ids[]" class="w-full data-attr mb-3">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected(old('product_id') === $product->id)
                                        data-src='{{ $product->image() }}'>
                                        {{ $product->name }} - {{ $product->code }} - {{ $product->price }}AED
                                    </option>
                                @endforeach
                            </select>
                            <div class="flex gap-2 pro-data">
                                <input type="number" class="input mb-3 quantity" placeholder="الكمية">
                                <input type="number" class="input w-56 price" placeholder="السعر">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.querySelector(".products").append(div);
            initTomSelects();
        });

        document.querySelector("#form").addEventListener("submit", function(e) {
            document.querySelectorAll(".product").forEach(function(productDiv) {
                const select = productDiv.querySelector("select");
                const productId = select.value;
                const priceInput = productDiv.querySelector(".price");
                const quantityInput = productDiv.querySelector(".quantity");

                if (priceInput && quantityInput && productId) {
                    priceInput.name = `prices[${productId}]`;
                    quantityInput.name = `quantities[${productId}]`;
                }
            });
            Swal.fire({
                title: 'جاري التحميل ...',
                text: 'برجاء الانتظار',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            })
        })

        function handleDeleteProduct() {
            this.event.target.closest('div.product').remove();
        }
    </script>
@endsection
