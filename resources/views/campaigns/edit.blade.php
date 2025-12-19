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
        <a href="{{ route('adsets.index') }}" style="color:rgb(114, 114, 255);">
            مجموعات الحملات الإعلانية
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        <a href="{{ route('campaigns.index') }}" style="color:rgb(114, 114, 255);">
            {{ $campaign->campaign }}
        </a>
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                عدل الحملة الإعلانية
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('campaigns.update', $campaign->id) }}" method="post" id="add-campaign"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="adset_id" value="{{ request()->adset }}">
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">اسم الحملة</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="campaign" placeholder="اسم الحملة"
                            value="{{ old('campaign') ? old('campaign') : $campaign->campaign }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">مصدر الحملة</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <select name="source" class="select">
                            <option value="facebook" @selected(old('source') ? old('source') === 'facebook' : $campaign->source === 'facebook')>فيسبوك</option>
                            <option value="tiktok" @selected(old('source') ? old('source') === 'tiktok' : $campaign->source === 'tiktok')>تيك توك</option>
                            <option value="snapchat" @selected(old('source') ? old('source') === 'snapchat' : $campaign->source === 'snapchat')>سناب شات</option>
                            <option value="google" @selected(old('source') ? old('source') === 'google' : $campaign->source === 'google')>جوجل</option>
                            <option value="whatsapp" @selected(old('source') ? old('source') === 'whatsapp' : $campaign->source === 'whatsapp')>واتساب</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">رابط المنتوج</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        @php
                            $campaignUrl = old('url') ? old('url') : explode('?', $campaign->url)[0];
                        @endphp
                        <input type="text" class="input w-full" name="url" placeholder="رابط المنتوج"
                            value="{{ $campaignUrl }}">
                    </div>
                </div>
                <button type="submit"
                    class="btn btn-light hover:bg-blue-600 text-white px-4 py-2 rounded-md md:ml-auto transition-colors duration-200">
                    تعديل<i class="ki-filled ki-pencil"></i>
                </button>
            </form>
        </div>
    </div>
@endsection
