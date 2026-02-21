<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عروض مميزة</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">


    {{-- Tracking Pixels --}}
    @php
        $pixels = $page->pixels->where('is_active', true)->groupBy('type');
    @endphp

    {{-- META --}}
    @foreach ($pixels->get('meta', collect()) as $pixel)
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window,
                document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $pixel->pixel_id }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $pixel->pixel_id }}&ev=PageView&noscript=1" /></noscript>
    @endforeach

    {{-- GOOGLE ADS --}}
    @foreach ($pixels->get('google_ads', collect()) as $pixel)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $pixel->pixel_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $pixel->pixel_id }}');
        </script>
    @endforeach

    {{-- GOOGLE ANALYTICS --}}
    @foreach ($pixels->get('google_analytics', collect()) as $pixel)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $pixel->pixel_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $pixel->pixel_id }}');
        </script>
    @endforeach

    {{-- TIKTOK --}}
    @foreach ($pixels->get('tiktok', collect()) as $pixel)
        @if ($pixel->code)
            {!! $pixel->code !!}
        @else
            <script>
                ! function(w, d, t) {
                    w.TiktokAnalyticsObject = t;
                    var ttq = w[t] = w[t] || [];
                    ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias",
                        "group", "enableCookie", "disableCookie"
                    ], ttq.setAndDefer = function(t, e) {
                        t[e] = function() {
                            t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
                        }
                    };
                    for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
                    ttq.instance = function(t) {
                        for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
                        return e
                    }, ttq.load = function(e, n) {
                        var i = "https://analytics.tiktok.com/i18n/pixel/events.js";
                        ttq._i = ttq._i || {}, ttq._i[e] = [], ttq._i[e]._u = i, ttq._t = ttq._t || {}, ttq._t[e] = +new Date,
                            ttq._o = ttq._o || {}, ttq._o[e] = n || {};
                        n = document.createElement("script");
                        n.type = "text/javascript", n.async = !0, n.src = i + "?sdkid=" + e + "&lib=" + t;
                        e = document.getElementsByTagName("script")[0];
                        e.parentNode.insertBefore(n, e)
                    };
                    ttq.load('{{ $pixel->pixel_id }}');
                    ttq.page();
                }(window, document, 'ttq');
            </script>
        @endif
    @endforeach

    {{-- SNAPCHAT --}}
    @foreach ($pixels->get('snapchat', collect()) as $pixel)
        @if ($pixel->code)
            {!! $pixel->code !!}
        @else
            <script>
                (function(e, t, n) {
                    if (e.snaptr) return;
                    var a = e.snaptr = function() {
                        a.handleRequest ? a.handleRequest.apply(a, arguments) : a.queue.push(arguments)
                    };
                    a.queue = [];
                    var s = 'script';
                    r = t.createElement(s);
                    r.async = !0;
                    r.src = n;
                    var u = t.getElementsByTagName(s)[0];
                    u.parentNode.insertBefore(r, u);
                })
                (window, document, 'https://sc-static.net/scevent.min.js');
                snaptr('init', '{{ $pixel->pixel_id }}');
                snaptr('track', 'PAGE_VIEW');
            </script>
        @endif
    @endforeach

    {{-- TWITTER --}}
    @foreach ($pixels->get('twitter', collect()) as $pixel)
        @if ($pixel->code)
            {!! $pixel->code !!}
        @else
            <script>
                ! function(e, t, n, s, u, a) {
                    e.twq || (s = e.twq = function() {
                            s.exe ? s.exe.apply(s, arguments) : s.queue.push(arguments);
                        }, s.version = '1.1', s.queue = [], u = t.createElement(n), u.async = !0, u.src =
                        'https://static.ads-twitter.com/uwt.js', a = t.getElementsByTagName(n)[0], a.parentNode.insertBefore(u,
                            a))
                }(window, document, 'script');
                twq('config', '{{ $pixel->pixel_id }}');
            </script>
        @endif
    @endforeach

    {{-- OTHER / CUSTOM --}}
    @foreach ($pixels->get('other', collect()) as $pixel)
        @if ($pixel->code)
            {!! $pixel->code !!}
        @endif
    @endforeach

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
                @php
                    $isSingle = $page->upsellProducts->count() === 1;
                @endphp

                {{-- LIMITED TIME OFFER STATEMENT --}}
                <div class="mb-1">
                    <span
                        class="inline-flex items-center gap-1 text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md font-bold {{ $isSingle ? 'text-sm mb-2' : 'text-[10px]' }} animate-pulse">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd"></path>
                        </svg>
                        عرض لفترة محدودة
                    </span>
                </div>
                @foreach ($page->upsellProducts as $product)
                    <label onclick="toggleProduct(this)"
                        class="product-card group relative border-2 rounded-2xl cursor-pointer
    transition-all duration-300 bg-white hover:shadow-lg border-gray-100 hover:border-gray-200
    {{ $isSingle ? 'flex flex-col items-center text-center p-6' : 'flex gap-4 items-center p-3' }}">


                        <input type="checkbox" name="selected_upsell_products[]" value="{{ $product->id }}"
                            class="hidden product-checkbox" {{ $isSingle ? 'checked' : '' }}>

                        {{-- image --}}
                        <div
                            class="relative {{ $isSingle ? 'w-44 h-44 mb-4' : 'w-24 h-24' }} flex-shrink-0 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">

                            @php
                                $imgSrc = $product->pivot->image
                                    ? asset($product->pivot->image)
                                    : ($product->image
                                        ? asset($product->image)
                                        : asset('images/productDefault.webp'));
                            @endphp
                            <img src="{{ $imgSrc }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                            <div
                                class="absolute top-1 right-1 bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded shadow-sm">
                                خصم خاص
                            </div>
                        </div>

                        <div class="{{ $isSingle ? 'w-full text-center' : 'flex-1 min-w-0' }}">

                            <h3
                                class="text-base font-bold text-gray-800 line-clamp-1 product-title {{ $isSingle ? 'text-lg mt-2' : '' }}">
                                {{ $product->pivot->name ?? $product->name }}
                            </h3>

                            <div class="flex items-center {{ $isSingle ? 'justify-center' : '' }} gap-2 mt-1">
                                <span class="text-xl font-black text-[{{ $page->theme_color }}]">
                                    {{ number_format($product->pivot->price ?? $product->price, 2) }}
                                    <span class="text-[10px] font-normal text-gray-500">د.إ</span>
                                </span>
                            </div>

                            <div
                                class="flex items-center {{ $isSingle ? 'justify-between gap-4 mt-3' : 'justify-between mt-1' }}">

                                <p class="text-[11px] font-bold text-green-600 opacity-0 product-added">
                                    ✓ تمت إضافة المنتج إلى طلبك
                                </p>

                                <div
                                    class="plus-btn w-8 h-8 rounded-full border-2 border-gray-300
                flex items-center justify-center transition-all duration-300">

                                    <svg class="w-5 h-5 text-gray-400 plus-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4"></path>
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

    <script>
        function toggleProduct(card) {

            const checkbox = card.querySelector('.product-checkbox');
            const plusBtn = card.querySelector('.plus-btn');
            const plusIcon = card.querySelector('.plus-icon');
            const addedText = card.querySelector('.product-added');
            const title = card.querySelector('.product-title');

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                card.style.borderColor = "{{ $page->theme_color }}";
                card.style.background = "{{ $page->theme_color }}10";

                // plusBtn.style.background = "{{ $page->theme_color }}";
                plusBtn.style.borderColor = "{{ $page->theme_color }}";
                plusIcon.style.color = "{{ $page->theme_color }}";

                addedText.style.opacity = "1";
                title.style.color = "{{ $page->theme_color }}";

            } else {
                card.style.borderColor = "#f3f4f6";
                card.style.background = "white";

                plusBtn.style.background = "transparent";
                plusBtn.style.borderColor = "#d1d5db";
                plusIcon.style.color = "#9ca3af";

                addedText.style.opacity = "0";
                title.style.color = "#1f2937";
            }
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const isSingle = {{ $isSingle ? 'true' : 'false' }};
            if (!isSingle) return;

            const card = document.querySelector('.product-card');
            if (!card) return;

            const checkbox = card.querySelector('.product-checkbox');
            const plusBtn = card.querySelector('.plus-btn');
            const plusIcon = card.querySelector('.plus-icon');
            const addedText = card.querySelector('.product-added');
            const title = card.querySelector('.product-title');

            checkbox.checked = true;

            card.style.borderColor = "{{ $page->theme_color }}";
            card.style.background = "{{ $page->theme_color }}10";

            plusBtn.style.borderColor = "{{ $page->theme_color }}";
            plusIcon.style.color = "{{ $page->theme_color }}";

            addedText.style.opacity = "1";
            title.style.color = "{{ $page->theme_color }}";
        });
    </script>

</body>

</html>
