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
        {{ $admin->name }}
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        تعديل
    </span>
@endsection
@section('content')
    <div class="card pb-2 5">
        <form class="card-body grid gap-5" action="{{ route('admins.update', $admin->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">اسم المدير</label>
                <input placeholder="اسم المدير" type="text" class="input" name="name"
                    value="{{ is_null(old('name')) ? $admin->name : old('name') }}">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">البريد الإلكتروني</label>
                <input placeholder="البريد الإلكتروني" type="email" class="input" name="email"
                    value="{{ is_null(old('email')) ? $admin->email : old('email') }}">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">Password</label>
                <input placeholder="كلمة المرور الجديدة" type="password" class="input" name="password">
            </div>
            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 mb-2.5">
                <label class="form-label max-w-56">تأكيد كلمة المرور</label>
                <input placeholder="تأكيد كلمة المرور الجديدة" type="password" class="input" name="password_confirmation">
            </div>
            @if (auth()->user()->id !== $admin->id)
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
                            <input class="checkbox" @checked(old('per_ids') ? in_array($per->id, old('per_ids')) : $admin->hasPermission($per->name)) name="per_ids[]" type="checkbox"
                                value="{{ $per->id }}" />
                        </label>
                    @endforeach
                </div>
            @endif
            <button class="btn btn-primary flex justify-center">
                تعديل
            </button>
        </form>
    </div>
@endsection
