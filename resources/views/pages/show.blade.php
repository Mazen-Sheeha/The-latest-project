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
        <a href="{{ route('pages.index') }}" style="color:rgb(114, 114, 255);">
            الصفحات
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        {{ $page->title }}
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header flex items-center justify-between">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="flex gap-2">
                <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-secondary">
                    <i class="fas fa-pencil me-2"></i>تعديل
                </a>
                <a href="{{ route('pages.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>رجوع
                </a>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="grid grid-cols-1 gap-4 mb-6">
                <div>
                    <h5 class="text-sm font-semibold text-gray-600 mb-2">العنوان</h5>
                    <p class="text-gray-800 text-lg">{{ $page->title }}</p>
                </div>

                @if ($page->slug)
                    <div>
                        <h5 class="text-sm font-semibold text-gray-600 mb-2">الرابط</h5>
                        <p class="text-gray-800 text-lg">{{ $page->slug }}</p>
                    </div>
                @endif

                @if ($page->description)
                    <div>
                        <h5 class="text-sm font-semibold text-gray-600 mb-2">الوصف</h5>
                        <p class="text-gray-700">{{ $page->description }}</p>
                    </div>
                @endif

                <div>
                    <h5 class="text-sm font-semibold text-gray-600 mb-2">حالة النشر</h5>
                    <span class="badge {{ $page->is_active ? 'badge-success' : 'badge-secondary' }}">
                        {{ $page->is_active ? 'منشورة' : 'غير منشورة' }}
                    </span>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h5 class="text-sm font-semibold text-gray-600 mb-4">المحتوى</h5>
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($page->content)) !!}
                </div>
            </div>

            @if ($page->created_at)
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <h5 class="font-semibold text-gray-600">تاريخ الإنشاء</h5>
                            <p class="text-gray-700">{{ $page->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if ($page->updated_at && $page->updated_at != $page->created_at)
                            <div>
                                <h5 class="font-semibold text-gray-600">آخر تحديث</h5>
                                <p class="text-gray-700">{{ $page->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
