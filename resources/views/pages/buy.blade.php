<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->slug ?? 'Landing Page' }}</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">

    {{-- Tracking Pixels --}}
    @php
        $pixels = $page->pixels()->where('is_active', true)->get()->groupBy('type');
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
            fbq('track', 'ViewContent', {
                content_name: '{{ $page->title }}',
                content_ids: ['{{ $product->id }}'],
                content_type: 'product',
                value: {{ $page->sale_price ?? $page->original_price }},
                currency: 'AED'
            });
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
            gtag('event', 'view_item', {
                items: [{
                    item_id: '{{ $product->id }}',
                    item_name: '{{ $product->name }}',
                    price: {{ $page->sale_price ?? $page->original_price }}
                }]
            });
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

    {{-- OTHER / CUSTOM CODE --}}
    @foreach ($pixels->get('other', collect()) as $pixel)
        @if ($pixel->code)
            {!! $pixel->code !!}
        @endif
    @endforeach

    @php
        function hexToRgb($hex, $returnArray = false)
        {
            $hex = str_replace('#', '', $hex);
            if (strlen($hex) == 3) {
                $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
            } else {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
            }
            if ($returnArray) {
                return [$r, $g, $b];
            }

            return "$r,$g,$b";
        }

        function isLightColor($hex)
        {
            [$r, $g, $b] = hexToRgb($hex, true);
            // standard luminance formula
            $luminance = 0.299 * $r + 0.587 * $g + 0.114 * $b;
            return $luminance > 165; // Adjust threshold as needed
        }

        $contrastColor = isLightColor($page->theme_color) ? '#000000' : '#ffffff';
    @endphp

    <style>
        * {
            font-family: 'Almarai', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        .top-text {
            font-size: 14px;
            display: inline-block;
            transition: all .45s ease;
            opacity: 1;
            transform: translateX(0);
            white-space: nowrap;
            overflow: hidden;
            color: {{ $contrastColor }} !important;
        }

        /* fade out → to left */
        .top-text.fade-out {
            opacity: 0;
            transform: translateX(-40px);
        }

        /* prepare from right */
        .top-text.prepare-in {
            opacity: 0;
            transform: translateX(40px);
        }

        /* fade in ← from right */
        .top-text.fade-in {
            opacity: 1;
            transform: translateX(0);
        }

        .top-moving-banner {
            width: 100%;
            overflow: hidden;
            background-color: rgba({{ hexToRgb($page->theme_color) }}, 0.3);
            color: {{ isLightColor($page->theme_color) ? '#000' : $page->theme_color }};
            position: relative;
            font-weight: 700;
            font-size: 16px;
        }

        .moving-texts {
            display: flex;
            white-space: nowrap;
            position: absolute;
            left: -100%;
            animation: moveRight linear infinite;
        }

        .moving-texts span {
            margin-right: 50px;
        }

        @keyframes moveRight {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        .count-box {
            background: {{ $page->theme_color }};
            color: {{ $contrastColor }};
            font-size: 1.5rem;
            font-weight: 800;
            padding: 10px 12px;
            border-radius: 10px;
            min-width: 48px;
        }

        .label {
            display: block;
            margin-top: 4px;
            font-size: 0.8rem;
            color: {{ $page->theme_color }};
            font-weight: 600;
        }

        .colon {
            font-size: 1.5rem;
            font-weight: bold;
            color: {{ $page->theme_color }};
            margin-top: -18px;
        }

        .hide-sticky {
            transform: translateY(120%);
        }

        @keyframes scaleIn {
            0% {
                transform: scale(.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale-in {
            animation: scaleIn .35s ease-out forwards;
        }

        .overflow-text {
            width: 100%;
            overflow-wrap: anywhere;
        }

        .features-grid:has(> :last-child:nth-child(odd))> :last-child {
            grid-column: span 2;
        }

        .buy-popup {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translate(-50%, -20px);
            background: #000;
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            opacity: 0;
            pointer-events: none;
            transition: all .6s ease;
            z-index: 9999;
        }

        .buy-popup.show {
            opacity: 1;
            transform: translate(-50%, 0);
        }

        .whatsapp-float {
            position: fixed;
            /* This formula keeps it on the edge of your 520px container */
            right: calc(50% - 260px + 10px);
            bottom: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        /* For small screens, just pin it to the right */
        @media (max-width: 520px) {
            .whatsapp-float {
                right: 15px;
            }
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
        }

        .whatsapp-float .label {
            margin-right: 12px;
            font-weight: 800;
            font-size: 16px;
            /* Bigger Font */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background-color: white;
            padding: 12px 20px;
            /* Bigger Padding */
            border-radius: 50px;
            /* Rounded Pill Shape */
            color: #25D366;
            border: 1px solid #e2e2e2;
        }

        .whatsapp-float img {
            width: 55px;
            /* Bigger Icon */
            height: 55px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));
        }

        #phoneInput.border-red-500 {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }

        #phoneInput.border-green-500 {
            border-color: #22c55e !important;
        }
    </style>
</head>


<body class="bg-white text-gray-900" dir="rtl">
    <div class="w-full max-w-[520px] bg-white min-h-screen shadow-xl m-auto relative pb-24">
        @php
            $wa = $page->whatsapp_phone ?? null;
            $wa_clean = $wa ? preg_replace('/[^0-9]/', '', $wa) : '1234567890';
        @endphp

        {{-- WhatsApp Button - Now inside the relative container --}}
        @if ($wa)
            <a href="https://wa.me/{{ $wa_clean }}" target="_blank" class="whatsapp-float">
                <span class="label">تحدث معنا</span>
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
            </a>
        @endif

        @php
            $movingTexts = $page->moving_banner_text ?? [];
            if (!is_array($movingTexts)) {
                $movingTexts = [$movingTexts];
            }
            $movingTexts = array_filter($movingTexts);
        @endphp

        @if (!empty($movingTexts))
            <div class="top-moving-banner h-12 p-2">
                <div class="moving-texts" id="movingTexts">
                    @foreach ($movingTexts as $text)
                        <span>{{ $text }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <p id="buy-text" class="buy-popup"> </p>

        @if (!empty($page->images) && count($page->images))
            @php $firstImage = $page->images[0]; @endphp

            <button type="button" onclick="openImageModal('{{ asset($firstImage) }}')" class="focus:outline-none">
                <img src="{{ asset($firstImage) }}" class="w-full object-cover rounded-lg shadow">
            </button>
        @endif

        @php
            $topFeatureTexts = $page->top_feature_text ?? [];
            if (!is_array($topFeatureTexts)) {
                $topFeatureTexts = [$topFeatureTexts];
            }
            $topFeatureTexts = array_filter($topFeatureTexts);
        @endphp

        @if (!empty($topFeatureTexts))
            <div class="w-full p-4 bg-[{{ $page->theme_color }}] text-center overflow-hidden">
                <p id="topFeatureText" class="top-text transition-all duration-500"></p>
            </div>
        @endif

        {{-- HERO --}}
        <section class="bg-white px-4 pt-6 pb-4 border-b">
            <div class="max-w-[420px] mx-auto text-right space-y-3">

                {{-- TITLE --}}
                <h1 class="text-2xl font-extrabold leading-snug text-gray-900">
                    {{ $page->title }}
                </h1>

                {{-- PRICES --}}
                <div class="flex items-center gap-3 flex-wrap">

                    {{-- NEW PRICE --}}
                    <span class="text-2xl font-extrabold text-black">
                        {{ number_format($page->pageSaleActive() ? $page->sale_price : $page->original_price) }} د.إ
                    </span>

                    {{-- OLD PRICE --}}
                    @if ($page->original_price && $page->sale_price && $page->pageSaleActive())
                        <span class="text-gray-400 line-through text-lg">
                            {{ number_format($page->original_price) }} د.إ
                        </span>
                    @endif

                    {{-- DISCOUNT BADGE --}}
                    @if ($page->sale_percent && $page->pageSaleActive())
                        <span
                            class="flex items-center gap-1 bg-[{{ $page->theme_color }}] text-[{{ $contrastColor }}] text-sm font-bold px-3 py-1 rounded-full">
                            {{ $page->sale_percent }}%
                        </span>
                    @endif
                </div>

                {{-- RATING + SOLD --}}
                <div class="flex items-center justify-between gap-3 text-sm text-gray-600">

                    <div class="flex gap-2">
                        <div class="flex items-center gap-1 text-yellow-400">
                            ★★★★★
                        </div>
                        <span>
                            {{ number_format($page->reviews_count ?? ($page->items_sold_count ?? 0)) }}
                            تقييم
                        </span>
                    </div>
                    <span>
                        {{ number_format($page->product->sales_number ?? 0) }} بيعت حتى الآن
                    </span>
                </div>
            </div>
        </section>

        {{-- URGENCY + COUNTDOWN --}}
        <section class="bg-white px-4 py-6 border-b" dir="rtl">
            <div class="max-w-[420px] mx-auto text-center space-y-4">

                <div class="text-lg font-bold text-gray-900">
                    عجل! فقط
                    <span class="inline-block bg-gray-200 px-3 py-1 rounded-md mx-1">
                        {{ $page->product->stock - $page->product->sales_number }}
                    </span>
                    متبقية في المخزون
                </div>

                @if ($page->pageSaleActive())
                    <div class="border rounded-xl p-4 inline-block bg-white shadow-sm">
                        <div id="countdown" data-end="{{ \Carbon\Carbon::parse($page->sale_ends_at)->timestamp }}"
                            class="flex items-center justify-center gap-3">

                            <div class="text-center">
                                <div class="count-box" data-days>00</div>
                                <span class="label">يوم</span>
                            </div>

                            <span class="colon">:</span>

                            <div class="text-center">
                                <div class="count-box" data-hours>00</div>
                                <span class="label">ساعة</span>
                            </div>

                            <span class="colon">:</span>

                            <div class="text-center">
                                <div class="count-box" data-minutes>00</div>
                                <span class="label">دقائق</span>
                            </div>

                            <span class="colon">:</span>

                            <div class="text-center">
                                <div class="count-box" data-seconds>00</div>
                                <span class="label">ثواني</span>
                            </div>

                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- IMPORTANT INFO --}}
        <section class="bg-white px-4 py-8">
            <div class="max-w-[420px] mx-auto space-y-4 text-right">
                <h2 class="text-2xl font-bold text-gray-900">معلومات مهمة</h2>
                <p class="text-gray-700 leading-relaxed text-base overflow-text">
                    {{ $page->description }}
                </p>
            </div>
        </section>

        {{-- ORDER MODAL --}}
        <div class="bg-white w-full max-w-sm sm:max-w-md md:max-w-lg relative my-8" id="orderForm">
            <div class="p-4 sm:p-6 space-y-5">
                <h2 class="text-2xl font-bold text-center">اطلب الأن</h2>

                @php
                    function darkenColor($hex, $percent = 25)
                    {
                        $hex = str_replace('#', '', $hex);

                        if (strlen($hex) == 3) {
                            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                        }

                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));

                        $r = max(0, min(255, $r - ($r * $percent) / 100));
                        $g = max(0, min(255, $g - ($g * $percent) / 100));
                        $b = max(0, min(255, $b - ($b * $percent) / 100));

                        return sprintf('#%02x%02x%02x', $r, $g, $b);
                    }

                    $offerColor = isLightColor($page->theme_color)
                        ? darkenColor($page->theme_color, 35) // darker if light
                        : $page->theme_color;
                @endphp
                @if (!empty($page->offers))
                    <div class="space-y-3" id="offersContainer">
                        @foreach ($page->offers as $offer)
                            <div class="offer-item flex items-center gap-3 border rounded-lg p-3 cursor-pointer hover:border-[{{ $offerColor }}]"
                                data-quantity="{{ $offer['quantity'] }}" data-price="{{ $offer['price'] }}">

                                <div class="flex justify-between w-full">

                                    <div class="font-bold">
                                        اشتري
                                        <span class="text-[{{ $offerColor }}]">{{ $offer['quantity'] }}</span>
                                        ب
                                        <span class="text-[{{ $offerColor }}]">{{ $offer['price'] }} د.إ</span>
                                    </div>

                                    @if ($offer['label'])
                                        <div
                                            class="bg-[{{ $offerColor }}] text-[{{ isLightColor($offerColor) ? '#000' : '#fff' }}] px-2 py-1 text-xs rounded-full">
                                            {{ $offer['label'] }}
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form id="formSubmit" method="POST" action="{{ route('pages.submitOrder', $page->slug) }}"
                    class="space-y-3">
                    @csrf
                    <input type="hidden" name="quantity" id="orderQuantity" value="1">
                    <input type="hidden" name="offer_price" id="offer_price"
                        value="{{ $page->sale_price ?? $page->original_price }}" />
                    <input name="full_name" placeholder="الاسم بالكامل" required
                        class="w-full px-4 py-3 border rounded-lg">
                    <div class="space-y-1" dir="rtl">
                        <input name="phone" id="phoneInput" dir="rtl"
                            placeholder="رقم الهاتف (مثال: 0501234567)" required
                            class="w-full px-4 py-3 border rounded-lg transition-colors duration-300 outline-none"
                            type="tel">
                        <p id="phoneError" class="text-red-500 text-xs hidden">يرجى إدخال رقم هاتف إماراتي صحيح
                            (05xxxxxxxx)</p>
                    </div>
                    <select name="government" required class="w-full px-4 py-3 border rounded-lg bg-white">
                        <option value="Abu Dhabi" selected>Abu Dhabi / أبو ظبي</option>
                        <option value="Dubai">Dubai / دبي</option>
                        <option value="Sharjah">Sharjah / الشارقة</option>
                        <option value="Ajman">Ajman / عجمان</option>
                        <option value="Al Ain">Al Ain / العين</option>
                        <option value="Fujairah">Fujairah / الفجيرة</option>
                        <option value="Umm Al-Quwain">Umm Al-Quwain / أم القيوين</option>
                        <option value="Ras Al Khaimah">Ras Al Khaimah / رأس الخيمة</option>
                    </select>
                    <input type="hidden" name="order_index_string" id="orderIndexString" value="">

                    <textarea name="address" placeholder="العنوان بالتفصيل" required rows="3"
                        class="w-full px-4 py-3 border rounded-lg resize-none"></textarea>
                    <button type="submit" id="submitBtn" class="w-full font-bold py-3 rounded-lg text-lg"
                        style="background-color: {{ $page->theme_color }}; color: {{ $contrastColor }};">
                        تأكيد الطلب
                    </button>
                </form>
            </div>
        </div>

        {{-- ALL IMAGES --}}
        <section class="bg-gray-50 px-4 py-8">
            <div class="max-w-[420px] mx-auto">

                {{-- <h2 class="text-2xl font-bold text-center mb-6 text-gray-900">
                    صور المنتج
                </h2> --}}

                <div class="grid grid-cols-1 gap-3">
                    @foreach ($page->images as $order => $path)
                        <button type="button" onclick="openImageModal('{{ asset($path) }}')"
                            class="focus:outline-none">
                            <img src="{{ asset($path) }}" class="w-full object-cover rounded-lg shadow">
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        @if (!empty($page->features))
            <div class="card border-b">
                <div class="p-4 grid grid-cols-2 gap-3 features-grid">

                    @if (in_array('cod', $page->features ?? []))
                        {{-- الدفع عند الاستلام --}}
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-regular fa-credit-card text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">الدفع</p>
                                <p class="text-gray-500 text-md">عند الاستلام</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('free_shipping', $page->features ?? []))
                        {{-- شحن مجاني --}}
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-regular fa-truck text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">مجاناً</p>
                                <p class="text-gray-500 text-md">التوصيل</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('replace', $page->features ?? []))
                        {{-- استبدال خلال 7 أيام --}}
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-solid fa-arrows-rotate text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">استبدال</p>
                                <p class="text-gray-500 text-md">خلال 7 يوم</p>
                            </div>
                        </label>
                    @endif


                    @if (in_array('support', $page->features ?? []))
                        {{-- خدمة 7\24 --}}
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-solid fa-headset text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">7\24</p>
                                <p class="text-gray-500 text-md">خدمة</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('warranty', $page->features ?? []))
                        {{-- ضمان لمدة سنة --}}
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-solid fa-shield text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">ضمان</p>
                                <p class="text-gray-500 text-md">لمدة سنة</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('same_day', $page->features ?? []))
                        {{-- التوصيل نفس اليوم --}}
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-solid fa-hourglass text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">التوصيل</p>
                                <p class="text-gray-500 text-md">نفس اليوم</p>
                            </div>
                        </label>
                    @endif
                </div>
            </div>
        @endif

        {{-- REVIEWS --}}
        @if ($page->reviews->count())
            <section class="bg-white rounded-lg shadow p-6 space-y-6">
                <h2 class="text-xl font-bold text-gray-800">
                    آراء العملاء
                    <span class="text-sm text-gray-500">({{ $page->reviews->count() }} تقييم)</span>
                </h2>
                <div class="space-y-5">
                    @foreach ($page->reviews as $review)
                        <div class="flex gap-4 border-b pb-5 last:border-b-0">
                            <div class="shrink-0">
                                @if ($review->reviewer_image)
                                    <img src="{{ asset($review->reviewer_image) }}"
                                        class="w-12 h-12 rounded-full object-cover border">
                                @else
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 font-bold border">
                                        {{ mb_substr($review->reviewer_name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center justify-between">
                                    <strong class="text-gray-800">{{ $review->reviewer_name }}</strong>
                                    <div class="flex text-yellow-400 text-sm">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->stars ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $review->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif


        {{-- STICKY ORDER BUTTON --}}
        <div id="sticky-order"
            class="fixed bottom-0 inset-x-0 z-40 bg-white border-t shadow-lg p-3 transition-transform duration-300 ease-in-out">
            <button class="w-full max-w-md mx-auto block font-bold py-4 rounded-xl text-xl"
                style="background-color: {{ $page->theme_color }}; color: {{ $contrastColor }};">
                <a href="#orderForm">
                    اطلب الأن
                </a>
            </button>
        </div>
    </div>

    @if (request()->query('success'))
        <div id="successOverlay" class="fixed inset-0 bg-black/80 z-[999] flex items-center justify-center">
            <div class="bg-white rounded-2xl p-8 text-center max-w-sm w-full mx-4 animate-scale-in">
                <div class="mx-auto mb-4 w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" stroke-width="3"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">تم استلام طلبك</h2>
                <p class="text-gray-600 mb-6">سيتم التواصل معك في أقرب وقت لتأكيد الطلب</p>
                <button onclick="closeSuccessOverlay()"
                    class="w-full bg-green-600 text-white font-bold py-3 rounded-xl text-lg">تمام</button>
            </div>
        </div>
    @endif

</body>

@if (request()->query('success'))
    <script>
        const purchaseValue = {{ request()->query('sellPrice') }};
        const currency = 'AED';
        const transactionId = {{ request()->query('order_id') }};
        const productName = @json($product->name);

        // Meta Purchase
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Purchase', {
                value: purchaseValue,
                currency: currency
            });
        }

        // Google Purchase
        if (typeof gtag !== 'undefined') {
            gtag('event', 'purchase', {
                transaction_id: transactionId,
                value: purchaseValue,
                currency: currency,
                items: [{
                    item_name: productName
                }]
            });
        }

        // TikTok Purchase
        if (typeof ttq !== 'undefined') {
            ttq.track('CompletePayment', {
                content_name: productName,
                value: purchaseValue,
                currency: currency
            });
        }

        // Snapchat Purchase
        if (typeof snaptr !== 'undefined') {
            snaptr('track', 'PURCHASE', {
                price: purchaseValue,
                currency: currency
            });
        }

        // Twitter (X) Purchase
        if (typeof twq !== 'undefined') {
            twq('event', 'tw-purchase', {
                value: purchaseValue,
                currency: currency
            });
        }
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const phoneInput = document.getElementById('phoneInput');
        const phoneError = document.getElementById('phoneError');
        const form = document.getElementById('formSubmit');
        const fullNameInput = form.querySelector('input[name="full_name"]');
        const governmentInput = form.querySelector('select[name="government"]');
        const addressInput = form.querySelector('textarea[name="address"]');
        const quantityInput = form.querySelector('input[name="quantity"]');
        const offerPriceInput = form.querySelector('input[name="offer_price"]');
        const btn = document.getElementById('submitBtn');
        let orderIndexString = null;

        const uaePattern = /^(?:\+971|00971|0)?(?:5[024568])\d{7}$/;

        const isPhoneValid = () => uaePattern.test(phoneInput.value.replace(/\s+/g, ''));

        const validatePhone = () => {
            if (isPhoneValid()) {
                phoneInput.classList.remove('border-red-500');
                phoneInput.classList.add('border-green-500');
                phoneError.classList.add('hidden');
                return true;
            } else {
                phoneInput.classList.add('border-red-500');
                phoneInput.classList.remove('border-green-500');
                phoneError.classList.remove('hidden');
                return false;
            }
        };

        const saveCartUser = () => {
            // ✅ Only require phone to exist — track even if not fully valid yet
            // But do require at least a valid UAE number before hitting the server
            if (!isPhoneValid()) {
                console.log('Phone not valid yet, skipping track');
                return;
            }

            console.log('Tracking...', {
                phone: phoneInput.value,
                full_name: fullNameInput.value,
            });

            const data = {
                phone: phoneInput.value,
                full_name: fullNameInput.value,
                government: governmentInput.value,
                address: addressInput.value,
                quantity: quantityInput.value,
                offer_price: offerPriceInput.value,
                order_index_string: orderIndexString,
                _token: form.querySelector('input[name="_token"]').value
            };

            console.log('Sending data:', data);
            console.log('URL:', `{{ route('pages.trackCartUser', $page->slug) }}`);

            fetch(`{{ route('pages.trackCartUser', $page->slug) }}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(res => {
                    console.log('Response status:', res.status);
                    return res.json();
                })
                .then(res => {
                    console.log('Response data:', res);
                    if (res.order_index_string) {
                        orderIndexString = res.order_index_string;
                        console.log('orderIndexString set to:', orderIndexString);
                    }
                })
                .catch((err) => {
                    console.error('trackCartUser error:', err);
                });
        };

        // ✅ Track on blur of each field (phone must be valid at time of call)
        [phoneInput, fullNameInput, governmentInput, addressInput].forEach(input => {
            input.addEventListener('blur', saveCartUser);
        });

        // ✅ Validate UI feedback separately on phone blur
        phoneInput.addEventListener('blur', validatePhone);

        // ✅ Also track when offers change quantity/price
        quantityInput.addEventListener('change', saveCartUser);
        offerPriceInput.addEventListener('change', saveCartUser);

        form.addEventListener('submit', (e) => {
            if (!validatePhone()) {
                e.preventDefault();
                phoneInput.focus();
                btn.disabled = false;
                btn.innerHTML = 'تأكيد الطلب';
                return;
            }

            // Inject order_index_string into form
            let hiddenIndex = form.querySelector('input[name="order_index_string"]');
            if (!hiddenIndex) {
                hiddenIndex = document.createElement('input');
                hiddenIndex.type = 'hidden';
                hiddenIndex.name = 'order_index_string';
                form.appendChild(hiddenIndex);
            }
            hiddenIndex.value = orderIndexString ?? '';

            btn.disabled = true;
            btn.innerHTML = 'جاري تأكيد الطلب...';
        });
    });
</script>

<script>
    // Countdown Logic
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('countdown');
        if (!el) return;
        const endTime = parseInt(el.dataset.end) * 1000;
        const d = el.querySelector('[data-days]');
        const h = el.querySelector('[data-hours]');
        const m = el.querySelector('[data-minutes]');
        const s = el.querySelector('[data-seconds]');
        const pad = n => String(n).padStart(2, '0');
        const tick = () => {
            const now = Date.now();
            let diff = Math.max(0, endTime - now);
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            diff %= (1000 * 60 * 60 * 24);
            const hours = Math.floor(diff / (1000 * 60 * 60));
            diff %= (1000 * 60 * 60);
            const minutes = Math.floor(diff / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            d.textContent = pad(days);
            h.textContent = pad(hours);
            m.textContent = pad(minutes);
            s.textContent = pad(seconds);
        };
        tick();
        setInterval(tick, 1000);
    });
</script>

<script>
    const offers = document.querySelectorAll('#offersContainer .offer-item');
    const quantityInput = document.getElementById('orderQuantity');
    const offerPriceInput = document.getElementById('offer_price');

    let selectedOffer = null;

    // default values
    quantityInput.value = 1;

    offers.forEach(offer => {
        offer.addEventListener('click', () => {

            // unselect if clicked again
            if (selectedOffer === offer) {
                offer.classList.remove(
                    'border-[{{ $page->theme_color }}]',
                    'shadow-xl',
                    'border-2'
                );

                selectedOffer = null;
                quantityInput.value = 1;

                // back to normal price
                offerPriceInput.value = "{{ $page->sale_price ?? $page->product->price }}";
                return;
            }

            // remove all selection
            offers.forEach(o => o.classList.remove(
                'border-[{{ $page->theme_color }}]',
                'shadow-xl',
                'border-2'
            ));

            // select current
            offer.classList.add(
                'border-[{{ $page->theme_color }}]',
                'shadow-xl',
                'border-2'
            );

            selectedOffer = offer;

            quantityInput.value = offer.dataset.quantity;

            offerPriceInput.value = offer.dataset.price;
        });
    });
</script>

<script>
    const sticky = document.getElementById('sticky-order');

    let scrollTimeout = null;
    let isScrolling = false;

    window.addEventListener('scroll', () => {
        if (!isScrolling) {
            sticky.classList.add('hide-sticky');
            isScrolling = true;
        }

        clearTimeout(scrollTimeout);

        scrollTimeout = setTimeout(() => {
            sticky.classList.remove('hide-sticky');
            isScrolling = false;
        }, 200); // وقت التوقف (ms)
    });
</script>


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

<script>
    const maleNames = [
        "محمد", "احمد", "محمود", "يوسف", "علي", "عبدالله", "مصطفى", "حسن", "عمر", "خالد",
        "ابراهيم", "طارق", "كريم", "رامي", "اسلام", "ياسين", "وليد", "سامح", "هشام", "شريف",
        "عمرو", "مروان", "تامر", "ادهم", "باسم", "سيف", "جمال", "حسين", "صلاح", "ريان"
    ];

    const femaleNames = [
        "سارة", "مريم", "نور", "منة", "فاطمة", "هدى", "دينا", "رانيا", "نورا", "ياسمين",
        "شيماء", "رحاب", "دعاء", "بسمة", "ندى", "آية", "ملك", "جنى", "سلمى", "فرح",
        "ريم", "ليان", "تالا", "روان", "لينا", "سما", "هاجر", "مي", "سمر", "إسراء"
    ];

    const buyText = document.getElementById('buy-text');

    function randomTime() {
        const num = Math.floor(Math.random() * 7) + 3;
        return `منذ ${num} دقائق`;
    }

    function showRandomBuy() {

        const isMale = Math.random() > 0.5;

        let name, text;

        if (isMale) {
            name = maleNames[Math.floor(Math.random() * maleNames.length)];
            text = "اشترى";
        } else {
            name = femaleNames[Math.floor(Math.random() * femaleNames.length)];
            text = "اشترت";
        }

        buyText.innerText = `${name} ${text} ${randomTime()}`;

        buyText.classList.add('show');

        setTimeout(() => {
            buyText.classList.remove('show');
        }, 1500);
    }

    // every 5s
    setInterval(showRandomBuy, 5000);
</script>

<script>
    const el = document.getElementById("topFeatureText");

    if (el) {
        const texts = @json($topFeatureTexts ?? []);

        if (Array.isArray(texts) && texts.length > 0) {

            let i = 0;
            el.innerText = texts[0]; // initial text

            if (texts.length > 1) {

                setInterval(() => {

                    // fade out
                    el.classList.remove("fade-in");
                    el.classList.add("fade-out");

                    setTimeout(() => {

                        // change text
                        i = (i + 1) % texts.length;
                        el.innerText = texts[i];

                        // reset animation
                        el.classList.remove("fade-out");
                        el.classList.add("fade-in");

                        setTimeout(() => {
                            el.classList.remove("fade-in");
                        }, 500);

                    }, 500);

                }, 3000);
            }
        }
    }
</script>

<script>
    const customMovingTexts = @json($page->moving_banner_text ?? []);
    const movingTextsArray = Array.isArray(customMovingTexts) ? customMovingTexts : [customMovingTexts];
    const filteredMovingTexts = movingTextsArray.filter(text => text);

    if (filteredMovingTexts.length > 0) {
        const container = document.getElementById('movingTexts');
        container.innerHTML = filteredMovingTexts.map(text => `<span>${text}</span>`).join('');
        // calculate duration based on text width
        const totalWidth = container.scrollWidth;
        const speed = 30; // pixels per second
        container.style.animationDuration = `${totalWidth / speed}s`;
    }
</script>

</html>
