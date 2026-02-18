@extends('layouts.app')

@section('url_pages')
    <span>
        <a href="{{ route('home') }}" style="color:rgb(114,114,255)">لوحة التحكم</a>
    </span>
    <i class="ki-filled ki-left"></i>
    <span>البكسلات</span>
@endsection

@php
    use App\Models\Pixel;
@endphp

@section('content')
    <div class="card min-w-full mb-5">
        <div class="card-header flex items-center justify-between">
            <h3 class="card-title">البكسلات (Tracking Pixels)</h3>

            <a href="{{ route('pixels.create') }}" class="btn btn-light hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                إضافة بكسل <i class="fas fa-plus ms-2"></i>
            </a>
        </div>
    </div>

    <div class="card min-w-full">
        <div class="card-table scrollable-x-auto">
            <table class="table align-middle text-2sm text-gray-600">
                @if ($pixels->count())
                    <thead class="bg-gray-100">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>معرف البكسل</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-center">التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pixels as $pixel)
                            <tr>
                                <td>{{ $pixel->id }}</td>

                                <td>
                                    <div class="font-semibold">{{ $pixel->name }}</div>
                                </td>

                                <td>
                                    <span class="badge badge-primary">
                                        {{ Pixel::getTypes()[$pixel->type] ?? $pixel->type }}
                                    </span>
                                </td>

                                <td>
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                                        {{ $pixel->pixel_id }}
                                    </code>
                                </td>

                                <td>
                                    <span class="badge {{ $pixel->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $pixel->is_active ? 'مفعل' : 'معطل' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $pixel->created_at->format('Y-m-d') }}
                                </td>

                                <td class="text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('pixels.edit', $pixel) }}" class="btn btn-sm btn-light">
                                            <i class="ki-filled ki-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light text-danger"
                                            onclick="deletePixel({{ $pixel->id }})">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </thead>
                @else
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">
                            لا توجد بكسلات مضافة
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        @if ($pixels->hasPages())
            <div class="card-footer">
                {{ $pixels->links() }}
            </div>
        @endif
    </div>

    <form id="delete-pixel-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('script')
    <script>
        function deletePixel(id) {
            if (confirm('هل أنت متأكد من حذف هذا البكسل؟')) {
                const form = document.getElementById('delete-pixel-form');
                form.action = `/pixels/${id}`;
                form.submit();
            }
        }
    </script>
@endsection
