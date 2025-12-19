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
        <a href="{{ route('shipping_companies.index') }}" style="color:rgb(114, 114, 255);">
            شركات الشحن
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        {{ $company->name }}
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        تعديل
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                عدل شركة شحن
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('shipping_companies.update', $company->id) }}" method="post" id="add-company"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                @method('PUT')
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">اسم شركة الشحن</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="name" placeholder="اسم شركة الشحن"
                            value="{{ old('name') ? old('name') : $company->name }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold">سعر الشركة</h5>
                    </div>
                    <div class="flex items-center relative">
                        <input type="text" id="price" min="1" max="1000" placeholder="5" class="input"
                            placeholder="السعر" name="price" style="width: 100px"
                            value="{{ old('shipping_price') ? old('shipping_price') : $company->price }}">
                    </div>
                </div>
                <button type="submit"
                    class="btn btn-light hover:bg-blue-600 text-white px-4 py-2 rounded-md md:ml-auto transition-colors duration-200">
                    تعديل <i class="ki-filled ki-pencil"></i>
                </button>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById('price').addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9.]/g, '');
                if ((this.value.match(/\./g) || []).length > 1) {
                    this.value = this.value.substr(0, this.value.length - 1);
                }
            });
        });
    </script>
@endsection
