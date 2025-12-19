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
        الإحصائيات
    </span>
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection
@section('content')
    <div class="container-fixed flex flex-col gap-3">
        <form action="{{ route('statistics') }}" method="GET" class="flex gap-3 flex-col flex-wrap" id="searchForm">
            @if ($products->count() > 0)
                <div class="flex gap-3">
                    <select id="custom_js" name="products_ids[]" multiple placeholder="اختر منتوجات..." autocomplete="off"
                        style="width: 100%">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ in_array($product->id, request()->products_ids ?? []) ? 'selected' : '' }}>
                                {{ $product->name }} - {{ $product->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="flex gap-3">
                <input type="hidden" name="from" class="from" value="{{ request()->from }}">
                <input type="hidden" name="to" class="to" value="{{ request()->to }}">
                <select name="upsell" class="select">
                    <option value="all" {{ request()->upsell === 'all' ? 'selected' : '' }}>أبسل و غير أبسل</option>
                    <option value="not_upsell" {{ request()->upsell === 'not_upsell' ? 'selected' : '' }}>غير أبسل
                    </option>
                    <option value="upsell" {{ request()->upsell === 'upsell' ? 'selected' : '' }}>أبسل</option>
                </select>
                <select name="city" class="select">
                    <option value="all" {{ request()->city === 'all' ? 'selected' : '' }}>جميع المدن</option>
                    @php
                        $cities = [
                            'أبو ظبي / Abu Dhabi',
                            'دبي / Dubai',
                            'الشارقة / Sharjah',
                            'عجمان / Ajman',
                            'العين / Al Ain',
                            'الفجيرة / Fujairah',
                            'أم القيوين / Umm al-Quwain',
                            'رأس الخيمة / Ras Al Khaimah',
                        ];
                    @endphp
                    @foreach ($cities as $city)
                        <option value="{{ $city }}" {{ request()->city === $city ? 'selected' : '' }}>
                            {{ $city }}</option>
                    @endforeach
                </select>
                <input type="text" id="dateRange" placeholder="اختر من - إلى" class="input"
                    value="{{ request()->from && request()->to ? request()->from . ' إلى ' . request()->to : '' }}" />
                <button type="submit" id="searchBtn" class="btn btn-primary flex justify-center max-w-56">بحث</button>
                @if (request()->has('from'))
                    <a href="{{ route('statistics') }}" id="resetLink"
                        class="btn btn-secondary flex justify-center max-w-56">تراجع</a>
                @endif
            </div>
        </form>
        <p class="text-xl font-bold text-gray-900">الطلبات</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5">
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $productsSalesCost }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Items Cost
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $deliveredPiecesCount }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Quantity Of Items
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $deliveringNowCount }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Delivering
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $totalExchange }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Exchange
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $canceledOrdersCount }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Return
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $ordersDelivered }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Delivered
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $ordersCount }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Orders
                        </span>
                    </div>
                </div>
            </div>

            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            % {{ number_format($confirmationRate, 2) }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Confirmation Rate
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            % {{ number_format($deliveryRate, 2) }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Delivery Rate
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            % {{ number_format($upsellRate, 2) }}
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Upsell Rate
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-xl font-bold text-gray-900 mt-10">رؤية بيانية</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
            <div class="card p-5">
                <h3 class="font-semibold text-lg mb-4">نسبة الطلبات التي تم توصيلها</h3>
                <div id="delivery_chart"></div>
            </div>
            <div class="card p-5">
                <h3 class="font-semibold text-lg mb-4">نسبة الطلبات المؤكدة</h3>
                <div id="confirmation_chart"></div>
            </div>
            <div class="card p-5 col-span-2">
                <h3 class="font-semibold text-lg mb-4">الطلبات المستلمة يوميًا</h3>
                <div id="daily_delivered_chart"></div>
            </div>
        </div>

        <p class="text-xl font-bold text-gray-900 my-3">الأرباح</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-7.5">
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $unpaidCash }} AED
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Unpaid Cash
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ number_format($totalProfit, 2) }} AED
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Profit
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $exchangeCost }} AED
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Exchange Cost
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $totalDeliveryCost }} AED
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Delivery Cost
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ number_format($amountSpentOnAds, 2) }} AED
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Amount Spent On Ads
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $deliveredCash }} AED
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Delivered Cash
                        </span>
                    </div>
                </div>
            </div>
            <div class="card p-5 lg:p-7.5 lg:pt-7">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="text-base font-medium leading-none text-gray-900 ">
                            {{ $totalCash }} AED
                        </span>
                        <span class="text-2sm leading-5" style="color: gray">
                            Total Cash
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "ar"
        });

        const customJsElement = document.getElementById('custom_js');
        if (customJsElement) {
            new TomSelect(customJsElement, {
                create: true,
                render: {
                    option: function(data) {
                        const div = document.createElement('div');
                        div.className = 'd-flex align-items-center';
                        const span = document.createElement('span');
                        span.className = 'flex-grow-1';
                        span.innerText = data.text;
                        div.append(span);
                        return div;
                    },
                }
            });
        }
        document.addEventListener("DOMContentLoaded", () => {
            const searchBtn = document.getElementById("searchBtn");
            const resetLink = document.getElementById("resetLink");
            const dateRangeInput = document.getElementById("dateRange");
            const productsSelect = document.querySelector('#custom_js');
            const upsellSelect = document.querySelector('[name="upsell"]');
            const citySelect = document.querySelector('[name="city"]');

            const originalProducts = @json(request()->products_ids ?? []);
            const originalUpsell = @json(request()->upsell ?? 'all');
            const originalCity = @json(request()->city ?? 'all');
            const originalFrom = @json(request()->from);
            const originalTo = @json(request()->to);
            const originalDateRange = (originalFrom && originalTo) ? `${originalFrom} إلى ${originalTo}` : '';

            function getSelectedValues(select) {
                return Array.from(select.selectedOptions).map(opt => opt.value);
            }

            function arraysEqual(a, b) {
                return a.length === b.length && a.every(v => b.includes(v));
            }

            function updateButton() {
                const currentProducts = getSelectedValues(productsSelect);
                const currentUpsell = upsellSelect.value;
                const currentCity = citySelect.value;
                const currentDateRange = dateRangeInput.value.trim();

                const sameProducts = arraysEqual(currentProducts, originalProducts);
                const sameUpsell = currentUpsell === originalUpsell;
                const sameCity = currentCity === originalCity;
                const sameDate = currentDateRange === originalDateRange;

                if (sameProducts && sameUpsell && sameCity && sameDate) {
                    searchBtn?.classList.add("hidden");
                    resetLink?.classList.remove("hidden");
                } else {
                    searchBtn?.classList.remove("hidden");
                    resetLink?.classList.add("hidden");
                }
            }

            productsSelect.addEventListener("change", updateButton);
            upsellSelect.addEventListener("change", updateButton);
            citySelect.addEventListener("change", updateButton);
            dateRangeInput.addEventListener("input", updateButton);

            updateButton();

            document.getElementById("searchForm").addEventListener("submit", (e) => {
                const dates = dateRangeInput.value.split(" إلى ");
                e.target.querySelector('[name="from"]').value = dates[0] ?? '';
                e.target.querySelector('[name="to"]').value = dates[1] ?? '';
            });

            new ApexCharts(document.querySelector("#delivery_chart"), {
                chart: {
                    type: 'pie',
                    height: 300
                },
                series: [
                    {{ $confirmedCount - $ordersDelivered }},
                    {{ $ordersDelivered }}
                ],
                labels: ['لم توصل', 'تم توصيلها'],
                colors: ['#ff4848', '#4caf50'],
                tooltip: {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        return `<div style="
                            padding: 6px 10px;
                            background: ${w.config.colors[seriesIndex]};
                            color: white;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                        ">
                            ${w.config.labels[seriesIndex]}: ${series[seriesIndex]}
                        </div>`;
                    }
                },
                dataLabels: {
                    style: {
                        colors: ['#ffffff']
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                legend: {
                    labels: {
                        colors: ['#ff4848', '#4caf50'],
                    }
                }
            }).render();

            new ApexCharts(document.querySelector("#confirmation_chart"), {
                chart: {
                    type: 'pie',
                    height: 300
                },
                series: [
                    {{ $confirmedCount }},
                    {{ $notConfirmedCount }}
                ],
                labels: ['مؤكدة', 'غير مؤكدة'],
                colors: ['#3B82F6', '#F59E0B'],
                tooltip: {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        return `<div style="
                            padding: 6px 10px;
                            background: ${w.config.colors[seriesIndex]};
                            color: white;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                        ">
                            ${w.config.labels[seriesIndex]}: ${series[seriesIndex]}
                        </div>`;
                    }
                },
                dataLabels: {
                    style: {
                        colors: ['#ffffff']
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                legend: {
                    labels: {
                        colors: ['#3B82F6', '#F59E0B'],
                    }
                }
            }).render();

            new ApexCharts(document.querySelector("#daily_delivered_chart"), {
                chart: {
                    type: 'bar',
                    height: 300
                },
                series: [{
                    name: 'طلبات مستلمة',
                    data: @json($deliveredCounts)
                }],
                xaxis: {
                    categories: @json($deliveredDates),
                    title: {
                        text: 'التاريخ'
                    }
                },
                yaxis: {
                    title: {
                        text: 'عدد الطلبات'
                    }
                },
                colors: ['#2196F3']
            }).render();

        });
    </script>
@endsection
