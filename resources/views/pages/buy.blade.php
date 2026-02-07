<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->slug ?? 'Landing Page' }}</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .count-box {
            background: #2f7f78;
            color: #fff;
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
            color: #2f7f78;
            font-weight: 600;
        }

        .colon {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2f7f78;
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
    </style>

</head>


<body class="bg-white text-gray-900" dir="rtl">
    <div class="w-full max-w-[520px] bg-white min-h-screen shadow-xl m-auto">

        <div class="w-full p-4 bg-[{{ $page->theme_color }}] text-center text-white">الدفع عند الستلام</div>

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
                            class="flex items-center gap-1 bg-[{{ $page->theme_color }}] text-white text-sm font-bold px-3 py-1 rounded-full">
                            {{ $page->sale_percent }}%
                        </span>
                    @endif
                </div>

                {{-- RATING + SOLD --}}
                <div class="flex items-center justify-between gap-3 text-sm text-gray-600">

                    <div class="flex gap-2">
                        {{-- STARS --}}
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

                {{-- STOCK --}}
                <div class="text-lg font-bold text-gray-900">
                    عجل! فقط
                    <span class="inline-block bg-gray-200 px-3 py-1 rounded-md mx-1">
                        {{ $page->product->stock - $page->product->sales_number }}
                    </span>
                    متبقية في المخزون
                </div>

                @if ($page->pageSaleActive())
                    {{-- COUNTDOWN BOX --}}
                    <div class="border rounded-xl p-4 inline-block bg-white shadow-sm">
                        <div id="countdown" data-end="{{ \Carbon\Carbon::parse($page->sale_ends_at)->timestamp }}"
                            class="flex items-center justify-center gap-3">

                            {{-- DAYS --}}
                            <div class="text-center">
                                <div class="count-box bg-[{{ $page->theme_color }}]" data-days>00</div>
                                <span class="label text-[{{ $page->theme_color }}]">يوم</span>
                            </div>

                            <span class="colon text-[{{ $page->theme_color }}]">:</span>

                            {{-- HOURS --}}
                            <div class="text-center">
                                <div class="count-box bg-[{{ $page->theme_color }}]" data-hours>00</div>
                                <span class="label text-[{{ $page->theme_color }}]">ساعة</span>
                            </div>

                            <span class="colon text-[{{ $page->theme_color }}]">:</span>

                            {{-- MINUTES --}}
                            <div class="text-center">
                                <div class="count-box bg-[{{ $page->theme_color }}]" data-minutes>00</div>
                                <span class="label text-[{{ $page->theme_color }}]">دقائق</span>
                            </div>

                            <span class="colon text-[{{ $page->theme_color }}]">:</span>

                            {{-- SECONDS --}}
                            <div class="text-center">
                                <div class="count-box bg-[{{ $page->theme_color }}]" data-seconds>00</div>
                                <span class="label text-[{{ $page->theme_color }}]">ثواني</span>
                            </div>

                        </div>
                    </div>
                @endif
            </div>
        </section>


        {{-- IMPORTANT INFO --}}
        <section class="bg-white px-4 py-8 border-t">
            <div class="max-w-[420px] mx-auto space-y-4 text-right">

                <h2 class="text-2xl font-bold text-gray-900">
                    معلومات مهمة
                </h2>

                <p class="text-gray-700 leading-relaxed text-base overflow-text">
                    {{ $page->description }}
                </p>

            </div>
        </section>

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

        {{-- ================= REVIEWS SECTION ================= --}}
        @if ($page->reviews->count())
            <section class="bg-white rounded-lg shadow p-6 space-y-6">

                <h2 class="text-xl font-bold text-gray-800">
                    آراء العملاء
                    <span class="text-sm text-gray-500">
                        ({{ $page->reviews->count() }} تقييم)
                    </span>
                </h2>

                <div class="space-y-5">

                    @foreach ($page->reviews as $review)
                        <div class="flex gap-4 border-b pb-5 last:border-b-0">

                            {{-- Reviewer Image --}}
                            <div class="shrink-0">
                                @if ($review->reviewer_image)
                                    <img src="{{ asset($review->reviewer_image) }}" alt="{{ $review->reviewer_name }}"
                                        class="w-12 h-12 rounded-full object-cover border">
                                @else
                                    @php
                                        $names = explode(' ', trim($review->reviewer_name));
                                        $first = mb_substr($names[0] ?? '', 0, 1);
                                        $second = mb_substr($names[1] ?? '', 0, 1);
                                        $initials = mb_strtoupper($first . $second);
                                    @endphp

                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center
                   bg-blue-600 text-white font-bold border">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 space-y-1">

                                {{-- Name + Stars --}}
                                <div class="flex items-center justify-between">
                                    <strong class="text-gray-800">
                                        {{ $review->reviewer_name }}
                                    </strong>

                                    {{-- Stars --}}
                                    <div class="flex text-yellow-400 text-sm">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->stars)
                                                ★
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                    </div>
                                </div>

                                {{-- Comment --}}
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    {{ $review->comment }}
                                </p>

                            </div>
                        </div>
                    @endforeach

                </div>
            </section>
        @endif


        {{-- STICKY ORDER BUTTON --}}
        <div id="sticky-order"
            class="fixed bottom-0 inset-x-0 z-40
            bg-white border-t shadow-lg p-3
            transition-transform duration-300 ease-in-out">
            <button onclick="openOrderModal()"
                class="w-full max-w-md mx-auto block
               bg-[{{ $page->theme_color }}]
               text-white font-bold py-4 rounded-xl text-xl">
                اطلب الأن
            </button>
        </div>


        {{-- ORDER MODAL --}}
        <div id="orderModal"
            class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4 overflow-y-auto"
            onclick="closeOrderModal()">

            <div class="bg-white w-full max-w-sm sm:max-w-md md:max-w-lg rounded-xl shadow-xl relative my-8 order-modal-inner"
                onclick="event.stopPropagation()">

                {{-- CLOSE --}}
                <button onclick="closeOrderModal()"
                    class="absolute right-4 top-4 text-gray-400 text-2xl hover:text-gray-600">&times;</button>

                <div class="p-4 sm:p-6 space-y-5 modal-body-scroll">

                    <h2 class="text-2xl font-bold text-center">اطلب الأن</h2>

                    @if ($page->pageSaleActive())
                        {{-- OFFER PRODUCTS --}}
                        <div class="space-y-3" id="offersContainer">
                            {{-- OFFER 1 --}}
                            <div
                                class="offer-item flex items-center gap-3 border rounded-lg p-3 sm:p-4 cursor-pointer hover:border-[{{ $page->theme_color }}]">
                                <div class="flex flex-col sm:flex-row justify-between w-full gap-3">
                                    <div class="flex gap-3 sm:gap-4">
                                        @if ($page->images)
                                            <img src="{{ asset($page->images[0]) }}"
                                                class="w-10 sm:w-12 rounded-lg flex-shrink-0" />
                                        @endif
                                        <div class="flex flex-col gap-2">
                                            <div class="font-bold text-sm sm:text-base">
                                                اشتري <span class="text-[{{ $page->theme_color }}]">1</span> ب
                                                <span
                                                    class="text-[{{ $page->theme_color }}]">{{ $page->sale_price * 1 }}
                                                    د.إ</span>
                                            </div>
                                            <div
                                                class="bg-[{{ $page->theme_color }}] w-fit p-1 sm:p-2 text-xs sm:text-[15px] rounded-2xl text-white">
                                                وفر %{{ $page->sale_percent }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-xs sm:text-sm text-gray-500 flex flex-col gap-2 sm:gap-4 whitespace-nowrap">
                                        <span
                                            class="text-[{{ $page->theme_color }}] font-bold">{{ $page->sale_price * 1 }}
                                            د.إ</span>
                                        <span class="line-through">{{ $page->original_price * 1 }} د.إ</span>
                                    </div>
                                </div>
                            </div>

                            {{-- OFFER 2 --}}
                            <div
                                class="offer-item flex items-center gap-3 border rounded-lg p-3 sm:p-4 cursor-pointer hover:border-[{{ $page->theme_color }}]">
                                <div class="flex flex-col sm:flex-row justify-between w-full gap-3">
                                    <div class="flex gap-3 sm:gap-4">
                                        <img src="{{ asset($page->images[0]) }}"
                                            class="w-10 sm:w-12 rounded-lg flex-shrink-0" />
                                        <div class="flex flex-col gap-2">
                                            <div class="font-bold text-sm sm:text-base">
                                                اشتري <span class="text-[{{ $page->theme_color }}]">2</span> ب
                                                <span
                                                    class="text-[{{ $page->theme_color }}]">{{ $page->sale_price * 2 }}
                                                    د.إ</span>
                                            </div>
                                            <div
                                                class="bg-gradient-to-l from-orange-500 to-orange-400 w-fit p-1 sm:p-2 text-xs sm:text-[15px] rounded-2xl text-white">
                                                أفضل عرض
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-xs sm:text-sm text-gray-500 flex flex-col gap-2 sm:gap-4 whitespace-nowrap">
                                        <span
                                            class="text-[{{ $page->theme_color }}] font-bold">{{ $page->sale_price * 2 }}
                                            د.إ</span>
                                        <span class="line-through">{{ $page->original_price * 2 }} د.إ</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif


                    {{-- FORM --}}
                    <form method="POST" action="{{ route('pages.submitOrder', $page->slug) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="quantity" id="orderQuantity" value="1">

                        <input name="full_name" placeholder="الاسم بالكامل" required
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg text-sm sm:text-base">

                        <input name="phone" placeholder="رقم الهاتف" required
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg text-sm sm:text-base">

                        {{-- Governorate / City --}}
                        <select name="government" required
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg bg-white text-sm sm:text-base">
                            <option value="Abu Dhabi" selected>Abu Dhabi / أبو ظبي</option>
                            <option value="Dubai">Dubai / دبي</option>
                            <option value="Sharjah">Sharjah / الشارقة</option>
                            <option value="Ajman">Ajman / عجمان</option>
                            <option value="Al Ain">Al Ain / العين</option>
                            <option value="Fujairah">Fujairah / الفجيرة</option>
                            <option value="Umm Al-Quwain">Umm Al-Quwain / أم القيوين</option>
                            <option value="Ras Al Khaimah">Ras Al Khaimah / رأس الخيمة</option>
                        </select>

                        {{-- Address --}}
                        <textarea name="address" placeholder="العنوان بالتفصيل" required rows="3"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg resize-none text-sm sm:text-base"></textarea>

                        <button type="submit" id="submitBtn"
                            class="w-full text-white font-bold py-3 rounded-lg text-lg"
                            style="background-color: {{ $page->theme_color }}">
                            تأكيد الطلب
                        </button>
                    </form>

                </div>
            </div>
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
    let modalOpen = false;

    function openOrderModal() {
        const modal = document.getElementById('orderModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modalOpen = true;
        const sticky = document.getElementById('sticky-order');
        if (sticky) sticky.classList.add('hidden');
    }

    function closeOrderModal() {
        const modal = document.getElementById('orderModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modalOpen = false;
        const sticky = document.getElementById('sticky-order');
        if (sticky) sticky.classList.remove('hidden');
    }
</script>

<script>
    const offers = document.querySelectorAll('#offersContainer .offer-item');
    const quantityInput = document.getElementById('orderQuantity');

    if (offers.length) {
        offers[0].classList.add('border-[{{ $page->theme_color }}]', 'shadow-xl', 'border-2');
        quantityInput.value = offers[0].dataset.quantity;
    }

    offers.forEach(offer => {
        offer.addEventListener('click', () => {
            offers.forEach(o => o.classList.remove('border-[{{ $page->theme_color }}]', 'shadow-xl'));
            offer.classList.add('border-[{{ $page->theme_color }}]', 'shadow-xl', 'border-2');
            quantityInput.value = offer.dataset.quantity;

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
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form[action*="submitOrder"]');
        const btn = document.getElementById('submitBtn');

        if (!form || !btn) return;

        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.innerHTML = 'جاري تأكيد الطلب...';
        });
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


</html>
