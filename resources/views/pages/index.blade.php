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

        {{-- ===== FILTERS ===== --}}
        <div class="card-body border-b pb-5">
            <form method="GET" action="{{ route('pages.index') }}" id="filter-form">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">

                    {{-- Search --}}
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <i class="ki-filled ki-magnifier absolute top-1/2 -translate-y-1/2 text-gray-400"
                                style="left: 10px"></i>
                            <input type="text" name="search" class="input w-full pr-9"
                                placeholder="بحث بالاسم أو العنوان..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Domain --}}
                    <div>
                        <select name="domain_id" class="input w-full"
                            onchange="document.getElementById('filter-form').submit()">
                            <option value="">كل الدومينات</option>
                            @foreach ($domains as $domain)
                                <option value="{{ $domain->id }}"
                                    {{ request('domain_id') == $domain->id ? 'selected' : '' }}>
                                    {{ $domain->domain }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product --}}
                    <div>
                        <select name="product_id" class="input w-full"
                            onchange="document.getElementById('filter-form').submit()">
                            <option value="">كل المنتجات</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pixel --}}
                    <div>
                        <select name="pixel_id" class="input w-full"
                            onchange="document.getElementById('filter-form').submit()">
                            <option value="">كل البكسلات</option>
                            @foreach ($pixels as $pixel)
                                <option value="{{ $pixel->id }}"
                                    {{ request('pixel_id') == $pixel->id ? 'selected' : '' }}>
                                    {{ $pixel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status + Reset row --}}
                    <div class="lg:col-span-2 flex gap-2">
                        <select name="is_active" class="input flex-1"
                            onchange="document.getElementById('filter-form').submit()">
                            <option value="">كل الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>منشورة</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير منشورة</option>
                        </select>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ki-filled ki-filter"></i>
                            بحث
                        </button>

                        @if (request()->hasAny(['search', 'domain_id', 'product_id', 'pixel_id', 'is_active']))
                            <a href="{{ route('pages.index') }}" class="btn btn-light px-4">
                                <i class="ki-filled ki-cross"></i>
                                مسح
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        {{-- Active filters summary --}}
        @if (request()->hasAny(['search', 'domain_id', 'product_id', 'pixel_id', 'is_active']))
            <div class="px-5 py-2 bg-blue-50 border-b flex items-center gap-2 flex-wrap text-sm text-blue-700">
                <i class="ki-filled ki-filter text-blue-500"></i>
                <span>فلاتر نشطة:</span>

                @if (request('search'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        بحث: {{ request('search') }}
                    </span>
                @endif

                @if (request('domain_id'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        الدومين: {{ $domains->firstWhere('id', request('domain_id'))?->domain }}
                    </span>
                @endif

                @if (request('product_id'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        المنتج: {{ $products->firstWhere('id', request('product_id'))?->name }}
                    </span>
                @endif

                @if (request('pixel_id'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        البكسل: {{ $pixels->firstWhere('id', request('pixel_id'))?->name }}
                    </span>
                @endif

                @if (request()->filled('is_active'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        الحالة: {{ request('is_active') == '1' ? 'منشورة' : 'غير منشورة' }}
                    </span>
                @endif

                <span class="text-gray-500 mr-auto">{{ $pages->total() }} نتيجة</span>
            </div>
        @endif
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
                                    @if ($page->domain)
                                        <div class="text-xs text-gray-400">{{ $page->domain->domain }}</div>
                                    @endif
                                </td>
                                <td>{{ $page->product?->name ?? '-' }}</td>
                                <td>
                                    @if ($page->original_price && !$page->sale_price)
                                        <span class="font-bold text-green-600">{{ number_format($page->original_price) }}
                                            د.إ</span>
                                    @endif
                                    @if ($page->original_price && $page->sale_price)
                                        <span class="font-bold text-green-600">{{ number_format($page->sale_price) }}
                                            د.إ</span>
                                        <div class="text-xs text-gray-400 line-through">
                                            {{ number_format($page->original_price) }} د.إ</div>
                                    @endif
                                </td>
                                <td>
                                    {{-- <form action="{{ route('pages.toggleActive', $page) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="badge cursor-pointer {{ $page->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $page->is_active ? 'منشورة' : 'غير منشورة' }}
                                        </button>
                                    </form> --}}
                                    <form action="{{ route('pages.toggleActive', $page) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <div style="position:relative; width:44px; height:24px;">
                                                <input type="checkbox"
                                                    style="opacity:0; width:0; height:0; position:absolute;"
                                                    onchange="this.closest('form').submit()"
                                                    {{ $page->is_active ? 'checked' : '' }} class="toggle-peer">
                                                <div
                                                    style="
                position: absolute;
                inset: 0;
                background-color: {{ $page->is_active ? '#22c55e' : '#d1d5db' }};
                border-radius: 9999px;
                transition: background-color 0.2s;
            ">
                                                    <div
                                                        style="
                    position: absolute;
                    top: 2px;
                    {{ $page->is_active ? 'left: 22px;' : 'left: 2px;' }}
                    width: 20px;
                    height: 20px;
                    background: white;
                    border-radius: 50%;
                    transition: left 0.2s;
                ">
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ $page->is_active ? 'منشورة' : 'غير منشورة' }}
                                            </span>
                                        </label>
                                    </form>
                                </td>
                                <td>
                                    @if ($page->is_active)
                                        <a href="{{ pageUrl($page) }}" target="_blank" class="text-blue-600 underline">
                                            {{ pageUrl($page) }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">غير متاحة</span>
                                    @endif
                                </td>
                                <td class="text-center">
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
                                                {{-- Edit --}}
                                                <div class="menu-item">
                                                    <a class="menu-link" href="{{ route('pages.edit', $page->id) }}">
                                                        <span class="menu-icon"><i class="ki-filled ki-pencil"></i></span>
                                                        <span class="menu-title">تعديل</span>
                                                    </a>
                                                </div>
                                                {{-- Duplicate --}}
                                                <div class="menu-item">
                                                    <form action="{{ route('pages.duplicate', $page->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="menu-link w-full">
                                                            <span class="menu-icon"><i
                                                                    class="ki-filled ki-copy"></i></span>
                                                            <span class="menu-title">تكرار</span>
                                                        </button>
                                                    </form>
                                                </div>
                                                {{-- Delete --}}
                                                <form class="menu-item delete-form"
                                                    action="{{ route('pages.destroy', $page->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="menu-link" type="submit">
                                                        <span class="menu-icon"><i class="ki-filled ki-trash"></i></span>
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
                        <td colspan="7" class="text-center p-10 text-gray-400">
                            @if (request()->hasAny(['search', 'domain_id', 'product_id', 'pixel_id', 'is_active']))
                                <div class="text-4xl mb-2">🔍</div>
                                <p>لا توجد نتائج تطابق الفلاتر المحددة</p>
                                <a href="{{ route('pages.index') }}"
                                    class="text-blue-500 text-sm hover:underline mt-1 inline-block">مسح الفلاتر</a>
                            @else
                                لا توجد صفحات بيع
                            @endif
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
