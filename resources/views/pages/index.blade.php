@extends('layouts.app')

@section('url_pages')
    <span>
        <a href="{{ route('home') }}" style="color:rgb(114,114,255)">لوحة التحكم</a>
    </span>
    <i class="ki-filled ki-left"></i>
    <span>صفحات البيع</span>
@endsection

@section('content')
    <div class="card min-w-full mb-5">
        <div class="card-header flex items-center justify-between">
            <h3 class="card-title">صفحات البيع (Landing Pages)</h3>

            <a href="{{ route('pages.create') }}" class="btn btn-light hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                إضافة صفحة بيع <i class="fas fa-plus ms-2"></i>
            </a>
        </div>
    </div>

    <div class="card min-w-full">
        <div class="card-table scrollable-x-auto">
            <table class="table align-middle text-2sm text-gray-600">
                @if ($pages->count())
                    <thead class="bg-gray-100">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>المنتج</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>رابط الصفحة</th>
                            <th class="text-center">التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <td>{{ $page->id }}</td>

                                <td>
                                    <div class="font-semibold">{{ $page->title }}</div>
                                </td>

                                <td>
                                    {{ $page->product?->name ?? '-' }}
                                </td>

                                <td>

                                    @if ($page->original_price && !$page->sale_price)
                                        <span class="font-bold text-green-600">
                                            {{ number_format($page->original_price) }} د.إ
                                        </span>
                                    @endif
                                    @if ($page->original_price && $page->sale_price)
                                        <span class="font-bold text-green-600">
                                            {{ number_format($page->sale_price) }} د.إ
                                        </span>
                                        <div class="text-xs text-gray-400 line-through">
                                            {{ number_format($page->original_price) }} د.إ
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <form action="{{ route('pages.toggleActive', $page->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="badge cursor-pointer {{ $page->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $page->is_active ? 'منشورة' : 'غير منشورة' }}
                                        </button>
                                    </form>
                                </td>

                                {{-- PUBLIC URL --}}
                                <td>
                                    @if ($page->is_active)
                                        <a href="{{ url('/buy/' . $page->slug) }}" target="_blank"
                                            class="text-blue-600 underline">
                                            عرض الصفحة
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M18 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6m4-4h4v4m0-4L10 14" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400">غير متاحة</span>
                                    @endif
                                </td>

                                {{-- ACTIONS --}}
                                <td class="text-center">
                                    <div class="menu inline-flex" data-menu="true">
                                        <div class="menu-item" data-menu-item-offset="0, 10px"
                                            data-menu-item-placement="bottom-end"
                                            data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown"
                                            data-menu-item-trigger="click|lg:click">

                                            {{-- Toggle button --}}
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical"></i>
                                            </button>

                                            {{-- Dropdown --}}
                                            <div class="menu-dropdown menu-default w-full max-w-[175px]"
                                                data-menu-dismiss="true">

                                                {{-- Edit --}}
                                                <div class="menu-item">
                                                    <a class="menu-link" href="{{ route('pages.edit', $page->id) }}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil"></i>
                                                        </span>
                                                        <span class="menu-title">تعديل</span>
                                                    </a>
                                                </div>

                                                {{-- Delete --}}
                                                <form class="menu-item delete-form"
                                                    action="{{ route('pages.destroy', $page->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="menu-link" type="submit">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-trash"></i>
                                                        </span>
                                                        <span class="menu-title">حذف</span>
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <tr>
                        <td colspan="7" class="text-center p-6">
                            لا توجد صفحات بيع
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="card-footer justify-center">
            {{ $pages->links() }}
        </div>
    </div>
@endsection
