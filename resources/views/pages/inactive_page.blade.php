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
    @if ($page->meta_pixel ?? false)
        {!! $page->meta_pixel !!}
    @endif
    @if ($page->tiktok_pixel ?? false)
        {!! $page->tiktok_pixel !!}
    @endif
    @if ($page->snapchat_pixel ?? false)
        {!! $page->snapchat_pixel !!}
    @endif
    @if ($page->twitter_pixel ?? false)
        {!! $page->twitter_pixel !!}
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
