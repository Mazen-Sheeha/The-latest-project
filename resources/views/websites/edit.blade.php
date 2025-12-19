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
        <a href="{{ route('websites.index') }}" style="color:rgb(114, 114, 255);">
            الدومينات
        </a>
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
                عدل الدومين
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('websites.update', $website->id) }}" method="post" id="add-website"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                @method('PUT')
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">ال key</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="key" placeholder="key"
                            value="{{ old('key') ?? $website->key }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold">الدومين</h5>
                    </div>
                    <div class="flex items-center relative">
                        <input type="text" placeholder="trendow.com" class="input w-full" placeholder="الدومين"
                            name="domain" value="{{ old('domain') ?? $website->domain }}">
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
