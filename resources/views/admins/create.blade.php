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
        <a href="{{ route('admins.index') }}" style="color:rgb(114, 114, 255);">
            المدراء
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        إضافة
    </span>
@endsection
@section('content')
    <div class="card pb-2 5">
        <form class="card-body grid gap-5" action="{{ route('admins.store') }}" method="post">
            @csrf
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">اسم المدير</label>
                <input type="text" class="input" name="name" value="{{ old('name') }}" placeholder="الاسم">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">البريد الإلكتروني</label>
                <input type="email" class="input" name="email" value="{{ old('email') }}"
                    placeholder="البريد الإلكتروني">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">كلمة المرور</label>
                <input type="password" class="input" name="password" placeholder="كلمة المرور">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">تأكيد كلمة المرور</label>
                <input type="password" class="input" name="password_confirmation" placeholder="تأكيد كلمة المرور">
            </div>

            <div class="card h-full">
                <div class="card-header">
                    <h3 class="card-title">
                        الصلاحيات
                    </h3>
                </div>
                @foreach ($permissions as $per)
                    <label class="card-group flex items-center justify-between py-4 gap-2.5">
                        <div class="flex flex-col justify-center gap-1.5">
                            <span class="leading-none font-medium text-sm text-gray-900">
                                {{ $per->name }}
                            </span>
                        </div>
                        <input class="checkbox" name="per_ids[]" @checked(old('per_ids') ? in_array($per->id, old('per_ids')) : false) type="checkbox"
                            value="{{ $per->id }}" />
                    </label>
                @endforeach
            </div>
            <button class="btn btn-primary flex justify-center">
                إضافة
            </button>
        </form>
    </div>
@endsection
