@extends('layouts.app')
@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">
            لوحة التحكم
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        <a href="{{ route('products.index') }}" style="color:rgb(114, 114, 255);">
            المنتوجات
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        <a href="{{ route('products.show', $product->id) }}" style="color:rgb(114, 114, 255);">
            {{ $product->name }}
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
        <form class="card-body grid gap-5" action="{{ route('products.update', $product->id) }}" method="post"
            id="update-product" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="flex items-baseline justify-center flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label for="image" class="cursor-pointer">
                    <img loading="lazy" src="{{ $product->image ? $product->image() : asset('images/upload_area.png') }}"
                        class="image-label " alt="Upload here"
                        style="width: 200px; height: 117px; object-fit: cover; border: 1px rgba(146, 145, 145, 0.259) solid">
                </label>
                <input id="image" hidden name="image" type="file" onchange="handleImageChange()">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">اسم المنتوج</label>
                <input type="text" class="input" name="name" value="{{ old('name') ? old('name') : $product->name }}"
                    placeholder="الاسم">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">كود المنتوج</label>
                <input type="text" class="input" name="code" placeholder="الكود"
                    value="{{ old('code') ? old('code') : $product->code }}">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">سعر المنتوج</label>
                <input type="text" class="input" name="price" id="price"
                    value="{{ old('price') ? old('price') : $product->price }}" placeholder="السعر">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">المخزون</label>
                <input type="number" class="input" name="stock"
                    value="{{ old('stock') ? old('stock') : $product->stock }}" placeholder="كم عدد القطع المتوفرة ؟">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">شركة الشحن</label>
                <select name="shipping_company_id" class="select">
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected(old('shipping_company_id') ? old('shipping_company_id') === $company->id : $product->shipping_company_id == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary flex justify-center">
                تعديل
            </button>
        </form>
    </div>
@endsection
@section('script')
    <script>
        function handleImageChange() {
            const input = this.event.target;
            const reader = new FileReader();
            reader.onload = function(e) {
                input.closest("div").querySelector('label img').src = e.target.result

            }
            reader.readAsDataURL(input.files[0]);
            console.log(document.querySelector("[type='hidden']").value)
        }
        document.querySelector("#update-product").addEventListener("submit", () => {
            Swal.fire({
                title: 'جاري التحميل ...',
                text: 'برجاء الانتظار',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            })
        })
        
        document.getElementById('price').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9.]/g, '');
            if ((this.value.match(/\./g) || []).length > 1) {
                this.value = this.value.substr(0, this.value.length - 1);
            }
        });
    </script>
@endsection
