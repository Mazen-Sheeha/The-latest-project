@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        th {
            padding: 7px !important;
        }

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

        .up {
            display: inline-block;
            transform: rotate(90deg);
        }

        .down {
            margin-right: 5px;
            display: inline-block;
            transform: rotate(-90deg)
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
        المنتوجات
    </span>
@endsection
@section('content')
    @php
        $productsCount = $products->count();
    @endphp
    @if (($productsCount > 0 && !request()->search) || request()->search || (request()->from && request()->to))
        <form action="{{ route('products.index') }}" method="GET" class="flex gap-1 flex-col flex-wrap" id="searchForm">
            <div class="flex gap-3">
                <input type="text" class="input" name="search" id="searchInput"
                    placeholder="ابحث عن المنتوجات بالاسم..." value="{{ request()->search }}">
                <input type="search" id="dateRange" placeholder="اختر من - إلى" class="input"
                    value="{{ request()->from && request()->to ? request()->from . ' إلى ' . request()->to : '' }}" />
                <input type="hidden" name="from" class="from" value="{{ request()->from }}">
                <input type="hidden" name="to" class="to" value="{{ request()->to }}">
                <button type="submit" id="searchBtn" class="btn btn-primary flex justify-center max-w-56">بحث</button>
                @if (request()->has('search'))
                    <a href="{{ route('products.index') }}" id="resetLink"
                        class="btn btn-secondary flex justify-center max-w-56">تراجع</a>
                @endif
            </div>
        </form>
    @endif
    <div class="card min-w-full mb-4">
        <div class="card-header">
            <h3 class="card-title">
                @if (request()->search || (request()->from && request()->to))
                    نتائج البحث
                    @if (request()->search)
                        عن "{{ request()->search }}"
                    @endif
                    @if (request()->from && request()->to)
                        من {{ request()->from }} إلى {{ request()->to }}
                    @endif
                @else
                    المنتوجات
                @endif
            </h3>
            <div class="flex items-center gap-5">
                <div class="menu" data-menu="true">
                    <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-start"
                        data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                            <i class="ki-filled ki-dots-vertical"></i>
                        </button>
                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('products.create') }}">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-add-files"></i>
                                    </span>
                                    <span class="menu-title">
                                        إضافة
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($productsCount > 0)
            <div class="card-table scrollable-x-auto">
                <div class="scrollable-auto">
                    <table class="table align-middle text-2sm text-gray-600" id="companies-table">
                        <tr class="bg-gray-100">
                            <th>نشط</th>
                            <th class="text-start font-medium">الصورة</th>
                            <th class="text-start font-medium">الاسم</th>
                            <th class="text-start font-medium">الكود</th>
                            <th class="text-start font-medium">المخزون</th>
                            <th class="text-start font-medium">المتبقي</th>
                            <th class="text-start font-medium">عدد الطلبات</th>
                            <th class="text-start font-medium" style="min-width: 100px">الطلبات الملغية</th>
                            <th class="text-start font-medium">بانتظار التأكيد
                            </th>
                            <th class="text-start font-medium">بانتظار الشحن</th>
                            <th class="text-start font-medium">تم الاستلام</th>
                            <th class="text-start font-medium">تم التأجيل</th>
                            <th class="text-start font-medium">لا يرد</th>
                            <th class="text-start font-medium">تم الإرسال</th>
                            <th class="text-start font-medium">تم الاستبدال</th>
                            <th class="text-start font-medium">نسبة التأكيد</th>
                            <th class="text-start font-medium">نسبة المبيعات</th>
                            <th>التحكم</th>
                        </tr>
                        @foreach ($products as $product)
                            @php
                                $orders = $product->orders;
                                $waiting_for_confirmation_count = $orders
                                    ->where('order_status', 'waiting_for_confirmation')
                                    ->count();
                                $waiting_for_shipping_count = $orders
                                    ->where('order_status', 'waiting_for_shipping')
                                    ->count();
                                $received_count = $orders->where('order_status', 'received')->count();
                                $postponed_count = $orders->where('order_status', 'postponed')->count();
                                $no_response_count = $orders->where('order_status', 'no_response')->count();
                                $sent_count = $orders->where('order_status', 'sent')->count();
                                $exchanged_count = $orders->where('order_status', 'exchanged')->count();
                                $rejected_with_phone_count = $orders
                                    ->where('order_status', 'rejected_with_phone')
                                    ->count();
                                $rejected_in_shipping_count = $orders
                                    ->where('order_status', 'rejected_in_shipping')
                                    ->count();
                                $confirmed_count =
                                    $orders->count() -
                                    $waiting_for_confirmation_count -
                                    $rejected_with_phone_count -
                                    $no_response_count;
                                $confirmation_rate =
                                    $orders->count() > 0 ? ($confirmed_count * 100) / $orders->count() : 0;
                                $delivery_rate =
                                    $confirmed_count > 0
                                        ? (($received_count + $exchanged_count) * 100) / $confirmed_count
                                        : 0;
                            @endphp
                            <tr>
                                <td>
                                    <form action="{{ route('products.changeStatus', $product->id) }}" method="POST"
                                        class="active-form">
                                        @csrf
                                        <button
                                            class="on-off @if ($product->active) on @else off @endif"></button>
                                    </form>
                                </td>
                                <td>
                                    @if ($product->image)
                                        <img loading="lazy" src="{{ $product->image() }}"
                                            style="min-width: 60px;max-height: 60px; min-height: 60px; max-width: 60px; object-fit: cover"
                                            alt="">
                                    @else
                                        ________
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->code }}</td>
                                <td class="stock">{{ $product->stock }}</td>
                                <td class="stock">{{ $product->stock - $product->sales_number }}</td>
                                <td>{{ $orders->count() }}</td>
                                <td class="flex flex-col gap-2" style="color: #fff">
                                    <span
                                        style="width:fit-content; background-color: #3498db; padding: 2px 7px; border-radius: 20px">الكل
                                        {{ $rejected_with_phone_count + $rejected_in_shipping_count }}</span>
                                    <span
                                        style="width:fit-content; background-color: #18bc9c; padding: 2px 7px; border-radius: 20px">بالهاتف
                                        {{ $rejected_with_phone_count }}</span>
                                    <span
                                        style="width:fit-content; background-color: #2c3e50; padding: 2px 7px; border-radius: 20px">بالشحن
                                        {{ $rejected_in_shipping_count }}</span>
                                </td>
                                <td style="color: #fff;background-color: rgb(157 157 157)">
                                    {{ $waiting_for_confirmation_count }}</td>
                                <td style="color: #fff;background-color: rgb(58 146 223)">{{ $waiting_for_shipping_count }}
                                </td>
                                <td style="color: #fff;background-color: #4caf50">{{ $received_count }}</td>
                                <td style="color: #fff;background-color: #ff701f">{{ $postponed_count }}</td>
                                <td style="color: #fff;background-color: #dfcc29">{{ $no_response_count }}</td>
                                <td style="color: #fff;background-color: #7239ea">{{ $sent_count }}</td>
                                <td style="color: #ffffff;background-color: #ffc0cb">{{ $exchanged_count }}</td>
                                <td>
                                    <span style="color: @if ($confirmation_rate >= 50) green @else red @endif"
                                        class="text-nowrap">
                                        % {{ number_format($confirmation_rate, 2) }}<span
                                            class="@if ($confirmation_rate >= 50) up @else down @endif">&gt;</span>
                                    </span>
                                </td>
                                <td>
                                    <span style="color: @if ($delivery_rate >= 50) green @else red @endif"
                                        class="text-nowrap">
                                        % {{ number_format($delivery_rate, 2) }} <span
                                            class="@if ($delivery_rate >= 50) up @else down @endif">&gt;</span>
                                    </span>
                                </td>
                                <td>
                                    <div class="menu inline-flex" data-menu="true">
                                        <div class="menu-item" data-menu-item-offset="0, 10px"
                                            data-menu-item-placement="bottom-end"
                                            data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown"
                                            data-menu-item-trigger="click|lg:click">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical"></i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[175px]"
                                                data-menu-dismiss="true">
                                                <div class="menu-item">
                                                    <a class="menu-link"
                                                        href="{{ route('products.show', $product->id) }}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-search-list"></i>
                                                        </span>
                                                        <span class="menu-title">التفاصيل</span>
                                                    </a>
                                                </div>
                                                <form class="menu-item increase-stock-form"
                                                    action="{{ route('products.increase_stock', $product->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" class="id" value="{{ $product->id }}">
                                                    <button class="menu-link" href="#">
                                                        <span class="menu-icon"><i class="ki-filled ki-plus"></i></span>
                                                        <span class="menu-title">إضافة مخزون</span>
                                                    </button>
                                                </form>
                                                <div class="menu-separator"></div>
                                                <div class="menu-item">
                                                    <a class="menu-link"
                                                        href="{{ route('products.edit', $product->id) }}">
                                                        <span class="menu-icon"><i class="ki-filled ki-pencil"></i></span>
                                                        <span class="menu-title">تعديل</span>
                                                    </a>
                                                </div>
                                                @can('access-delete-any-thing')
                                                    @if (!$product->hasOrders())
                                                        <form class="menu-item delete-form"
                                                            action="{{ route('products.destroy', $product->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" class="id"
                                                                value="{{ $product->id }}">
                                                            <button class="menu-link" href="#">
                                                                <span class="menu-icon"><i
                                                                        class="ki-filled ki-trash"></i></span>
                                                                <span class="menu-title">حذف</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="card-footer
                                        justify-center">
                {{ $products->links() }}
            </div>
        @else
            @if (request()->search)
                <h5 class="p-3 text-center">لا توجد نتائج بحث</h5>
            @else
                <h5 class="p-3 text-center">لا توجد منتوجات</h5>
            @endif
        @endif
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script>
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "ar"
        });

        document.getElementById("searchForm")?.addEventListener("submit", (e) => {
            const dates = e.target.querySelector("#dateRange").value.split(" إلى ");
            e.target.querySelector('[name="from"]').value = dates[0] ?? '';
            e.target.querySelector('[name="to"]').value = dates[1] ?? '';
        });

        document.querySelectorAll(".delete-form").forEach(form => {
            form.addEventListener("submit", function deleteHandler(e) {
                e.preventDefault();
                const form = e.target;
                Swal.fire({
                    title: "هل حقا تريد حذف هذا المنتوج ؟",
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "حذف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendRequest("DELETE", form, "حدث خطأ أثناء حذف المنتوج");
                    }
                });
            });
        });

        document.querySelectorAll(".active-form").forEach(form => {
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                sendRequest('POST', form, 'حدث خطأ أثناء تغيير حالة نشاط المنتوج');
            })
        })

        document.querySelectorAll(".increase-stock-form").forEach(form => {
            form.addEventListener("submit", e => {
                e.preventDefault();
                Swal.fire({
                    title: "أضف كمية لهذا المنتوج",
                    input: "number",
                    inputAttributes: {
                        autocapitalize: "off"
                    },
                    showCancelButton: true,
                    confirmButtonText: "أضف الكمية",
                    confirmButtonColor: "#1B84FF",
                    cancelButtonText: "إلغاء",
                    showLoaderOnConfirm: true,
                    preConfirm: async (quantity) => {
                        sendRequest("POST", form, "حدث خطأ أثناء إضافة المخزون", {
                            quantity
                        })
                    },
                });
            })
        });

        function sendRequest(method, form, errMsg, dataOfBody = null) {
            const token = form.querySelector("[name='_token']").value;
            const url = form.action;
            fetch(url, {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method,
                    body: method === "POST" && dataOfBody ? JSON.stringify(dataOfBody) : null
                })
                .then(res => res.json())
                .then((data) => {
                    if (data.success && data.message) {
                        toastify().success(data.message);
                        if (method === "DELETE") {
                            form.closest("tr").remove();
                        }
                        if (method === "POST") {
                            if (dataOfBody?.quantity) {
                                form.closest("tr").querySelectorAll(".stock").forEach(td => td.innerHTML = +td
                                    .textContent +
                                    +dataOfBody.quantity)
                            } else {
                                const btn = form.querySelector(".on-off")
                                const isOn = btn.classList.contains("on");
                                btn.classList.remove(isOn ? "on" : "off");
                                btn.classList.add(isOn ? "off" : "on");
                            }
                        }
                    } else {
                        toastify().error(data.message || errMsg);
                    }
                });
        }
    </script>
@endsection
