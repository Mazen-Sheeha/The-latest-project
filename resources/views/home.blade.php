@extends('layouts.app')
@section('url_pages')
    <span class="text-gray-700">
        لوحة التحكم
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        الصفحة الرئيسية
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                إحصائيات الطلبات
            </h3>
        </div>
        <div class="card-body">
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-body flex flex-col justify-end items-stretch grow px-3 py-1">
                        <div id="orders_chart">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-7.5 mt-7">
                    <div class="card p-5 lg:p-7.5 lg:pt-7">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-3">
                                <span class="text-base font-medium leading-none text-gray-900 ">
                                    {{ $stats['today'] }}
                                </span>
                                <span class="text-2sm leading-5" style="color:#438dcf;">
                                    عدد الطلبات هذا اليوم
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5 lg:p-7.5 lg:pt-7">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-3">
                                <span class="text-base font-medium leading-none text-gray-900 ">
                                    {{ $stats['this_week'] }}
                                </span>
                                <span class="text-2sm leading-5 text-gray-500" style="color:#438dcf;">
                                    عدد الطلبات هذا الأسبوع
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5 lg:p-7.5 lg:pt-7">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-3">
                                <span class="text-base font-medium leading-none text-gray-900">
                                    {{ $stats['this_month'] }}
                                </span>
                                <span class="text-2sm leading-5 text-gray-500" style="color:#438dcf;">
                                    عدد الطلبات هذا الشهر
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5 lg:p-7.5 lg:pt-7">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-3">
                                <span class="text-base font-medium leading-none text-gray-900 ">
                                    {{ $stats['total'] }}
                                </span>
                                <span class="text-2sm leading-5 text-gray-500" style="color:#438dcf;">
                                    عدد الطلبات الكلية
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const options = {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                    name: 'عدد الطلبات',
                    data: @json($dailyOrders),
                }],
                xaxis: {
                    categories: @json($daysLabels),
                    title: {
                        text: 'اليوم'
                    }
                },
                colors: ['#1E88E5'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                yaxis: {
                    title: {
                        text: 'عدد الطلبات'
                    },
                    labels: {
                        formatter: val => `${val}`
                    }
                },
                tooltip: {
                    x: {
                        formatter: function(val) {
                            return 'يوم ' + val;
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#orders_chart"), options);
            chart.render();
        });
    </script>
@endsection
