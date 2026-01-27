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
        الدومينات
    </span>
@endsection
@section('content')
    <div class="card min-w-full mb-5">
        <div class="card-header flex items-center justify-between">
            <h3 class="card-title">دومينات الصفحات (host domains)</h3>

            <a href="{{ route('domains.create') }}" class="btn btn-light hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                إضافة دومين جديد <i class="fas fa-plus ms-2"></i>
            </a>
        </div>
    </div>

    {{-- <div class="card-body p-6">
            <form action="{{ route('domains.store') }}" method="post" id="add-domain" class="flex flex-col gap-6">
                @csrf
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex gap-2 flex-1">
                        <div class="flex items-center">
                            <h5 class="text-gray-800 font-semibold whitespace-nowrap">الدومين <span
                                    class="text-red-500">*</span></h5>
                        </div>
                        <div class="flex items-center relative flex-1">
                            <input type="text" class="input w-full @error('domain') border-red-500 @enderror"
                                name="domain" placeholder="example.com" value="{{ old('domain') }}">
                            @error('domain')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex gap-2 flex-1">
                        <div class="flex items-center">
                            <h5 class="text-gray-800 font-semibold whitespace-nowrap">الحالة <span
                                    class="text-red-500">*</span></h5>
                        </div>
                        <div class="flex items-center relative flex-1">
                            <select name="status" class="input w-full @error('status') border-red-500 @enderror">
                                <option value="">اختر الحالة</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                            </select>
                            @error('status')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-gray-800 font-semibold mb-4">طريقة الإعداد <span class="text-red-500">*</span></h4>

                    <div class="flex flex-col gap-4">
                        <!-- Wildcard Option -->
                        <label
                            class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                            onclick="updateSetupType('wildcard')">
                            <input type="radio" name="setup_type" value="wildcard"
                                {{ old('setup_type') == 'wildcard' || !old('setup_type') ? 'checked' : '' }} class="mt-1">
                            <div class="flex-1">
                                <h5 class="text-gray-800 font-semibold">Wildcard Domain (*.trendocp.com)</h5>
                                <p class="text-gray-600 text-sm mt-1">استخدام الدومين كـ Wildcard بدون الحاجة لتعديل DNS.
                                    سيتم حفظ الدومين كـ <code
                                        class="bg-gray-200 px-2 py-1 rounded">domain.trendocp.com</code>
                                </p>
                            </div>
                        </label>

                        <!-- DNS Record Option -->
                        <label
                            class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                            onclick="updateSetupType('dns_record')">
                            <input type="radio" name="setup_type" value="dns_record"
                                {{ old('setup_type') == 'dns_record' ? 'checked' : '' }} class="mt-1">
                            <div class="flex-1">
                                <h5 class="text-gray-800 font-semibold">DNS Record للنسخ واللصق</h5>
                                <p class="text-gray-600 text-sm mt-1">سيتم إنشاء سجل DNS جاهز للنسخ واللصق في لوحة التحكم
                                    الخاصة بك (مثل Hostinger)</p>
                            </div>
                        </label>
                    </div>
                    @error('setup_type')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex gap-2 flex-1">
                        <div class="flex items-center">
                            <h5 class="text-gray-800 font-semibold whitespace-nowrap">IP التحقق</h5>
                        </div>
                        <div class="flex items-center relative flex-1">
                            <input type="text" class="input w-full @error('verification_ip') border-red-500 @enderror"
                                name="verification_ip" placeholder="192.168.1.1" value="{{ old('verification_ip') }}">
                            @error('verification_ip')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex items-center">
                        <button type="submit"
                            class="btn btn-light hover:bg-blue-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                            إضافة <i class="fas fa-plus me-2 mr-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div> --}}

    <div class="card min-w-full">
        <div class="card-header">
            <h3 class="card-title">
                قائمة الدومينات
            </h3>
        </div>
        <div class="overflow-x-auto ps-8">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            الرقم
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            الدومين
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            الحالة
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            طريقة الإعداد
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            IP التحقق
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($domains as $domain)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $domain->id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                {{ $domain->domain }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $domain->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $domain->status == 'active' ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($domain->setup_type === 'wildcard')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        Wildcard
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        DNS Record
                                    </span>
                                @endif
                            </td>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $domain->verification_ip ?? 'غير محدد' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('domains.show', $domain->id) }}"
                                        class="btn btn-sm btn-info text-white px-3 py-1 rounded transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('domains.edit', $domain->id) }}"
                                        class="btn btn-sm btn-primary text-white px-3 py-1 rounded transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" onclick="deleteDomain({{ $domain->id }})"
                                        class="btn btn-sm btn-danger text-white px-3 py-1 rounded transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                لا توجد دومينات حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($domains->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $domains->links() }}
            </div>
        @endif
    </div>

    <script>
        function deleteDomain(domainId) {
            if (confirm('هل أنت متأكد من حذف هذا الدومين؟')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/domains/' + domainId;
                form.innerHTML = '@csrf @method('DELETE')';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
