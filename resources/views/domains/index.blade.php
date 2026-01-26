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
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                أضف دومين جديد
            </h3>
        </div>
        <div class="card-body p-6">
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
        </div>
    </div>

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
