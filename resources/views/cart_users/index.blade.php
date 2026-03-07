@extends('layouts.app')

@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">لوحة التحكم</a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs"></i>
    <span class="text-gray-700">السلة المتروكة</span>
@endsection

@section('content')
    <div class="card min-w-full mb-4">
        <div class="card-header">
            <h3 class="card-title">عملاء لم يكملوا الطلب</h3>
            @if ($cartUsers->count() > 0)
                <form method="POST" action="{{ route('cart-users.destroyAll') }}"
                    onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">حذف الكل</button>
                </form>
            @endif
        </div>

        {{-- ===== FILTERS ===== --}}
        <div class="card-body border-b pb-4">
            <form method="GET" action="{{ route('cart_users.index') }}" class="flex flex-wrap gap-3 items-end">

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم أو هاتف..."
                        class="input input-sm w-48">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">صفحة المنتج</label>
                    <select name="page_id" class="input input-sm w-48">
                        <option value="">كل الصفحات</option>
                        @foreach ($pages as $page)
                            <option value="{{ $page->id }}" {{ request('page_id') == $page->id ? 'selected' : '' }}>
                                {{ $page->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">المدينة</label>
                    <select name="government" class="input input-sm w-40">
                        <option value="">اختر مدينة</option>
                        <option value="Abu Dhabi">Abu Dhabi / أبو ظبي</option>
                        <option value="Dubai">Dubai / دبي</option>
                        <option value="Sharjah">Sharjah / الشارقة</option>
                        <option value="Ajman">Ajman / عجمان</option>
                        <option value="Al Ain">Al Ain / العين</option>
                        <option value="Fujairah">Fujairah / الفجيرة</option>
                        <option value="Umm Al-Quwain">Umm Al-Quwain / أم القيوين</option>
                        <option value="Ras Al Khaimah">Ras Al Khaimah / رأس الخيمة</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input input-sm w-36">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs text-gray-500 font-medium">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input input-sm w-36">
                </div>

                {{-- Filter Actions --}}
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-filled ki-filter"></i>
                        بحث
                    </button>

                    @if (request()->hasAny(['search', 'page_id', 'government', 'date_from', 'date_to']))
                        <a href="{{ route('cart_users.index') }}" class="btn btn-light">
                            <i class="ki-filled ki-cross"></i>
                            مسح
                        </a>
                    @endif
                </div>
            </form>
        </div>
        {{-- Active Filters Summary --}}
        @if (request()->hasAny(['search', 'page_id', 'government', 'date_from', 'date_to']))
            <div class="px-5 py-2 bg-blue-50 border-b flex items-center gap-2 flex-wrap text-sm text-blue-700">
                <i class="ki-filled ki-filter text-blue-500"></i>
                <span>فلاتر نشطة:</span>

                @if (request('search'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        بحث: {{ request('search') }}
                    </span>
                @endif

                @if (request('page_id'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        الصفحة: {{ $pages->firstWhere('id', request('page_id'))?->title }}
                    </span>
                @endif

                @if (request('government'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        المدينة: {{ request('government') }}
                    </span>
                @endif

                @if (request('date_from'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        من: {{ request('date_from') }}
                    </span>
                @endif

                @if (request('date_to'))
                    <span class="bg-white border border-blue-200 rounded-full px-3 py-0.5">
                        إلى: {{ request('date_to') }}
                    </span>
                @endif

                <span class="text-gray-500 mr-auto">{{ $cartUsers->total() }} نتيجة</span>
            </div>
        @endif

        @if ($cartUsers->count() > 0)
            <div class="card-table scrollable-x-auto">
                <div class="scrollable-auto">
                    <table class="table align-middle text-2sm text-gray-600">
                        <tr class="bg-gray-100">
                            <th class="text-start font-medium">#</th>
                            <th class="text-start font-medium">الاسم</th>
                            <th class="text-start font-medium">رقم الهاتف</th>
                            <th class="text-start font-medium">المدينة</th>
                            <th class="text-start font-medium">العنوان</th>
                            <th class="text-start font-medium">صفحة المنتج</th>
                            <th class="text-start font-medium">اخر تحديث</th>
                            <th class="text-start font-medium">التحكم</th>
                        </tr>
                        @foreach ($cartUsers as $cartUser)
                            <tr class="text-nowrap"
                                style="background-color: {{ $cartUser->is_completed ? '#39BF52' : '#BF3939' }}; color:white">
                                <td>{{ $cartUser->id }}</td>
                                <td>{{ $cartUser->full_name ?: '-' }}</td>
                                <td>{{ $cartUser->phone ?: '-' }}</td>
                                <td>{{ $cartUser->government ?: '-' }}</td>
                                <td>{{ $cartUser->address ?: '-' }}</td>
                                <td>{{ $cartUser->page?->title ?: '-' }}</td>
                                <td>{{ $cartUser->updated_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>
                                    <div class="flex gap-2">

                                        @if ($cartUser->is_completed)
                                            <form method="POST"
                                                action="{{ route('cart-users.cancelOrder', $cartUser->id) }}"
                                                onsubmit="return confirm('هل تريد إلغاء إكمال الطلب؟')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning"
                                                    style="background-color:#f59e0b !important; color:white !important;">
                                                    إلغاء الطلب
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST"
                                                action="{{ route('cart-users.completeOrder', $cartUser->id) }}"
                                                onsubmit="return confirm('هل أنت متأكد من إكمال الطلب؟')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    إكمال الطلب
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('cart-users.destroy', $cartUser->id) }}"
                                            onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $cartUsers->appends(request()->query())->links() }}
            </div>
        @else
            <div class="card-body py-10 text-center text-gray-500">
                لا يوجد
            </div>
        @endif
    </div>
@endsection
