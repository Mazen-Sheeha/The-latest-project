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
            <h3 class="card-title"> عملاء لم يكملوا الطلب</h3>
            @if ($cartUsers->count() > 0)
                <form method="POST" action="{{ route('cart-users.destroyAll') }}"
                    onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        حذف الكل
                    </button>
                </form>
            @endif
        </div>

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
                            {{-- <th class="text-start font-medium">Offer Price</th>
                            <th class="text-start font-medium">Quantity</th> --}}
                            <th class="text-start font-medium">اخر تحديث</th>
                            <th class="text-start font-medium">التحكم</th>
                        </tr>
                        @foreach ($cartUsers as $cartUser)
                            <tr class="text-nowrap">
                                <td>{{ $cartUser->id }}</td>
                                <td>{{ $cartUser->full_name ?: '-' }}</td>
                                <td>{{ $cartUser->phone ?: '-' }}</td>
                                <td>{{ $cartUser->government ?: '-' }}</td>
                                <td>{{ $cartUser->address ?: '-' }}</td>
                                <td>{{ $cartUser->page?->title ?: '-' }}</td>
                                {{-- <td>{{ is_null($cartUser->offer_price) ? '-' : number_format((float) $cartUser->offer_price, 2) }}
                                </td>
                                <td>{{ $cartUser->quantity ?: '-' }}</td> --}}
                                <td>{{ $cartUser->updated_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('cart-users.destroy', $cartUser->id) }}"
                                        onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $cartUsers->links() }}
            </div>
        @else
            <div class="card-body py-10 text-center text-gray-500">
                لا يوجد
            </div>
        @endif
    </div>
@endsection
