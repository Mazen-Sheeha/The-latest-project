<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عروض مميزة</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Almarai', sans-serif;
        }


        @keyframes pulse-soft {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .animate-pulse-soft {
            animation: pulse-soft 2s infinite ease-in-out;
        }

        /* Custom scrollbar for product list */
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #e2e2e2;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased font-sans">

    <div class="w-full max-w-[520px] min-h-screen bg-white mx-auto shadow-2xl flex flex-col relative">

        {{-- Top Progress/Status Bar (Visual Only) --}}
        <div class="h-1.5 w-full bg-gray-100 flex">
            <div class="h-full bg-[{{ $page->theme_color }}] w-2/3"></div>
            <div class="h-full bg-gray-200 w-1/3"></div>
        </div>

        {{-- Header --}}
        <div class="py-8 px-6 text-center bg-white">
            <div
                class="inline-block px-4 py-1.5 mb-3 rounded-full bg-[{{ $page->theme_color }}]/10 text-[{{ $page->theme_color }}] text-xs font-bold tracking-wide uppercase">
                وفر أكثر مع هذه العروض
            </div>
            <h2 class="text-2xl font-black text-gray-800 leading-tight">
                أضف لطلبك ووفر التوصيل!
            </h2>
            <p class="text-gray-500 text-sm mt-2">
                اختر المنتجات التي تريد إضافتها بخصم حصري الآن
            </p>
        </div>

        {{-- Form Start --}}
        <form method="POST" action="{{ route('pages.submitOrderFromUpsellPage') }}"
            class="flex flex-col flex-grow overflow-hidden">
            @csrf

            {{-- Hidden Fields: Logic Preserved --}}
            <input type="hidden" name="page_id" value="{{ $page->id }}">
            @if ($order)
                <input type="hidden" name="full_name" value="{{ $order->name }}">
                <input type="hidden" name="phone" value="{{ $order->phone }}">
                <input type="hidden" name="government" value="{{ $order->city }}">
                <input type="hidden" name="address" value="{{ $order->address }}">
            @else
                <input type="hidden" name="full_name" value="{{ $orderData['full_name'] ?? '' }}">
                <input type="hidden" name="phone" value="{{ $orderData['phone'] ?? '' }}">
                <input type="hidden" name="government" value="{{ $orderData['government'] ?? '' }}">
                <input type="hidden" name="address" value="{{ $orderData['address'] ?? '' }}">
                <input type="hidden" name="quantity" value="{{ $orderData['quantity'] ?? 1 }}">
                <input type="hidden" name="offer_price" value="{{ $offerPrice ?? $page->product->price }}">
            @endif

            {{-- Scrollable Products List --}}
            <div class="px-6 pb-6 space-y-4 flex-grow overflow-y-auto custom-scroll bg-white">
                @foreach ($page->upsellProducts as $product)
                    <label
                        class="group relative flex gap-4 items-center border-2 rounded-2xl p-3 cursor-pointer transition-all duration-300 bg-white hover:shadow-lg border-gray-100 has-[:checked]:border-[{{ $page->theme_color }}] has-[:checked]:bg-[{{ $page->theme_color }}]/5 hover:border-gray-200">

                        {{-- Hidden Checkbox --}}
                        <input type="checkbox" name="selected_upsell_products[]" value="{{ $product->id }}"
                            class="peer hidden">

                        {{-- Product Image --}}
                        <div
                            class="relative w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden bg-gray-50 border border-gray-100">
                            @php
                                $imgSrc = $product->pivot->image
                                    ? asset($product->pivot->image)
                                    : ($product->image
                                        ? asset($product->image)
                                        : asset('images/productDefault.webp'));
                            @endphp
                            <img src="{{ $imgSrc }}" alt="{{ $product->pivot->name ?? $product->name }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                            <div
                                class="absolute top-1 right-1 bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded shadow-sm">
                                خصم خاص
                            </div>
                        </div>

                        {{-- Product Info --}}
                        <div class="flex-1 min-w-0">
                            <h3
                                class="text-base font-bold text-gray-800 line-clamp-1 group-hover:text-[{{ $page->theme_color }}] transition-colors">
                                {{ $product->pivot->name ?? $product->name }}
                            </h3>

                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xl font-black text-[{{ $page->theme_color }}]">
                                    {{ number_format($product->pivot->price ?? $product->price, 2) }}
                                    <span class="text-[10px] font-normal text-gray-500">د.إ</span>
                                </span>
                            </div>

                            <div class="mt-2 flex items-center justify-between">
                                <span
                                    class="inline-flex items-center text-[10px] font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                    متوفر في المخزن
                                </span>

                                {{-- Plus / Checkmark Toggle UI --}}
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-gray-200 flex items-center justify-center transition-all duration-300
                        peer-checked:bg-[{{ $page->theme_color }}] peer-checked:border-[{{ $page->theme_color }}]
                        group-hover:border-[{{ $page->theme_color }}]">

                                    {{-- Plus Sign (Visible when NOT checked) --}}
                                    <svg class="w-5 h-5 text-[{{ $page->theme_color }}] peer-checked:hidden"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>

                                    {{-- Checkmark (Visible ONLY when checked) --}}
                                    <svg class="w-5 h-5 text-white hidden peer-checked:block" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- Action Buttons (Sticky at Bottom) --}}
            <div
                class="border-t border-gray-100 bg-white p-6 space-y-3 mt-auto shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)]">
                <button type="submit"
                    class="w-full bg-[{{ $page->theme_color }}] transition-all hover:brightness-110 active:scale-[0.98] text-white py-4 rounded-2xl font-black text-lg shadow-lg shadow-[{{ $page->theme_color }}]/30 animate-pulse-soft">
                    تأكيد وإضافة للطلب
                </button>

                <button type="button" onclick="skipUpsell()"
                    class="w-full bg-white border border-gray-200 text-gray-400 py-3 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-colors">
                    لا شكراً، أكمل طلبي الحالي فقط
                </button>
            </div>
        </form>
    </div>

    {{-- Success Overlay: Improved UI --}}
    @if (request()->query('success'))
        <div id="successOverlay"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[999] flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-8 text-center max-w-sm w-full shadow-2xl animate-scale-in">
                <div
                    class="mx-auto mb-6 w-20 h-20 rounded-full bg-green-50 flex items-center justify-center border-4 border-white shadow-lg">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="3"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-2">طلبك وصل!</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">
                    شكراً لثقتك بنا. سيقوم فريقنا بمراجعة الطلب والتواصل معك قريباً.
                </p>
                <button onclick="closeSuccessOverlay()"
                    class="w-full bg-gray-900 text-white font-bold py-4 rounded-2xl text-lg hover:bg-black transition-all">
                    فهمت
                </button>
            </div>
        </div>
    @endif

    <script>
        // Clean URL after success
        if (window.location.search.includes('success=1')) {
            const url = new URL(window.location.href);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url.pathname);
        }

        function closeSuccessOverlay() {
            document.getElementById('successOverlay')?.remove();
        }

        function skipUpsell() {
            // Uncheck everything before submit to ensure "Skip" logic works
            document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
            document.querySelector('form').submit();
        }
    </script>
</body>

</html>
