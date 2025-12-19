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
        <a href="{{ route('products.index') }}" style="color:rgb(114, 114, 255) ;">
            المنتوجات
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        {{ $product->name }}
    </span>
@endsection
@section('content')
    @php
        $orders = $product->orders;
        $waiting_for_confirmation_count = $orders->where('order_status', 'waiting_for_confirmation')->count();
        $waiting_for_shipping_count = $orders->where('order_status', 'waiting_for_shipping')->count();
        $received_count = $orders->where('order_status', 'received')->count();
        $postponed_count = $orders->where('order_status', 'postponed')->count();
        $no_response_count = $orders->where('order_status', 'no_response')->count();
        $sent_count = $orders->where('order_status', 'sent')->count();
        $exchanged_count = $orders->where('order_status', 'exchanged')->count();
        $rejected_with_phone_count = $orders->where('order_status', 'rejected_with_phone')->count();
        $rejected_in_shipping_count = $orders->where('order_status', 'rejected_in_shipping')->count();
    @endphp
    <div class="card">
        <div class="card-header">
            تفاصيل المنتوج
        </div>
        <div class="card-body">
            <img src="{{ $product->image() }}" style="width: 100%; max-height: 500px; object-fit: cover" loading="lazy">
            <div class="flex justify-between mt-5">
                <div style="margin-top: 50px; display: flex; flex-direction: column; gap: 20px ">
                    <div>اسم المنتوج : {{ $product->name }}</div>
                    <div>سعر المنتوج : {{ $product->price }}</div>
                    <div>كود المنتوج : {{ $product->code }}</div>
                    <div>المخزون : {{ $product->stock }}</div>
                    <div>المبيعات : {{ $product->sales_number }}</div>
                    <div>المتبقي : {{ $product->stock - $product->sales_number }}</div>
                </div>
                @if ($product->orders->count() > 0)
                    <div class="card" style="width: 550px;justify-self: flex-end; align-self: flex-end">
                        <div class="card-body flex justify-center items-center px-3 py-1">
                            <div id="chart" class="w-full"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const data = [
                {{ $waiting_for_confirmation_count }},
                {{ $waiting_for_shipping_count }},
                {{ $received_count }},
                {{ $sent_count }},
                {{ $postponed_count }},
                {{ $no_response_count }},
                {{ $exchanged_count }},
                {{ $rejected_with_phone_count }},
                {{ $rejected_in_shipping_count }}
            ];
            const labels = [
                'بانتظار التـأكيد',
                'بانتظار الشحن',
                'تم الاستلام',
                'تم الإرسال',
                'تم التأجيل',
                'لا يرد',
                "تم استبداله",
                "تم الإلغاء بالهاتف",
                "تم الإلغاء في الشحن"
            ];
            const colors = ['#9d9d9d', '#1b84ff', '#17c653', '#7239ea', '#dfcc29', '#ff701f', '#f794a4', "#6e1f1f",
                '#a94442'
            ];

            const options = {
                series: data,
                labels: labels,
                colors: colors,
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
                chart: {
                    type: 'donut',
                    height: 350
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['#f9f9f9']
                },
                dataLabels: {
                    enabled: false
                },

                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                },
                legend: {
                    position: 'right',
                    fontSize: '13px',
                    fontWeight: '500',
                    labels: {
                        colors: '#4b5563'
                    },
                    markers: {
                        width: 8,
                        height: 8
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            const chartEl = document.querySelector("#chart");
            if (chartEl) {
                const chart = new ApexCharts(chartEl, options);
                chart.render();
            }
        });
    </script>
@endsection
