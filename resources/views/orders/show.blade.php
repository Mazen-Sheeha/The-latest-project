@extends('layouts.app')
@section('style')
    <style>
        form:has(span.status) {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
        }

        .no-select {
            user-select: none;
        }

        .payment_status {
            width: 100% !important;
            padding: 2px 5px;
            border-radius: 5px;
            color: #fff;
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
        <a href="{{ route('orders.index') }}" style="color:rgb(114, 114, 255);">
            الطلبات
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        {{ $order->name }}
    </span>
@endsection
@section('content')
    <div class="card pb-2 5">
        <div class="card-body grid gap-5">
            <div class="flex gap-2">
                حالة الطلب :
                <form action="{{ route('orders.changeOrderStatus', $order->id) }}" type="change_order_status" method="POST"
                    style="margin: 0">
                    @csrf
                    {{ $order->status() }}
                </form>
            </div>
            <div class="flex gap-2">
                حالة الدفع :
                @if ($order->paid)
                    <form action="{{ route('orders.changePaymentStatus', $order->id) }}" style="margin: 0"
                        type="change_payment_status" method="POST">
                        @csrf
                        <span class="payment_status status no-select"
                            style="background-color: rgb(69, 187, 69); color: #fff">تم</span>
                    </form>
                @else
                    <form action="{{ route('orders.changePaymentStatus', $order->id) }}" style="margin: 0" method="POST"
                        type="change_payment_status">
                        @csrf
                        <span class="payment_status status no-select"
                            style="background-color: rgb(255, 72, 72); color: #fff">لم يتم</span>
                    </form>
                @endif
            </div>
            <div>
                سعر الطلب : {{ $total }}AED
            </div>
            <div>
                اسم المشتري : {{ $order->name }}
            </div>
            <div>
                رقم الهاتف : {{ $order->phone }} @if ($order->blockedNumbers->count() > 0)
                    (<span style="color: red">محظور</span>)
                @endif
            </div>
            <div>
                المدينة : {{ $order->city }}
            </div>
            <div>
                العنوان : {{ $order->address }}
            </div>
            <div>
                الرابط : <a href=" {{ $order->url }}" style="text-decoration: underline; color:blue">
                    {{ $order->url }}</a>
            </div>
            <div>
                رقم التتبع : {{ $order->tracking_number ?? '_____________' }}
            </div>
            <div>
                اسم الحملة : {{ $order->campaign->campaign ?? '_____________' }}
            </div>
            <div>
                مصدر الحملة : {{ $order->campaign->source ?? '_____________' }}
            </div>
            <div>
                سعر الشحن : {{ number_format($order->shipping_price, 1) }}AED
            </div>
            <div>
                ملاحظات على الطلب : {{ $order->notes ?? '_____________' }}
            </div>
            <div>
                المنتوجات :
                <div class="flex flex-col gap-3 py-2">
                    @foreach ($order->products as $product)
                        <div class="flex flex-wrap items-center gap-3 border ">
                            <img loading="lazy" src="{{ $product->image() }}"
                                style="object-fit: cover; height: 75px; width: 100px;" alt="لا توجد صورة">
                            <div class="flex gap-10">
                                <div class="flex flex-col gap-10">
                                    <span>{{ $product->code }}</span>
                                    <span>سعر البيع : {{ number_format($product->pivot->price, 1) }}AED</span>
                                </div>
                                <div class="flex flex-col gap-10">
                                    <span>{{ $product->name }}</span>
                                    <span>الإجمالي :
                                        {{ number_format($product->pivot->price * $product->pivot->quantity, 2) }}AED</span>
                                </div>
                                <div class="flex flex-col gap-10">
                                    <span>×{{ $product->pivot->quantity }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll(".payment_status").forEach(status => {
                status.addEventListener("dblclick", changePaymentStatusHandler)
            })

            document.querySelectorAll(".order_status").forEach(status => {
                status.addEventListener("dblclick", changeOrderStatusHandler)
            })
        });

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
                            case "change_payment_status":
                                let paymentStatus = form.querySelector('.payment_status');
                                let isPaymentWasDone = paymentStatus.innerText.trim() == "تم";
                                console.log(paymentStatus.innerText)
                                paymentStatus.style.backgroundColor = isPaymentWasDone ? "rgb(255, 72, 72)" :
                                    "rgb(69, 187, 69)";
                                paymentStatus.innerText = isPaymentWasDone ? "لم يتم" :
                                    "تم";
                                break;
                            case "change_order_status":
                                const orderStatus = form.querySelector(".order_status");
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
