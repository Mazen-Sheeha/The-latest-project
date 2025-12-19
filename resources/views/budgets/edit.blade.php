@extends('layouts.app')
@section('url_pages')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">
            لوحة التحكم
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        <a href="{{ route('budgets.index') }}" style="color:rgb(114, 114, 255);">
            الميزانية
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        {{ $budget->date }}
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
                عدل ميزانية
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('budgets.update', $budget->id) }}" method="post" id="add-budget"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                @method('PUT')
                <input type="hidden" value="{{ $budget->campaign_id }}" name="campaign_id">
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">الميزانية</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="budget" placeholder="الميزانية"
                            value="{{ old('budget') ? old('budget') : $budget->budget }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">التاريخ</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="search" id="date" name="date" placeholder="التاريخ" class="input"
                            value="{{ old('date') ? old('date') : $budget->date }}" />

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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script>
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            locale: "ar"
        });
    </script>
@endsection
