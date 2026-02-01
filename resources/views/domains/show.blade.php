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
        <a href="{{ route('domains.index') }}" style="color:rgb(114, 114, 255);">
            الدومينات
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        عرض الدومين
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="card-header">
            <h3 class="card-title">
                تفاصيل الدومين
            </h3>
        </div>
        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm font-semibold mb-2">الدومين</label>
                    <div class="text-gray-900 font-medium text-lg">{{ $domain->domain }}</div>
                </div>

                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm font-semibold mb-2">الحالة</label>
                    <div>
                        <span
                            class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $domain->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $domain->status == 'active' ? 'نشط' : 'معطل' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm font-semibold mb-2">IP التحقق</label>
                    <div class="text-gray-900 font-medium">{{ $domain->verification_ip ?? 'غير محدد' }}</div>
                </div>

                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm font-semibold mb-2">طريقة الإعداد</label>
                    <div class="text-gray-900 font-medium">
                        @if ($domain->setup_type === 'wildcard')
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                Wildcard Domain
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                DNS Record
                            </span>
                        @endif
                    </div>
                </div>

                @if ($domain->setup_type === 'dns_record' && $domain->dns_record)
                    @php
                        $dns = json_decode($domain->dns_record, true);
                    @endphp

                    <div class="flex flex-col col-span-2">
                        <label class="text-gray-600 text-sm font-semibold mb-3">
                            DNS Records
                        </label>

                        @foreach ($dns['records'] as $record)
                            <div
                                class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-3 flex justify-between items-center">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Type</span>
                                        <div class="font-semibold">{{ $record['type'] }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Name</span>
                                        <div class="font-semibold">{{ $record['host'] }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Points to</span>
                                        <div class="font-semibold">{{ $record['value'] }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">TTL</span>
                                        <div class="font-semibold">{{ $record['ttl'] }}</div>
                                    </div>
                                </div>
                                <button onclick="copyDNS('{{ $record['host'] }} {{ $record['value'] }}')"
                                    class="btn btn-sm btn-secondary ml-4">
                                    Copy
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm font-semibold mb-2">تاريخ الإنشاء</label>
                    <div class="text-gray-900 font-medium">{{ $domain->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm font-semibold mb-2">آخر تعديل</label>
                    <div class="text-gray-900 font-medium">{{ $domain->updated_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm font-semibold mb-2">عدد الصفحات</label>
                    <div class="text-gray-900 font-medium">{{ $domain->pages()->count() }}</div>
                </div>
            </div>
        </div>
        <div class="card-footer p-6 border-t border-gray-200 flex gap-2">
            <a href="{{ route('domains.edit', $domain->id) }}"
                class="btn btn-primary text-white px-4 py-2 rounded transition-colors">
                <i class="fas fa-edit me-2 mr-2"></i>تعديل
            </a>
            <a href="{{ route('domains.index') }}"
                class="btn btn-secondary text-white px-4 py-2 rounded transition-colors">
                <i class="fas fa-arrow-left me-2 mr-2"></i>العودة
            </a>
        </div>
    </div>

    @if ($domain->pages()->exists())
        <div class="card bg-white shadow-sm rounded-lg border border-gray-200 mt-6">
            <div class="card-header">
                <h3 class="card-title">
                    الصفحات المرتبطة
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الرقم</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">اسم الصفحة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Slug</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($domain->pages as $page)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $page->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $page->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $page->slug }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $page->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $page->is_active ? 'مفعلة' : 'معطلة' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script>
        function copyDNS(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('DNS record copied successfully');
            }).catch(() => {
                alert('Copy failed, please copy manually');
            });
        }
    </script>
@endsection
