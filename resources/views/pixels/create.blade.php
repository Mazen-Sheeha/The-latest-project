@extends('layouts.app')

@section('url_pages')
    <span>
        <a href="{{ route('home') }}" style="color:rgb(114,114,255)">لوحة التحكم</a>
    </span>
    <i class="ki-filled ki-left"></i>
    <span>
        <a href="{{ route('pixels.index') }}" style="color:rgb(114,114,255)">البكسلات</a>
    </span>
    <i class="ki-filled ki-left"></i>
    <span>إضافة بكسل</span>
@endsection

@section('content')
    <div class="space-y-6">

        <form action="{{ route('pixels.store') }}" method="POST" id="pixel-form">
            @csrf

            <div class="card bg-white shadow rounded-lg">
                <div class="card-header">
                    <h3 class="card-title">إضافة بكسل جديد</h3>
                </div>

                <div class="card-body p-6 space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">اسم البكسل *</label>
                            <input name="name" class="input w-full" required value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">نوع البكسل *</label>
                            <select name="type" class="input w-full" required>
                                <option value="">اختر نوع البكسل</option>
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">معرف البكسل (Pixel ID) *</label>
                            <input name="pixel_id" class="input w-full" required value="{{ old('pixel_id') }}"
                                placeholder="مثال: 123456789012345">
                            @error('pixel_id')
                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <span>تفعيل البكسل</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">كود البكسل (اختياري)</label>
                        <textarea name="code" class="input w-full font-mono text-xs" rows="6"
                            placeholder="أدخل كود البكسل الكامل إذا كان متاحاً">{{ old('code') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            يمكنك إدخال كود البكسل الكامل (مثل: fbq('init', '...')) أو تركه فارغاً
                        </p>
                        @error('code')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer flex gap-3 p-6">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('pixels.index') }}" class="btn btn-secondary">رجوع</a>
                </div>
            </div>

        </form>
    </div>
@endsection
