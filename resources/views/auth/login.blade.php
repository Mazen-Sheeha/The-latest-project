@extends('layouts.app')
@section('style')
    <style>
        .page-bg {
            background-image: url("{{ asset('adminTemplate/media/images/2600x1200/bg-10.png') }}");
        }
    </style>
@endsection
@section('login_form')
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg" dir="rtl">
        <div class="card max-w-[370px] w-full">
            <form action="{{ route('login') }}" method="post" class="card-body flex flex-col gap-5 p-10" id="sign_in_form">
                @csrf
                <div class="text-center mb-2.5">
                    <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
                        تسجيل الدخول
                    </h3>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="form-label font-normal text-gray-900">
                        البريد الإلكتروني
                    </label>
                    <input class="input" placeholder="أدخل البريد الإلكتروني" type="email" required name="email"
                        value="{{ old('email') }}" />
                </div>
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between gap-1">
                        <label class="form-label font-normal text-gray-900">
                            كلمة المرور
                        </label>
                    </div>
                    <div class="input" data-toggle-password="true">
                        <input placeholder="أدخل كلمة المرور" type="password" required name="password" />
                        <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
                            <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
                            </i>
                            <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
                            </i>
                        </button>
                    </div>
                </div>
                <button class="btn btn-primary flex justify-center grow">
                    تسجيل
                </button>
            </form>
        </div>
    </div>
@endsection
