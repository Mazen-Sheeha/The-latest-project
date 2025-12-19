@extends('layouts.app')
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        form:has(span.status) {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
        }

        .ts-wrapper .option .title {
            display: block;
        }

        .ts-wrapper .option .url {
            font-size: 12px;
            display: block;
            color: #a0a0a0;
        }

        .no-select {
            user-select: none;
        }

        .status {
            width: 100% !important;
            padding: 2px 5px;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
        }
    </style>
@endsection
@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">
            لوحة التحكم
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        الطلبات
    </span>
@endsection
@section('content')
    @if (
        $orders->count() > 0 ||
            request()->has('search') ||
            (request()->has('from') && request()->has('to')) ||
            (request()->filled('upsell') && request()->upsell !== 'all') ||
            (request()->filled('products_ids') && !in_array('all', request()->products_ids ?? [])) ||
            (request()->filled('city') && request()->city !== 'all') ||
            (request()->filled('shipping_company_id') && request()->shipping_company_id !== 'all'))
        <form action="{{ route('orders.index') }}" method="GET" class="flex gap-3 flex-col flex-wrap" id="searchForm">
            <div class="flex gap-3">
                <input type="hidden" name="from" class="from" value="{{ request()->from }}">
                <input type="hidden" name="to" class="to" value="{{ request()->to }}">
                <select name="upsell" class="select">
                    <option value="all" {{ request()->upsell === 'all' ? 'selected' : '' }}>أبسل و غير أبسل</option>
                    <option value="not_upsell" {{ request()->upsell === 'not_upsell' ? 'selected' : '' }}>غير أبسل</option>
                    <option value="upsell" {{ request()->upsell === 'upsell' ? 'selected' : '' }}>أبسل</option>
                </select>
                <select name="paid" class="select" id="paidSelect">
                    <option value="all" {{ request()->paid === 'all' || is_null(request()->paid) ? 'selected' : '' }}>
                        مدفوع و غير مدفوع</option>
                    <option value="paid" {{ request()->paid === 'paid' ? 'selected' : '' }}>مدفوع</option>
                    <option value="not_paid" {{ request()->paid === 'not_paid' ? 'selected' : '' }}>غير مدفوع</option>
                </select>
                <select name="order_status" class="select" id="statusSelect">
                    <option value="">كل الحالات</option>
                    <option value="waiting_for_confirmation"
                        {{ request()->order_status === 'waiting_for_confirmation' ? 'selected' : '' }}>بانتظار التأكيد
                    </option>
                    <option value="waiting_for_shipping"
                        {{ request()->order_status === 'waiting_for_shipping' ? 'selected' : '' }}>بانتظار الشحن</option>
                    <option value="received" {{ request()->order_status === 'received' ? 'selected' : '' }}>تم الاستلام
                    </option>
                    <option value="sent" {{ request()->order_status === 'sent' ? 'selected' : '' }}>تم الإرسال</option>
                    <option value="postponed" {{ request()->order_status === 'postponed' ? 'selected' : '' }}>تم التأجيل
                    </option>
                    <option value="no_response" {{ request()->order_status === 'no_response' ? 'selected' : '' }}>لا يرد
                    </option>
                    <option value="exchanged" {{ request()->order_status === 'exchanged' ? 'selected' : '' }}>تم استبداله
                    </option>
                    <option value="returned" {{ request()->order_status === 'returned' ? 'selected' : '' }}>تم استرجاعه
                    </option>
                    <option value="rejected_with_phone"
                        {{ request()->order_status === 'rejected_with_phone' ? 'selected' : '' }}>تم الإلغاء برقم الهاتف
                    </option>
                    <option value="rejected_in_shipping"
                        {{ request()->order_status === 'rejected_in_shipping' ? 'selected' : '' }}>تم الإلغاء أثناء الشحن
                    </option>
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
                <select name="shipping_company_id" class="select">
                    <option value="all" {{ request()->shipping_company_id === 'all' ? 'selected' : '' }}>جميع شركات
                        الشحن</option>
                    @foreach ($shipping_companies as $company)
                        <option value="{{ $company->id }}"
                            {{ request()->shipping_company_id == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                @if ($products->count() > 0)
                    <select id="custom_js" name="products_ids[]" multiple placeholder="اختر منتوجات..." autocomplete="off"
                        style="width: 70%">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ in_array($product->id, request()->products_ids ?? []) ? 'selected' : '' }}>
                                {{ $product->name }} - {{ $product->code }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <input type="text" id="dateRange" placeholder="اختر من - إلى" class="input"
                    value="{{ request()->from && request()->to ? request()->from . ' إلى ' . request()->to : '' }}" />
                <div class="flex gap-3 items-center" style="width: 550px">
                    <span>السعر</span>
                    <input type="number" name="min_price" class="select" min="0" placeholder="من"
                        value="{{ request()->min_price }}">
                    <input type="number" name="max_price" class="select" min="0" placeholder="إلى"
                        value="{{ request()->max_price }}">
                </div>
            </div>
            <div class="flex gap-3">
                <input type="search" class="input" name="search" id="searchInput" placeholder="ابحث عن الطلبات..."
                    value="{{ request()->search }}">
                <button type="submit" id="searchBtn" class="btn btn-primary flex justify-center max-w-56">بحث</button>
                @if (request()->has('search'))
                    <a href="{{ route('orders.index') }}" id="resetLink"
                        class="btn btn-secondary flex justify-center max-w-56">تراجع</a>
                @endif
            </div>
        </form>
    @endif
    <div class="card min-w-full mb-4">
        <div class="card-header">
            <h3 class="card-title">
                @if (request()->search)
                    نتائج البحث عن "{{ request()->search }}"
                @else
                    الطلبات
                @endif
            </h3>
            <div class="flex items-center gap-5">
                <div class="menu" data-menu="true">
                    <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-start"
                        data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                            <i class="ki-filled ki-dots-vertical">
                            </i>
                        </button>
                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('orders.create') }}">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-add-files"></i>
                                    </span>
                                    <span class="menu-title">
                                        إضافة
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('orders.export') }}">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-file-up"></i>
                                    </span>
                                    <span class="menu-title">
                                        تصدير كل الطلبات كملف excel
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($orders->count() > 0)
            <div class="card-table scrollable-x-auto">
                <div class="scrollable-auto">
                    <table class="table align-middle text-2sm text-gray-600" id="companies-table">
                        <tr class="bg-gray-100">
                            <th class="text-start font-medium">#</th>
                            <th class="text-start font-medium">المنتوجات</th>
                            <th class="text-start font-medium">رابط الطلب</th>
                            <th class="text-start font-medium">اسم المشتري</th>
                            <th class="text-start font-medium">رقم الهاتف</th>
                            <th class="text-start font-medium">المدينة</th>
                            <th class="text-start font-medium">تاريخ الطلب</th>
                            <th class="text-start font-medium">سعر الطلب</th>
                            <th class="text-start font-medium">رقم التتبع</th>
                            <th class="text-start font-medium">حالة الطلب</th>
                            <th class="text-start font-medium">حالة الدفع</th>
                            <th class="min-w-16">التحكم</th>
                        </tr>
                        @foreach ($orders as $order)
                            <tr
                                @if ($order->blockedNumbers->count()) class="blocked text-nowrap" style="background-color:#f77c7c; color:#fff" @else class="text-nowrap" @endif>
                                <td>
                                    {{ $order->id }}
                                </td>
                                <td>
                                    @foreach ($order->products as $product)
                                        {{ $product->code }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($order->url)
                                        @php
                                            $host = parse_url($order->url, PHP_URL_HOST); // www.trendow.com
                                            $host = explode('.', str_replace('www.', '', $host))[0]; // trendow

                                            $path = trim(parse_url($order->url, PHP_URL_PATH), '/'); // products/P65
                                            $parts = explode('/', $path);
                                        @endphp
                                        <a href="{{ $order->url }}" target="_blank" title="{{ $order->url }}"
                                            style="color: rgb(129, 129, 251); text-decoration: underline;">
                                            {{ $host }}/{{ $parts[1] ?? '' }} </a>
                                    @else
                                        ______
                                    @endif
                                </td>
                                <td>
                                    {{ explode(' ', $order->name)[0] }}
                                </td>
                                <td>
                                    {{ $order->phone() }}
                                </td>
                                <td>
                                    {{ explode('/', $order->city)[0] }}
                                </td>
                                <td>
                                    {{ $order->created_at->format('H:i - Y/m/d') }}
                                </td>
                                <td>
                                    {{ $order->total }}AED
                                </td>
                                <td>
                                    <form action="{{ route('orders.changeTrackingNumber', $order->id) }}"
                                        type="change_tracking_number" method="POST" style="margin: 0">
                                        @csrf
                                        <span class="tracking_number cursor-pointer underline no-select">
                                            @if ($order->tracking_number)
                                                {{ $order->tracking_number }}
                                            @else
                                                ______
                                            @endif
                                        </span>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('orders.changeOrderStatus', $order->id) }}"
                                        type="change_order_status" method="POST" style="margin: 0">
                                        @csrf
                                        {{ $order->status() }}
                                    </form>
                                </td>
                                <td>
                                    @if ($order->paid)
                                        <form action="{{ route('orders.changePaymentStatus', $order->id) }}"
                                            style="margin: 0" type="change_payment_status" method="POST">
                                            @csrf
                                            <span class="payment_status status no-select"
                                                style="background-color: rgb(69, 187, 69); color: #fff">تم</span>
                                        </form>
                                    @else
                                        <form action="{{ route('orders.changePaymentStatus', $order->id) }}"
                                            style="margin: 0" method="POST" type="change_payment_status">
                                            @csrf
                                            <span class="payment_status status no-select"
                                                style="background-color: rgb(255, 72, 72); color: #fff">لم يتم</span>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <div class="menu inline-flex" data-menu="true">
                                        <div class="menu-item" data-menu-item-offset="0, 10px"
                                            data-menu-item-placement="bottom-end"
                                            data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown"
                                            data-menu-item-trigger="click|lg:click">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical">
                                                </i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[175px]"
                                                data-menu-dismiss="true">
                                                <div class="menu-item">
                                                    <a class="menu-link" href="{{ route('orders.show', $order->id) }}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-search-list">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            التفاصيل
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-separator">
                                                </div>
                                                <div class="menu-item">
                                                    <a class="menu-link" href="{{ route('orders.edit', $order->id) }}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            تعديل
                                                        </span>
                                                    </a>
                                                </div>
                                                @can('access-delete-any-thing')
                                                    <form class="menu-item delete-form" type="delete_order"
                                                        action="{{ route('orders.destroy', $order->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" class="id" value="{{ $order->id }}">
                                                        <button class="menu-link">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-trash">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                حذف
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @if ($order->blockedNumbers->count())
                                                    <form class="menu-item unblock-number" type="unblock_number"
                                                        action="{{ route('blocked_numbers.destroy') }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" class="number"
                                                            value="{{ $order->phone }}">
                                                        <button class="menu-link">
                                                            <span class="menu-icon">
                                                                <i class="fas fa-ban"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                رفع الحظر
                                                            </span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form class="menu-item block-number" type="block_number"
                                                        action="{{ route('blocked_numbers.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" class="number"
                                                            value="{{ $order->phone }}">
                                                        <button class="menu-link">
                                                            <span class="menu-icon">
                                                                <i class="fas fa-ban" style="color: rgb(255, 95, 95)"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                حظر الرقم
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="card-footer justify-center">
                {{ $orders->links() }}
            </div>
        @elseif(request()->has('search'))
            <h5 class="p-3 text-center">لا توجد نتائج بحث</h5>
        @else
            <h5 class="p-3 text-center">لا توجد طلبات</h5>
        @endif
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
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

        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "ar"
        });

        function bindEvents() {
            document.querySelectorAll(".delete-form").forEach(form => {
                form.removeEventListener("submit", deleteHandler);
                form.addEventListener("submit", deleteHandler);
            });

            document.querySelectorAll(".block-number").forEach(form => {
                form.removeEventListener("submit", blockNumberHandler);
                form.addEventListener("submit", blockNumberHandler);
            });

            document.querySelectorAll(".unblock-number").forEach(form => {
                form.removeEventListener("submit", unBlockNumberHandler);
                form.addEventListener("submit", unBlockNumberHandler);
            });

            document.querySelectorAll(".payment_status").forEach(status => {
                status.addEventListener("dblclick", changePaymentStatusHandler)
            })

            document.querySelectorAll(".order_status").forEach(status => {
                status.addEventListener("dblclick", changeOrderStatusHandler)
            })

            document.querySelectorAll(".tracking_number").forEach(number => {
                number.addEventListener("click", changeTrackingNumberHandler)
            })

            document.getElementById("searchForm").addEventListener("submit", (e) => {
                const dates = e.target.querySelector("#dateRange").value.split(" إلى ");
                e.target.querySelector('[name="from"]').value = dates[0] ?? '';
                e.target.querySelector('[name="to"]').value = dates[1] ?? '';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            bindEvents();
            handleResetAndsearchBtns();
        });

        function handleResetAndsearchBtns() {
            const searchInput = document.getElementById("searchInput");
            const searchBtn = document.getElementById("searchBtn");
            const resetLink = document.getElementById("resetLink");
            const dateRangeInput = document.getElementById("dateRange");

            const upsellSelect = document.querySelector('select[name="upsell"]');
            const productSelect = document.querySelector('select[name="products_ids[]"]');
            const citySelect = document.querySelector('select[name="city"]');
            const shippingSelect = document.querySelector('select[name="shipping_company_id"]');
            const paidSelect = document.getElementById("paidSelect");
            const statusSelect = document.getElementById("statusSelect");
            const minPriceInput = document.querySelector('input[name="min_price"]');
            const maxPriceInput = document.querySelector('input[name="max_price"]');

            const originalSearch = @json(request()->search ?? '');
            const originalFrom = @json(request()->from);
            const originalTo = @json(request()->to);
            const originalUpsell = @json(request()->upsell ?? 'all');
            const originalProduct = @json(request()->product_id ?? 'all');
            const originalCity = @json(request()->city ?? 'all');
            const originalShipping = @json(request()->shipping_company_id ?? 'all');
            const originalPaid = @json(request()->paid ?? 'all');
            const originalStatus = @json(request()->order_status ?? '');
            const originalMinPrice = @json(request()->min_price ?? '');
            const originalMaxPrice = @json(request()->max_price ?? '');

            const originalDateRange = (originalFrom && originalTo) ? `${originalFrom} إلى ${originalTo}` : '';

            function updateButton() {
                const currentSearch = searchInput.value.trim();
                const currentDateRange = dateRangeInput.value.trim();
                const currentMinPrice = minPriceInput.value.trim();
                const currentMaxPrice = maxPriceInput.value.trim();
                const selectedProducts = productSelect ? Array.from(productSelect.selectedOptions).map(opt => opt.value)
                    .sort() : null;

                const hasAnyFilter =
                    currentSearch !== '' ||
                    currentDateRange !== '' ||
                    currentMinPrice !== '' ||
                    currentMaxPrice !== '' ||
                    upsellSelect.value !== 'all' ||
                    citySelect.value !== 'all' ||
                    shippingSelect.value !== 'all' ||
                    paidSelect.value !== 'all' ||
                    statusSelect.value !== '' ||
                    selectedProducts?.length > 0;

                if (resetLink) {
                    if (hasAnyFilter) resetLink.classList.remove('hidden');
                    else resetLink.classList.add('hidden');
                }

                const originalProducts = @json(request()->products_ids ?? []).sort();

                const allSame =
                    currentSearch === originalSearch.trim() &&
                    currentDateRange === originalDateRange &&
                    currentMinPrice === originalMinPrice &&
                    currentMaxPrice === originalMaxPrice &&
                    upsellSelect.value === originalUpsell &&
                    citySelect.value === originalCity &&
                    shippingSelect.value === originalShipping &&
                    paidSelect.value === originalPaid &&
                    statusSelect.value === originalStatus &&
                    arraysEqual(selectedProducts, originalProducts);

                if (allSame || !hasAnyFilter) {
                    searchBtn.classList.add('hidden');
                } else {
                    searchBtn.classList.remove('hidden');
                }
            }

            function arraysEqual(arr1, arr2) {
                if (arr1?.length !== arr2?.length) return false;
                return arr1?.every(val => arr2?.includes(val));
            }

            searchInput.addEventListener("input", updateButton);
            dateRangeInput.addEventListener("input", updateButton);
            upsellSelect.addEventListener("change", updateButton);
            citySelect.addEventListener("change", updateButton);
            shippingSelect.addEventListener("change", updateButton);
            paidSelect.addEventListener("change", updateButton);
            statusSelect.addEventListener("change", updateButton);
            productSelect?.addEventListener("change", updateButton);
            minPriceInput.addEventListener("input", updateButton);
            maxPriceInput.addEventListener("input", updateButton);

            updateButton();
        }



        function deleteHandler(e) {
            e.preventDefault();
            const form = e.target;
            Swal.fire({
                title: "هل حقا تريد حذف هذا الطلب ؟",
                showCancelButton: true,
                cancelButtonText: 'إلغاء',
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "حذف",
            }).then((result) => {
                if (result.isConfirmed) {
                    sendRequest("DELETE", form, "حدث خطأ أثناء حذف الطلب");
                }
            });
        }

        function blockNumberHandler(e) {
            e.preventDefault();
            const form = e.target;
            const phone = form.querySelector(".number").value;
            Swal.fire({
                title: "هل حقا تريد حظر هذا الرقم ؟",
                showCancelButton: true,
                cancelButtonText: 'إلغاء',
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "حظر",
            }).then((result) => {
                if (result.isConfirmed) {
                    sendRequest("POST", form, "حدث خطأ أثناء حظر الرقم", {
                        phone
                    }).then(() => {
                        document.querySelector("table").querySelectorAll("span.phone").forEach(
                            span => {
                                const row = span.closest("tr");
                                if (span.innerText === phone) {
                                    row.style.backgroundColor = "#f77c7c";
                                    row.style.color = "#fff";
                                    const blockNumberForm = row.querySelector('.block-number');
                                    if (blockNumberForm) {
                                        blockNumberForm.remove();
                                        const unBlockForm = document.createElement("div");
                                        unBlockForm.innerHTML = `
                                    <form class="menu-item unblock-number" type="unblock_number"
                                        action="{{ route('blocked_numbers.destroy') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" class="number" value="${phone}">
                                        <button class="menu-link">
                                            <span class="menu-icon">
                                                <i class="fas fa-ban"></i>
                                            </span>
                                            <span class="menu-title">
                                                رفع الحظر
                                            </span>
                                        </button>
                                    </form>
                                `;
                                        row.querySelector(".menu-dropdown").append(unBlockForm);
                                        bindEvents();
                                    }
                                }
                            });
                    });
                }
            });
        }

        function unBlockNumberHandler(e) {
            e.preventDefault();
            const form = e.target;
            const phone = form.querySelector(".number").value;
            sendRequest("DELETE", form, "حدث خطأ أثناء رفع الحظر", {
                phone
            }).then(() => {
                document.querySelector("table").querySelectorAll("span.phone").forEach(span => {
                    if (span.innerText === phone) {
                        const row = span.closest("tr");
                        row.style.backgroundColor = "";
                        row.style.color = "";
                        const unBlockNumberForm = row.querySelector('.unblock-number');
                        if (unBlockNumberForm) {
                            unBlockNumberForm.remove();
                            const blockForm = document.createElement("div");
                            blockForm.innerHTML = `
                            <form class="menu-item block-number" type="block_number"
                                action="{{ route('blocked_numbers.store') }}" method="POST">
                                @csrf
                                <input type="hidden" class="number" value="${phone}">
                                <button class="menu-link">
                                    <span class="menu-icon">
                                        <i class="fas fa-ban" style="color: rgb(255, 95, 95)"></i>
                                    </span>
                                    <span class="menu-title">
                                        حظر الرقم
                                    </span>
                                </button>
                            </form>
                        `;
                            row.querySelector(".menu-dropdown").append(blockForm);
                            bindEvents();
                        }
                    }
                });
            });
        }

        function changePaymentStatusHandler(e) {
            e.preventDefault();
            Swal.fire({
                title: "هل تريد تغيير حالة الدفع ؟",
                showCancelButton: true,
                cancelButtonText: 'إلغاء',
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "أجل",
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = e.target.closest("form");
                    sendRequest("POST", form, "حدث خطأ أثناء تغيير حالة الدفع");
                }
            })
        }

        function changeOrderStatusHandler(e) {
            e.preventDefault();

            Swal.fire({
                title: "اختر الحالة",
                input: "select",
                inputOptions: {
                    'waiting_for_confirmation': 'بانتظار التأكيد',
                    'waiting_for_shipping': 'بانتظار الشحن',
                    'received': 'تم الاستلام',
                    'sent': 'تم الإرسال',
                    'postponed': 'تم التأجيل',
                    'no_response': 'لا يرد',
                    "exchanged": "تم استبداله",
                    'rejected_with_phone': 'تم الإلغاء برقم الهاتف',
                    'rejected_in_shipping': 'تم الإلغاء أثناء الشحن',
                },
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "تغيير",
                cancelButtonText: "إلغاء",
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (!result.isConfirmed) return;

                const status = result.value;
                const form = e.target.closest("form");

                if (status === "postponed") {
                    Swal.fire({
                        title: "حدد تاريخ التأجيل",
                        html: `<input type="text" id="postpone-date" class="swal2-input" placeholder="اختر التاريخ">`,
                        didOpen: () => {
                            flatpickr("#postpone-date", {
                                minDate: "today",
                                dateFormat: "Y-m-d",
                            });
                        },
                        showCancelButton: true,
                        confirmButtonText: "تأكيد",
                        cancelButtonText: "إلغاء",
                        preConfirm: () => {
                            const date = document.getElementById('postpone-date').value;
                            if (!date) {
                                Swal.showValidationMessage("يرجى اختيار تاريخ التأجيل");
                            }
                            return date;
                        }
                    }).then((dateResult) => {
                        if (!dateResult.isConfirmed) return;

                        sendRequest("POST", form, "حدث خطأ أثناء تغيير حالة الطلب", {
                            status: status,
                            date: dateResult.value
                        });
                    });

                } else {
                    sendRequest("POST", form, "حدث خطأ أثناء تغيير حالة الطلب", {
                        status: status
                    });
                }
            });
        }

        function changeTrackingNumberHandler(e) {
            e.preventDefault()
            Swal.fire({
                title: "اكتب رقم التتبع",
                input: "text",
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "تغيير",
                cancelButtonText: "إلغاء",
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = e.target.closest("form");
                    sendRequest("POST", form, "حدث خطأ أثناء تعديل رقم التتبع", {
                        tracking_number: result.value
                    });
                }
            });
        }

        function sendRequest(method, form, errMsg, dataOfBody = null) {
            const type = form.getAttribute('type');
            const token = form.querySelector("[name='_token']").value;
            const url = form.action;

            return fetch(url, {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method,
                    body: dataOfBody ? JSON.stringify(dataOfBody) : null
                })
                .then(res => res.json())
                .then((data) => {
                    if (data.success && data.message) {
                        toastify().success(data.message);
                        switch (type) {
                            case "delete_order":
                                form.closest('tr').remove();
                                break;
                            case "change_payment_status":
                                let paymentStatus = form.closest('tr').querySelector('.payment_status');
                                let isPaymentWasDone = paymentStatus.innerText.trim() == "تم";
                                console.log(paymentStatus.innerText)
                                paymentStatus.style.backgroundColor = isPaymentWasDone ? "rgb(255, 72, 72)" :
                                    "rgb(69, 187, 69)";
                                paymentStatus.innerText = isPaymentWasDone ? "لم يتم" :
                                    "تم";
                                break;
                            case "change_tracking_number":
                                form.closest("tr").querySelector(".tracking_number").innerText = dataOfBody
                                    .tracking_number ? dataOfBody.tracking_number : "______";
                                break
                            case "change_order_status":
                                const orderStatus = form.closest("tr").querySelector(".order_status");
                                let status = dataOfBody.status;
                                let bg = "rgb(157 157 157)";
                                let color = "#fff";
                                switch (status) {
                                    case "waiting_for_confirmation":
                                        status = "بانتظار التأكيد"
                                        break;
                                    case "waiting_for_shipping":
                                        status = "بانتظار الشحن";
                                        bg = "rgb(58 146 223)";
                                        break;
                                    case "received":
                                        status = "تم الاستلام";
                                        bg = "#4caf50";
                                        break;
                                    case "sent":
                                        status = "تم الإرسال";
                                        bg = "#9c27b0";
                                        break;
                                    case "postponed":
                                        status = "تم التأجيل";
                                        bg = "#ff701f";
                                        break;
                                    case "no_response":
                                        status = "لا يرد";
                                        bg = "#dfcc29";
                                        color = '#000';
                                        break;
                                    case "rejected_with_phone":
                                        status = "تم الإلغاء بالهاتف";
                                        bg = "#6e1f1f";
                                        color = "#fff";
                                        break;
                                    case "rejected_in_shipping":
                                        status = "تم الإلغاء في الشحن";
                                        bg = "#a94442";
                                        break;
                                    case "exchanged":
                                        status = "تم استبداله";
                                        bg = "#ffc0cb";
                                        color = "#000";
                                        break;
                                    default:
                                        break;
                                }
                                orderStatus.innerText = status;
                                orderStatus.style.color = color;
                                orderStatus.style.backgroundColor = bg;
                                break;
                            default:
                                break;
                        }
                    } else {
                        toastify().error(data.message || errMsg);
                        throw new Error(data.message || errMsg);
                    }
                });

        }
    </script>
@endsection
