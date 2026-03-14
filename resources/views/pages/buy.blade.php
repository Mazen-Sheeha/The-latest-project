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
            fbq('track', 'PageView');
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
            gtag('event', 'page_view');
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
                twq('event', 'tw-PageView', {});
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
            bottom: 150px;
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

        @keyframes bounce-x {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(-5px);
            }
        }

        .animate-bounce-x {
            animation: bounce-x 1s infinite;
        }
    </style>
</head>


<body class="bg-white text-gray-900" dir="rtl">
    <div class="w-full max-w-[520px] bg-white min-h-screen shadow-xl m-auto relative pb-24">
        @php
            $wa = $page->whatsapp_phone ?? null;
            $wa_clean = $wa ? preg_replace('/[^0-9]/', '', $wa) : '1234567890';
        @endphp

        @if ($wa)
            @php
                $utmSource = request()->query('utm_source');
                $utmCampaign = request()->query('utm_campaign');

                $utmText = '';

                if ($utmSource || $utmCampaign) {
                    $utmText = '(' . trim(($utmSource ?? '') . ' - ' . ($utmCampaign ?? ''), ' -') . ')';
                }

                $baseMessage = $page->whatsapp_label ?? '';

                $message = str_replace('__UTM__', $utmText, $baseMessage);
            @endphp

            <a href="https://wa.me/{{ $wa_clean }}?text={{ urlencode($message) }}" target="_blank"
                class="whatsapp-float">

                <span class="label">
                    تحدث معنا
                </span>

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

            <button type="button" onclick="openImageModal('{{ asset('public/' . $firstImage) }}')"
                class="focus:outline-none">
                <img src="{{ asset('public/' . $firstImage) }}" class="w-full object-cover shadow">
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

            $darkerColor = isLightColor($page->theme_color)
                ? darkenColor($page->theme_color, 35) // darker if light
                : $page->theme_color;
        @endphp

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
                        {{ number_format($page->items_sold_count ?? 0) }} بيعت حتى الآن
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
                        {{ $page->stock_count ? $page->stock_count : $page->product->stock - $page->product->sales_number }}
                    </span>
                    متبقية في المخزون
                </div>

                @if ($page->pageSaleActive())
                    <div class="border rounded-xl p-4 inline-block bg-white shadow-sm">
                        <div id="countdown" data-end="{{ \Carbon\Carbon::parse($page->sale_ends_at)->timestamp }}"
                            class="flex items-center justify-center gap-3">

                            <div class="text-center">
                                <div class="count-box" data-days>00</div>
                                <span class="label text-[{{ $darkerColor }}]">يوم</span>
                            </div>

                            <span class="colon">:</span>

                            <div class="text-center">
                                <div class="count-box" data-hours>00</div>
                                <span class="label text-[{{ $darkerColor }}]">ساعة</span>
                            </div>

                            <span class="colon">:</span>

                            <div class="text-center">
                                <div class="count-box" data-minutes>00</div>
                                <span class="label text-[{{ $darkerColor }}]">دقائق</span>
                            </div>

                            <span class="colon">:</span>

                            <div class="text-center">
                                <div class="count-box" data-seconds>00</div>
                                <span class="label text-[{{ $darkerColor }}]">ثواني</span>
                            </div>

                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- IMPORTANT INFO --}}
        @if ($page->description)
            <section class="bg-white px-4 py-8">
                <div class="max-w-[420px] mx-auto space-y-4 text-right">
                    <h2 class="text-2xl font-bold text-gray-900">معلومات مهمة</h2>
                    <p class="text-gray-700 leading-relaxed text-base overflow-text">
                        {{ $page->description }}
                    </p>
                </div>
            </section>
        @endif

        {{-- OUTER FRAME CONTAINER --}}
        <div class="max-w-2xl mx-4 my-8 overflow-hidden bg-white border-4 border-double rounded-3xl shadow-2xl"
            style="border-color: {{ $page->theme_color }}44;"> {{-- 44 adds transparency to the hex --}}

            <div class="p-1 sm:p-2"> {{-- Subtle inner spacing for the frame --}}
                <div class="p-6 sm:p-10 space-y-8 bg-white rounded-2xl border border-gray-100">

                    {{-- Header --}}
                    @if (!empty($page->offers) && count($page->offers) > 0)
                        <div class="text-center space-y-3">
                            <div class="inline-block px-4 py-1 rounded-full text-xs font-bold tracking-widest uppercase mb-2"
                                style="background-color: {{ $page->theme_color }}22; color: {{ $darkerColor }};">
                                وفر اكثر
                            </div>
                            <h2 class="text-3xl font-black text-gray-900 leading-tight">اختر العرض المناسب</h2>
                            <p class="text-gray-500 text-sm">وفر اكثر عند شرائك مع العروض</p>
                        </div>
                    @else
                        <div class="text-center space-y-3">
                            <h2 class="text-3xl font-black text-gray-900 leading-tight">اطلب الآن</h2>
                            <p class="text-gray-500 text-sm">أدخل بياناتك وسنوصلها إليك</p>
                        </div>
                    @endif

                    {{-- Offers Section --}}
                    @if (!empty($page->offers))
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                                    <span class="w-2 h-6 rounded-full"
                                        style="background-color: {{ $page->theme_color }};"></span>
                                    1. اختر باقتك المفضلة
                                </h3>
                            </div>

                            <div class="grid gap-8" id="offersContainer">
                                @foreach ($page->offers as $index => $offer)
                                    @php
                                        $anySelected = collect($page->offers)->contains(function ($o) {
                                            return isset($o['selected']) &&
                                                ($o['selected'] == true || $o['selected'] == '1');
                                        });
                                        $isActive =
                                            (isset($offer['selected']) &&
                                                ($offer['selected'] == true || $offer['selected'] == '1')) ||
                                            ($index === 0 && !$anySelected);
                                    @endphp

                                    <div class="offer-item {{ $isActive ? 'selected-offer shadow-md' : '' }} group relative flex items-center justify-between p-5 border-2 rounded-2xl cursor-pointer transition-all duration-300 hover:scale-[1.01]"
                                        onclick="selectOffer(this, {{ $index }}, true)"
                                        data-quantity="{{ $offer['quantity'] }}" data-price="{{ $offer['price'] }}"
                                        style="border-color: {{ $isActive ? $darkerColor : '#f3f4f6' }}; background-color: #fafafa;">


                                        {{-- Selection Indicator --}}
                                        <div
                                            class="absolute -top-3 -right-3 bg-white rounded-full shadow-lg {{ $isActive ? '' : 'hidden' }} selection-check z-30">
                                            <svg class="w-8 h-8" style="color: {{ $darkerColor }};"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>

                                        <div class="flex justify-between items-center gap-5">
                                            @if ($offer['image'])
                                                <div
                                                    class="w-20 h-20 rounded-xl overflow-hidden border-2 border-white shadow-sm shrink-0">
                                                    <img src="{{ asset('public/' . $offer['image']) }}"
                                                        alt="offer" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                            <div class="flex flex-col justify-between items-center gap-5">
                                                @php
                                                    $qty = $offer['quantity'];
                                                    $price = $offer['price'];

                                                    $defaultSentence = match (true) {
                                                        $qty == 1 => "قطعة واحدة بـ {$price} د.إ فقط",
                                                        $qty == 2 => "قطعتين بـ {$price} د.إ فقط",
                                                        $qty == 3 => "ثلاث قطع بـ {$price} د.إ فقط",
                                                        $qty == 4 => "أربع قطع بـ {$price} د.إ فقط",
                                                        $qty == 5 => "خمس قطع بـ {$price} د.إ فقط",
                                                        default => "{$qty} قطع بـ {$price} د.إ فقط",
                                                    };

                                                    $lineThroughPrice = null;
                                                    if ($page->pageSaleActive()) {
                                                        $lineThroughPrice = $page->sale_price;
                                                    } elseif ($page->original_price) {
                                                        $lineThroughPrice = $page->original_price;
                                                    }
                                                @endphp
                                                <div
                                                    class="text-xl font-black text-gray-800 self-start max-w-fit min-w-32">
                                                    {{ !empty($offer['sentence']) ? $offer['sentence'] : $defaultSentence }}
                                                </div>

                                                <div class="flex items-baseline gap-4 self-start">
                                                    <div class="text-lg font-bold"
                                                        style="color: {{ $darkerColor }};">
                                                        {{ $offer['price'] }} د.إ
                                                    </div>

                                                    @if ($lineThroughPrice)
                                                        <div class="text-sm text-gray-400 line-through">
                                                            {{ $lineThroughPrice }} د.إ
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                        @if ($offer['label'])
                                            <div class="absolute -top-3 left-4 px-3 py-1 text-[10px] font-black rounded-md shadow-sm transform -rotate-2"
                                                style="background-color: {{ $darkerColor }}; color: {{ isLightColor($darkerColor) ? '#000' : '#fff' }};">
                                                {{ $offer['label'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Form Section --}}
                    <div id="orderForm" class="p-6 rounded-2xl border-2 border-dashed"
                        style="border-color: {{ $page->theme_color }}33; background-color: #fdfdfd;">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-6 text-lg">
                            <span class="w-2 h-6 rounded-full"
                                style="background-color: {{ $page->theme_color }};"></span>
                            2. بيانات التوصيل
                        </h3>

                        <form id="formSubmit" method="POST" action="{{ route('pages.submitOrder', $page->slug) }}"
                            class="space-y-5">
                            @csrf
                            <input type="hidden" name="quantity" id="orderQuantity" value="1">
                            <input type="hidden" name="offer_price" id="offer_price"
                                value="{{ $page->sale_price ?? $page->original_price }}" />
                            <input type="hidden" name="order_index_string" id="orderIndexString" value="">

                            <div class="space-y-1">
                                <label class="text-sm font-bold text-gray-700 mr-1">الاسم بالكامل</label>
                                <input name="full_name" placeholder="ادخل اسمك هنا" required
                                    class="w-full px-4 py-4 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-opacity-10 transition-all outline-none"
                                    style="--tw-focus-ring-color: {{ $page->theme_color }};">
                            </div>

                            <div class="space-y-1" dir="rtl">
                                <label class="text-sm font-bold text-gray-700 mr-1">رقم الهاتف</label>
                                <input name="phone" id="phoneInput" dir="rtl" placeholder="05xxxxxxxx"
                                    required
                                    class="w-full px-4 py-4 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-opacity-10 transition-all outline-none"
                                    type="tel">
                                <p id="phoneError" class="text-red-500 text-xs hidden">يرجى إدخال رقم هاتف إماراتي
                                    صحيح
                                    (05xxxxxxxx)</p>
                            </div>


                            <div class="space-y-1">
                                <label class="text-sm font-bold text-gray-700 mr-1">الإمارة</label>
                                <select name="government" required
                                    class="w-full px-4 py-4 bg-white border border-gray-200 rounded-xl focus:ring-4 outline-none appearance-none">
                                    <option value="Abu Dhabi" selected>Abu Dhabi / أبو ظبي</option>
                                    <option value="Dubai">Dubai / دبي</option>
                                    <option value="Sharjah">Sharjah / الشارقة</option>
                                    <option value="Ajman">Ajman / عجمان</option>
                                    <option value="Al Ain">Al Ain / العين</option>
                                    <option value="Fujairah">Fujairah / الفجيرة</option>
                                    <option value="Umm Al-Quwain">Umm Al-Quwain / أم القيوين</option>
                                    <option value="Ras Al Khaimah">Ras Al Khaimah / رأس الخيمة</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-bold text-gray-700 mr-1">العنوان</label>
                                <textarea name="address" placeholder="يرجى كتابة العنوان بالتفصيل..." required rows="2"
                                    class="w-full px-4 py-4 bg-white border border-gray-200 rounded-xl resize-none outline-none focus:ring-4"></textarea>
                            </div>

                            <button type="submit" id="submitBtn"
                                class="w-full font-black py-5 rounded-2xl text-2xl shadow-xl transform transition hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3"
                                style="background-color: {{ $page->theme_color }}; color: {{ $contrastColor }};">
                                <span>تأكيد الطلب الآن</span>
                                <svg class="w-6 h-6 animate-bounce-x" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    {{-- Trust Badges --}}
                    {{-- <div class="flex justify-center gap-6 text-gray-400 opacity-70">
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    stroke-width="2" />
                            </svg>
                            <span class="text-[10px] font-bold">ضمان أصلي</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M20 12H4M10 18l-6-6 6-6" stroke-width="2" />
                            </svg>
                            <span class="text-[10px] font-bold">دفع عند الاستلام</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-width="2" />
                            </svg>
                            <span class="text-[10px] font-bold">فحص قبل الدفع</span>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        {{-- ALL IMAGES --}}
        <section class="bg-gray-50 px-4 py-8">
            <div class="max-w-[420px] mx-auto">

                {{-- <h2 class="text-2xl font-bold text-center mb-6 text-gray-900">
                    صور المنتج
                </h2> --}}

                <div class="grid grid-cols-1">
                    @foreach ($page->images as $order => $path)
                        @if ($loop->first)
                            @continue
                        @endif
                        <button type="button" onclick="openImageModal('{{ asset($path) }}')"
                            class="focus:outline-none">
                            <img src="{{ asset('public/' . $path) }}" class="w-full object-cover shadow">
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

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
                                    <img src="{{ asset('public/' . $review->reviewer_image) }}"
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

        {{-- @if (!empty($page->features))
            <div class="card border-b">
                <div class="p-4 grid grid-cols-2 gap-3 features-grid">

                    @if (in_array('cod', $page->features ?? []))
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-regular fa-credit-card text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">الدفع</p>
                                <p class="text-gray-500 text-md">عند الاستلام</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('free_shipping', $page->features ?? []))
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-regular fa-truck text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">مجاناً</p>
                                <p class="text-gray-500 text-md">التوصيل</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('replace', $page->features ?? []))
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-solid fa-arrows-rotate text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">استبدال</p>
                                <p class="text-gray-500 text-md">خلال 7 يوم</p>
                            </div>
                        </label>
                    @endif


                    @if (in_array('support', $page->features ?? []))
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-solid fa-headset text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">7\24</p>
                                <p class="text-gray-500 text-md">خدمة</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('warranty', $page->features ?? []))
                        <label class="border p-3 rounded flex items-center gap-4">
                            <i class="fa-solid fa-shield text-[{{ $page->theme_color }}] text-3xl"></i>
                            <div>
                                <p class="text-l">ضمان</p>
                                <p class="text-gray-500 text-md">لمدة سنة</p>
                            </div>
                        </label>
                    @endif

                    @if (in_array('same_day', $page->features ?? []))
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
        @endif --}}

        {{-- FEATURES --}}
        @if (!empty($page->features))
            <div class="card border-b">
                <div class="p-4 grid grid-cols-2 gap-3 features-grid">
                    @php
                        $featureIcons = [
                            'cod' => 'fa-regular fa-credit-card',
                            'free_shipping' => 'fa-regular fa-truck',
                            'replace' => 'fa-solid fa-arrows-rotate',
                            'support' => 'fa-solid fa-headset',
                            'warranty' => 'fa-solid fa-shield',
                            'same_day' => 'fa-solid fa-hourglass',
                        ];
                    @endphp

                    @foreach ($page->features as $value => $label)
                        <label class="border p-3 rounded flex items-center gap-4">
                            @if (isset($featureIcons[$value]))
                                <i class="{{ $featureIcons[$value] }} text-[{{ $page->theme_color }}] text-3xl"></i>
                            @endif
                            <p>{{ $label }}</p>
                        </label>
                    @endforeach

                </div>
            </div>
        @endif

        {{-- POLICES MODELS --}}
        @include('partials._policy-modals')

        {{-- STICKY ORDER BUTTON --}}
        <div id="sticky-order"
            class="fixed bottom-0 inset-x-0 z-40 bg-white border-t shadow-lg p-3 transition-transform duration-300 ease-in-out">

            <button type="button" onclick="scrollToOrderForm()"
                class="w-full max-w-md mx-auto block font-bold py-4 rounded-xl text-xl"
                style="background-color: {{ $page->theme_color }}; color: {{ $contrastColor }};">
                اطلب الأن
            </button>
        </div>
    </div>

    @if (request()->query('success'))
        <div id="successOverlay" class="fixed inset-0 bg-black/80 z-[99999] flex items-center justify-center">
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
    function scrollToOrderForm() {
        const form = document.getElementById('orderForm');
        if (!form) return;

        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function selectOffer(element, index, undoInit) {
        if (!element) return;

        const isAlreadySelected = element.classList.contains('selected-offer');

        document.querySelectorAll('.offer-item').forEach(item => {
            item.style.borderColor = '#f3f4f6';
            item.classList.remove('shadow-md', 'selected-offer');
            item.querySelector('.selection-check').classList.add('hidden');
        });

        if (isAlreadySelected && undoInit) {
            element.style.borderColor = '#f3f4f6';
            element.classList.remove('shadow-md', 'selected-offer');
            element.querySelector('.selection-check').classList.add('hidden');

            document.getElementById('orderQuantity').value = "1";
            document.getElementById('offer_price').value =
                "{{ $page->pageSaleActive() ? $page->sale_price : $page->original_price }}";
            document.getElementById('orderIndexString').value = "";
            return
        }

        element.style.borderColor = "{{ $darkerColor }}";
        element.classList.add('shadow-md', 'selected-offer');
        element.querySelector('.selection-check').classList.remove('hidden');

        document.getElementById('orderQuantity').value = element.dataset.quantity;
        document.getElementById('offer_price').value = element.dataset.price;
        document.getElementById('orderIndexString').value = index;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const activeOffer = document.querySelector('.offer-item.selected-offer');

        if (activeOffer) {
            const allOffers = Array.from(document.querySelectorAll('.offer-item'));
            const activeIndex = allOffers.indexOf(activeOffer);

            selectOffer(activeOffer, activeIndex, false);
        } else {
            document.getElementById('orderQuantity').value = "";
            document.getElementById('offer_price').value = "";
            document.getElementById('orderIndexString').value = "";
        }
    });
</script>

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

        const urlParams = new URLSearchParams(window.location.search);

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
                utm_source: urlParams.get('utm_source') ?? null,
                utm_campaign: urlParams.get('utm_campaign') ?? null,
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
            document.getElementById('utm_source').value = urlParams.get('utm_source') ?? null;
            document.getElementById('utm_campaign').value = urlParams.get('utm_campaign') ?? null;

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
