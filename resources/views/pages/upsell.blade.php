<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ممكن يعجبك</title>
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-gray-900" dir="rtl">
    <div class="w-full max-w-[520px] bg-white min-h-screen shadow-xl m-auto">

        <h2 class="text-xl font-bold text-center mb-4 mt-4">
            ممكن يعجبك كمان
        </h2>

        <div class="flex flex-col p-8">
            @foreach ($page->upsellProducts as $product)
                <div>
                    <form method="POST" action="{{ route('pages.submitOrderFromUpsellPage', $product->id) }}">
                        @csrf
                        <input type="hidden" name="full_name" value="{{ $order->name }}">
                        <input type="hidden" name="phone" value="{{ $order->phone }}">
                        <input type="hidden" name="government" value="{{ $order->city }}">
                        <input type="hidden" name="address" value="{{ $order->address }}">

                        <div class="space-y-4">
                            <strong>{{ $product->name }}</strong>
                            <span class="text-green-600 font-bold">{{ $product->price }} د.إ</span>
                        </div>

                        <button type="submit" class="mt-6 w-full bg-green-600 text-white py-3 rounded-lg font-bold">
                            أضف إلى الطلب
                        </button>
                    </form>
                </div>
            @endforeach

        </div>


    </div>

</body>

</html>
