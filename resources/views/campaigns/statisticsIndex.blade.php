@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .on-off {
            display: block;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
        }

        .on {
            background: green;
        }

        .off {
            background: red;
        }
    </style>
@endsection

@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">لوحة التحكم</a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs"></i>
    <span class="text-gray-700">
        <a href="{{ route('adsets.index') }}" style="color:rgb(114, 114, 255);">مجموعات الحملات الإعلانية</a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs"></i>
    <span class="text-gray-700">
        <a href="{{ route('adsets.statistics') }}" style="color:rgb(114, 114, 255);">تقرير مجموعات الحملات الإعلانية</a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs"></i>
    <span class="text-gray-700">تقرير الحملات الإعلانية</span>
@endsection

@section('content')
    <form action="{{ route('campaigns.statistics') }}" method="GET" class="flex gap-3 flex-col flex-wrap" id="searchForm">
        <input type="hidden" name="adset_id" value="{{ request()->adset_id }}">
        <div class="flex gap-3 items-center">
            <span>الحالة</span>
            <select name="active" class="select">
                <option value="all" @selected(request()->active == 'all' || !request()->active)>الكل</option>
                <option value="active" @selected(request()->active == 'active')>نشط</option>
                <option value="not-active" @selected(request()->active == 'not-active')>غير نشط</option>
            </select>

            <span>المصدر</span>
            <select name="source" class="select">
                <option value="all" selected>الكل</option>
                @foreach (['facebook', 'tiktok', 'snapchat', 'google', 'whatsapp'] as $src)
                    <option value="{{ $src }}" @selected(request()->source === $src)>{{ $src }}</option>
                @endforeach
            </select>

            <input type="search" id="dateRange" placeholder="اختر من - إلى" class="input"
                value="{{ request()->from && request()->to ? request()->from . ' إلى ' . request()->to : '' }}" />
            <input type="hidden" name="from" class="from" value="{{ request()->from }}">
            <input type="hidden" name="to" class="to" value="{{ request()->to }}">

            <button type="submit" id="searchBtn" class="btn btn-primary flex justify-center max-w-56">بحث</button>

            @if (request()->hasAny(['from', 'to', 'source', 'active']))
                <a href="{{ route('campaigns.statistics', ['adset_id' => request()->adset_id]) }}"
                    class="btn btn-secondary flex justify-center max-w-56">تراجع</a>
            @endif
        </div>
    </form>

    <div class="card min-w-full mt-4">
        <div class="card-header">
            <h3 class="card-title">تقرير الحملات الإعلانية</h3>
        </div>

        <div class="card-table scrollable-x-auto">
            <div class="scrollable-auto">
                @if ($campaigns->count() > 0)
                    <table class="table align-middle text-2sm text-gray-600" id="campaigns-table">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="text-start font-medium min-w-5">نشط</th>
                                <th class="text-start font-medium min-w-15">الحملة</th>
                                <th class="text-start font-medium min-w-15">الميزانية</th>
                                <th class="text-start font-medium min-w-15">عدد الطلبات</th>
                                <th class="text-start font-medium min-w-15">طلبات تم تأكيدها</th>
                                <th class="text-start font-medium min-w-15">طلبات تم توصليها</th>
                                <th class="text-start font-medium min-w-15">التكلفة لكل طلب</th>
                                <th class="text-start font-medium min-w-15">التكلفة لكل طلب تم تأكيده</th>
                                <th class="text-start font-medium min-w-15">التكلفة لكل طلب تم توصيله</th>
                                <th class="text-start font-medium min-w-15">معدل الربح الكلي</th>
                                <th class="text-start font-medium min-w-15">معدل تأكيد الطلبات</th>
                                <th class="text-start font-medium min-w-15">معدل توصيل الطلبات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campaigns as $campaign)
                                @php
                                    $stats = $campaign->calculateStatistics();
                                @endphp
                                <tr>
                                    <td>
                                        <form action="{{ route('campaigns.changeActive', $campaign->id) }}" method="POST"
                                            class="active-form" style="margin: 0">
                                            @csrf
                                            <button
                                                class="on-off @if ($campaign->active) on @else off @endif"></button>
                                        </form>
                                    </td>
                                    <td class="campaign-name text-nowrap">
                                        {{ $campaign->source }}
                                        <br>
                                        {{ $campaign->campaign }}
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('budgets.index', ['campaign' => $campaign->id]) }}"
                                            style="color:rgb(114, 114, 255); text-decoration: underline">
                                            AED {{ $campaign->budgets_sum_budget ?? 0 }}
                                        </a>
                                    </td>
                                    <td>{{ $stats['orders_count'] }}</td>
                                    <td>{{ $stats['confirmed'] }}</td>
                                    <td>{{ $stats['delivered'] }}</td>
                                    <td>AED {{ number_format($stats['c_p_result'], 2) }}</td>
                                    <td>AED {{ number_format($stats['c_p_confirmed'], 2) }}</td>
                                    <td>AED {{ number_format($stats['c_p_delivered'], 2) }}</td>
                                    <td>AED {{ number_format($stats['total_earn_by_pcs'], 2) }}</td>
                                    <td>% {{ number_format($stats['confirmation_rate'], 2) }}</td>
                                    <td>% {{ number_format($stats['delivered_rate'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-4 text-center bg-white">
                        @if (request()->hasAny(['from', 'to', 'source', 'active']))
                            <h5 class="text-gray-600">لا توجد حملات إعلانية تطابق معايير البحث</h5>
                            <a href="{{ route('campaigns.statistics', ['adset_id' => request()->adset_id]) }}"
                                class="text-blue-500 hover:underline">إعادة تعيين الفلتر</a>
                        @else
                            <h5 class="text-gray-600">لا توجد حملات إعلانية</h5>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="card-footer justify-center">
            {{ $campaigns->links() }}
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Date Range Picker
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "ar"
            });

            // Form Submission
            document.getElementById("searchForm").addEventListener("submit", function(e) {
                const dates = e.target.querySelector("#dateRange").value.split(" إلى ");
                e.target.querySelector('[name="from"]').value = dates[0] || '';
                e.target.querySelector('[name="to"]').value = dates[1] || '';
            });

            // Toggle Active Status
            document.querySelectorAll('.active-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    const button = form.querySelector('.on-off');
                    const spinner = document.createElement('div');
                    spinner.className = 'loading-spinner';
                    spinner.style.display = 'block';

                    // Add spinner to button
                    button.innerHTML = '';
                    button.appendChild(spinner);
                    button.style.opacity = '0.7';

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')
                                    .value,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                button.classList.toggle('on');
                                button.classList.toggle('off');
                                toastify().success(data.message);
                            } else {
                                toastify().error(data.message || 'حدث خطأ ما');
                            }
                        })
                        .catch(error => {
                            toastify().error('حدث خطأ في الاتصال');
                        })
                        .finally(() => {
                            button.innerHTML = '';
                            button.style.opacity = '1';
                        });
                });
            });
        });
    </script>
@endsection
