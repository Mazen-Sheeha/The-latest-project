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
                اختر المنتجات التي تريد إضافتها إلى طلبك
            </p>
        </div>

        {{-- Form Start --}}
        <form method="POST" action="{{ route('pages.submitOrderFromUpsellPage') }}" class="flex flex-col h-screen">
            @csrf

            {{-- Hidden Fields for Order Data --}}
            <input type="hidden" name="page_id" value="{{ $page->id }}">

            @if ($order)
                {{-- If we have an existing order --}}
                <input type="hidden" name="full_name" value="{{ $order->name }}">
                <input type="hidden" name="phone" value="{{ $order->phone }}">
                <input type="hidden" name="government" value="{{ $order->city }}">
                <input type="hidden" name="address" value="{{ $order->address }}">
            @else
                {{-- Otherwise use session order data --}}
                <input type="hidden" name="full_name" value="{{ $orderData['full_name'] ?? '' }}">
                <input type="hidden" name="phone" value="{{ $orderData['phone'] ?? '' }}">
                <input type="hidden" name="government" value="{{ $orderData['government'] ?? '' }}">
                <input type="hidden" name="address" value="{{ $orderData['address'] ?? '' }}">
                <input type="hidden" name="quantity" value="{{ $orderData['quantity'] ?? 1 }}">
                <input type="hidden" name="offer_price" value="{{ $offerPrice ?? $page->product->price }}">
            @endif

            {{-- Products --}}
            <div class="p-6 space-y-4 flex-grow overflow-y-auto">
                @foreach ($page->upsellProducts as $product)
                    <label
                        class="flex gap-4 items-start border rounded-2xl shadow-sm p-4 cursor-pointer hover:border-[{{ $page->theme_color }}] transition bg-white">

                        {{-- Checkbox --}}
                        <input type="checkbox" name="selected_upsell_products[]" value="{{ $product->id }}"
                            class="mt-2 w-5 h-5 cursor-pointer accent-[{{ $page->theme_color }}]">

                        {{-- Product Content --}}
                        <div class="flex-1 space-y-2">
                            {{-- Product Image --}}
                            <div class="h-32 bg-gray-100 rounded-lg overflow-hidden">
                                @if ($product->pivot->image)
                                    <img src="{{ asset($product->pivot->image) }}" alt="{{ $product->pivot->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <img src="{{ $product->image ? asset($product->image) : asset('images/productDefault.webp') }}"
                                        alt="{{ $product->pivot->name }}" class="w-full h-full object-cover">
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold">
                                    {{ $product->pivot->name ?? $product->name }}
                                </h3>

                                <div class="flex items-center justify-between">
                                    <span class="text-[{{ $page->theme_color }}] text-xl font-extrabold">
                                        {{ number_format($product->pivot->price ?? $product->price, 2) }} د.إ
                                    </span>

                                    <span class="text-sm text-gray-500">
                                        عرض خاص
                                    </span>
                                </div>
                            </div>
                        </div>

                    </label>
                @endforeach
            </div>

            {{-- Action Buttons (Sticky) --}}
            <div class="border-t bg-white p-6 space-y-3">
                <button type="submit"
                    class="w-full bg-[{{ $page->theme_color }}] transition text-white py-3 rounded-xl font-bold text-lg">
                    تأكيد الطلب
                </button>

                <button type="button" onclick="skipUpsell()"
                    class="w-full bg-gray-200 transition text-gray-800 py-3 rounded-xl font-bold text-lg hover:bg-gray-300">
                    تجاوز العروض
                </button>
            </div>
        </form>

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

    function skipUpsell() {
        // Submit the form without any selected products
        document.querySelector('form').submit();
    }
</script>

</html>
