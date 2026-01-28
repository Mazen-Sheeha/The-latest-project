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
        <a href="{{ route('domains.index') }}" style="color:rgb(114, 114, 255);">
            الدومينات
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        تعديل الدومين
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
            <form action="{{ route('domains.update', $domain->id) }}" method="post" id="edit-domain"
                class="flex flex-col gap-6">
                @csrf
                @method('PUT')
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex gap-2 flex-1">
                        <div class="flex items-center">
                            <h5 class="text-gray-800 font-semibold whitespace-nowrap">الدومين <span
                                    class="text-red-500">*</span></h5>
                        </div>
                        <div class="flex items-center relative flex-1">
                            <input type="text" class="input w-full @error('domain') border-red-500 @enderror"
                                name="domain" placeholder="example.com" value="{{ old('domain') ?? $domain->domain }}">
                            @error('domain')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex gap-2 flex-1">
                        <div class="flex items-center">
                            <h5 class="text-gray-800 font-semibold whitespace-nowrap">الحالة <span
                                    class="text-red-500">*</span></h5>
                        </div>
                        <div class="flex items-center relative flex-1">
                            <select name="status" class="input w-full @error('status') border-red-500 @enderror">
                                <option value="">اختر الحالة</option>
                                <option value="active" {{ old('status') ?? $domain->status == 'active' ? 'selected' : '' }}>
                                    نشط</option>
                                <option value="pending"
                                    {{ old('status') ?? $domain->status == 'pending' ? 'selected' : '' }}>معطل</option>
                            </select>
                            @error('status')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-gray-800 font-semibold mb-4">طريقة الإعداد <span class="text-red-500">*</span></h4>

<div class="flex flex-col gap-4">
    <!-- Wildcard Option (Disabled) -->
    <label
        class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-100 opacity-60">
        <input type="radio"
               name="setup_type"
               value="wildcard"
               disabled
               class="mt-1 cursor-not-allowed">
        <div class="flex-1">
            <h5 class="text-gray-800 font-semibold">
                Wildcard Domain (*.example.com)
                <span class="ml-2 text-xs bg-gray-300 text-gray-700 px-2 py-0.5 rounded">
                    غير متاح حاليًا
                </span>
            </h5>
            <p class="text-gray-600 text-sm mt-1">
                هذه الميزة قيد التطوير حاليًا وسيتم توفيرها قريبًا.
            </p>
        </div>
    </label>

    <!-- DNS Record Option -->
    <label
        class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition"
        onclick="updateSetupType('dns_record')">
        <input type="radio"
               name="setup_type"
               value="dns_record"
               {{ old('setup_type') == 'dns_record' || !old('setup_type') ? 'checked' : '' }}
               class="mt-1">
        <div class="flex-1">
            <h5 class="text-gray-800 font-semibold">DNS Record للنسخ واللصق</h5>
            <p class="text-gray-600 text-sm mt-1">
                سيتم إنشاء سجل DNS جاهز للنسخ واللصق في لوحة التحكم الخاصة بك
                (مثل Hostinger)
            </p>
        </div>
    </label>
</div>                    
                    
                    @error('setup_type')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col md:flex-row gap-6">
                    <!--<div class="flex gap-2 flex-1">-->
                    <!--    <div class="flex items-center">-->
                    <!--        <h5 class="text-gray-800 font-semibold whitespace-nowrap">IP التحقق</h5>-->
                    <!--    </div>-->
                    <!--    <div class="flex items-center relative flex-1">-->
                    <!--        <input type="text" class="input w-full @error('verification_ip') border-red-500 @enderror"-->
                    <!--            name="verification_ip" placeholder="192.168.1.1"-->
                    <!--            value="{{ old('verification_ip') ?? $domain->verification_ip }}">-->
                    <!--        @error('verification_ip')-->
                    <!--            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>-->
                    <!--        @enderror-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="btn btn-light hover:bg-blue-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                            حفظ التعديلات <i class="fas fa-save me-2 mr-2"></i>
                        </button>
                        <a href="{{ route('domains.index') }}"
                            class="btn btn-secondary text-white px-6 py-2 rounded-md transition-colors duration-200">
                            العودة
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection