<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ممكن يعجبك</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <div class="w-full max-w-[520px] min-h-screen bg-white mx-auto shadow-xl">

        {{-- Header --}}
        <div class="py-6 border-b text-center">
            <h2 class="text-2xl font-extrabold">
                ممكن يعجبك كمان
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                ضيف منتجات بسعر مميز على طلبك
            </p>
        </div>

        {{-- Products --}}
        <div class="p-6 space-y-6">
            @foreach ($page->upsellProducts as $product)
                <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">

                    {{-- Product Image --}}
                    <div class="h-48 bg-gray-100">
                        <img src="{{ $product->image ? asset($product->image) : asset('images/productDefault.webp') }}"
                            alt="{{ $product->name }}" class="w-full h-full object-cover">
                    </div>

                    {{-- Product Info --}}
                    <div class="p-4 space-y-3">
                        <h3 class="text-lg font-bold">
                            {{ $product->name }}
                        </h3>

                        <div class="flex items-center justify-between">
                            <span class="text-[{{ $page->theme_color }}] text-xl font-extrabold">
                                {{ $product->price }} د.إ
                            </span>

                            <span class="text-sm text-gray-500">
                                عرض خاص
                            </span>
                        </div>

                        {{-- Add To Order --}}
                        <form method="POST" action="{{ route('pages.submitOrderFromUpsellPage', $product->id) }}">
                            @csrf

                            <input type="hidden" name="page_id" value="{{ $page->id }}">
                            <input type="hidden" name="full_name" value="{{ $order->name }}">
                            <input type="hidden" name="phone" value="{{ $order->phone }}">
                            <input type="hidden" name="government" value="{{ $order->city }}">
                            <input type="hidden" name="address" value="{{ $order->address }}">

                            <button type="submit"
                                class="w-full mt-4 bg-[{{ $page->theme_color }}] transition text-white py-3 rounded-xl font-bold text-lg">
                                أضف إلى الطلب
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    @if (request()->query('success'))
        <div id="successOverlay" class="fixed inset-0 bg-black/80 z-[999] flex items-center justify-center">

            <div class="bg-white rounded-2xl p-8 text-center max-w-sm w-full mx-4 animate-scale-in">

                {{-- Check Icon --}}
                <div class="mx-auto mb-4 w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" stroke-width="3"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">
                    تم استلام طلبك
                </h2>

                <p class="text-gray-600 mb-6">
                    سيتم التواصل معك في أقرب وقت لتأكيد الطلب
                </p>

                <button onclick="closeSuccessOverlay()"
                    class="w-full bg-green-600 hover:bg-green-700 transition
                       text-white font-bold py-3 rounded-xl text-lg">
                    تمام
                </button>
            </div>
        </div>
    @endif
</body>
<script>
    if (window.location.search.includes('success=1')) {
        const url = new URL(window.location.href);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url.pathname);
    }

    function closeSuccessOverlay() {
        document.getElementById('successOverlay')?.remove();
    }
</script>

</html>
