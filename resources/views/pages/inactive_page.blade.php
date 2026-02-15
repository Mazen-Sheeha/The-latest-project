<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Active Page</title>
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">


    {{-- Tracking Pixels --}}
    @if ($page->meta_pixel)
        <!-- Meta Pixel Blueprint -->
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
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $page->meta_pixel }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $page->meta_pixel }}&ev=PageView&noscript=1" /></noscript>
        <!-- End Meta Pixel Blueprint -->
    @endif

    @if ($page->google_ads_pixel)
        <!-- Google Ads Pixel Blueprint -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $page->google_ads_pixel }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $page->google_ads_pixel }}');
        </script>
        <!-- End Google Ads Pixel Blueprint -->
    @endif

    @if ($page->google_analytics)
        <!-- Google Analytics Blueprint -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $page->google_analytics }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $page->google_analytics }}');
        </script>
        <!-- End Google Analytics Blueprint -->
    @endif
    @if ($page->tiktok_pixel)
        <!-- Tiktok Pixel Code -->
        {!! $page->tiktok_pixel !!}
        <!-- End TikTok Pixel Code -->
    @endif
    @if ($page->snapchat_pixel)
        <!-- SnapChat Pixel Code -->
        {!! $page->snapchat_pixel !!}
        <!-- End SnapChat Pixel Code -->
    @endif
    @if ($page->twitter_pixel)
        <!-- Twitter Pixel Code -->
        {!! $page->twitter_pixel !!}
        <!-- End Twitter Pixel Code -->
    @endif

    <style>
        * {
            font-family: 'Almarai', sans-serif;
        }
    </style>
</head>

<body>

    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="text-center bg-white shadow rounded-lg p-10 max-w-md">

            <div class="text-red-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m0 3h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-xl font-bold mb-2">هذه الصفحة غير متاحة حالياً</h1>

            <p class="text-gray-600 mb-6">
                صفحة البيع هذه غير منشورة أو تم إيقافها مؤقتاً.
                <br>
                يرجى المحاولة لاحقاً.
            </p>
        </div>
    </div>

</body>

</html>
