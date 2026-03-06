<?php

namespace App\Http\Controllers;

use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Domain;
use App\Models\Order;
use App\Models\Page;
use App\Models\Pixel;
use App\Models\Product;
use App\Services\CartUserService;
use App\Services\EasyOrderService;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PageController extends Controller
{
    protected PageService $pageService;
    protected CartUserService $cartUserService;

    public function __construct(PageService $pageService, CartUserService $cartUserService)
    {
        $this->pageService = $pageService;
        $this->cartUserService = $cartUserService;
    }

    // -----------------------------------
    // Display Buy Page
    // -----------------------------------
    public function showBuyPage(Request $request, string $slug): View
    {
        $domain = request()->currentDomain();

        if ($domain === null) {
            $page = Page::with('product', 'reviews', 'pixels')
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        } else {
            $page = Page::with('product', 'reviews', 'pixels')
                ->where('slug', $slug)
                ->where('domain_id', $domain->id)
                ->where('is_active', true)
                ->firstOrFail();
        }

        if (!$page) {
            return view('pages.inactive_page');
        }

        return view('pages.buy', [
            'page' => $page,
            'product' => $page->product,
            'success' => request()->query('success')
        ]);
    }

    // -----------------------------------
    // Display Upsell Page
    // -----------------------------------
    public function showUpsellPage(string $slug, int $orderId): View
    {
        $domain = request()->currentDomain();

        if ($domain === null) {
            $page = Page::with('product', 'reviews', 'pixels')
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        } else {
            $page = Page::with('product', 'reviews', 'pixels')
                ->where('slug', $slug)
                ->where('domain_id', $domain->id)
                ->where('is_active', true)
                ->firstOrFail();
        }

        if (!$page) {
            return view('pages.inactive_page');
        }

        $order = Order::findOrFail($orderId);

        return view('pages.upsell', [
            'page' => $page,
            'order' => $order,
        ]);
    }

    // -----------------------------------
    // Submit Order from Buy Page
    // -----------------------------------
    public function submitOrder(
        Request $request,
        string $slug,
        EasyOrderService $easyOrderService
    ): RedirectResponse {
        $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'government' => 'required|string',
            'address' => 'required|string',
        ]);

        Log::info('Submitting order', $request->all());

        $domain = request()->currentDomain();

        if ($domain === null) {
            $page = Page::with('product', 'reviews', 'upsellProducts')
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        } else {
            $page = Page::with('product', 'reviews', 'upsellProducts')
                ->where('slug', $slug)
                ->where('domain_id', $domain->id)
                ->where('is_active', true)
                ->firstOrFail();
        }

        $cartUser = null;
        if ($request->input('order_index_string')) {
            $cartUser = $this->cartUserService->findByOrderIndex($request->input('order_index_string'));
        }

        $quantity = (int) $request->input('quantity', 1);
        $sellPrice = $request->input('offer_price', null) ?? $page->sale_price ?? $page->original_price;
        $order = $easyOrderService->createFromPage(
            $request,
            $page->product,
            $sellPrice,
            $quantity,
            $page->slug ? route('pages.buy', $page->slug) : null
        );

        Log::info('Order created', ['order_id' => $order->id, 'sell_price' => $sellPrice]);

        if ($cartUser) {
            $this->cartUserService->deleteAfterPurchase($cartUser->order_index_string);
        }

        // Redirect to upsell if upsell products exist
        if ($page->upsellProducts->count() > 0) {
            $upsellParams = ['slug' => $page->slug, 'orderId' => $order->id];

            if ($request->input('utm_source')) {
                $upsellParams['utm_source'] = $request->input('utm_source');
            }
            if ($request->input('utm_campaign')) {
                $upsellParams['utm_campaign'] = $request->input('utm_campaign');
            }

            Log::info('Redirecting to upsell page', ['upsellParams' => $upsellParams]);

            return redirect()->route('pages.showUpsellPage', $upsellParams);
        }

        return redirect()->route('pages.buy', [
            'page' => $page,
            'success' => 1,
            'sellPrice' => $sellPrice,
            'order_id' => $order->id
        ]);
    }

    // -----------------------------------
    // Submit Order from Upsell Page
    // -----------------------------------
    public function submitOrderFromUpsellPage(
        Request $request,
    ): RedirectResponse {
        Log::info('Submitting upsell products', $request->all());

        $page = Page::findOrFail($request->page_id);
        $order = Order::findOrFail($request->order_id);

        $upsellProductIds = $request->input('selected_upsell_products', []);

        $upsellTotal = 0;

        if (!empty($upsellProductIds)) {
            foreach ($upsellProductIds as $productId) {
                $upsellProduct = $page->upsellProducts()->where('products.id', $productId)->first();

                if ($upsellProduct) {
                    $price = $upsellProduct->pivot->price ?? $upsellProduct->price;

                    $order->products()->attach($productId, [
                        'price' => $price,
                        'quantity' => 1,
                        'real_price' => $price,
                    ]);

                    $upsellProduct->increment('sales_number');
                    $upsellTotal += $price;
                }
            }
        }

        $finalPrice = $order->products->sum(fn($p) => $p->pivot->price * $p->pivot->quantity);

        Log::info('Upsell products added', [
            'order_id' => $order->id,
            'upsell_total' => $upsellTotal,
            'final_price' => $finalPrice,
        ]);

        return redirect()->route('pages.buy', [
            'page' => $page,
            'success' => 1,
            'sellPrice' => $finalPrice,
            'order_id' => $order->id
        ]);
    }

    // -----------------------------------
    // Admin CRUD
    // -----------------------------------
    public function index(Request $request): View
    {
        $pages = $this->pageService->index($request);
        $domains = Domain::orderBy('domain')->get();
        $products = Product::orderBy('name')->get();
        $pixels = Pixel::orderBy('name')->get();

        return view('pages.index', compact('pages', 'domains', 'products', 'pixels'));
    }

    public function create(): View
    {
        $products = Product::all();
        $domains = Domain::all();
        $pixels = Pixel::where('is_active', true)->get();
        return view('pages.create', compact('products', 'domains', 'pixels'));
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        return $this->pageService->store($validatedData);
    }

    public function show(Page $page): View
    {
        return $this->pageService->show($page);
    }

    public function edit(Page $page): View
    {
        $products = Product::all();
        $domains = Domain::all();
        $pixels = Pixel::where('is_active', true)->get();
        $page->load('pixels');
        return $this->pageService->edit($page, $products, $domains, $pixels);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $validatedData = $request->validated();
        return $this->pageService->update($validatedData, $page);
    }

    public function destroy(Page $page): JsonResponse|RedirectResponse
    {
        return $this->pageService->destroy($page);
    }

    // -----------------------------------
    // Helper: Delete image
    // -----------------------------------
    public function deleteImage(Page $page, Request $request)
    {
        $request->validate(['index' => 'required|integer|min:0']);

        $images = $page->images ?? [];
        $index = $request->input('index');

        if (!isset($images[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'الصورة غير موجودة'
            ], 404);
        }

        $imagePath = $images[$index];
        if (Storage::disk('direct_public')->exists($imagePath)) {
            Storage::disk('direct_public')->delete($imagePath);
        }

        array_splice($images, $index, 1);
        $page->images = $images;
        $page->save();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح',
            'images' => $images
        ]);
    }

    // -----------------------------------
// Helper: Delete any image by path
// -----------------------------------
    public function deleteAnyImage(Request $request, Page $page)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');

        if (!Storage::disk('direct_public')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'الصورة غير موجودة',
            ], 404);
        }

        Storage::disk('direct_public')->delete($path);

        $images = $page->images ?? [];
        $images = array_values(array_filter($images, fn($img) => $img !== $path));
        $page->images = $images;

        $offers = $page->offers ?? [];

        foreach ($offers as &$offer) {
            if (isset($offer['image']) && $offer['image'] === $path) {
                $offer['image'] = null;
            }
        }

        $page->offers = $offers;

        $page->reviews()
            ->where('reviewer_image', $path)
            ->update(['reviewer_image' => null]);

        $page->upsellProducts()
            ->wherePivot('image', $path)
            ->updateExistingPivot(
                $page->upsellProducts()
                    ->wherePivot('image', $path)
                    ->pluck('products.id')
                    ->toArray(),
                ['image' => null]
            );

        $page->save();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح',
        ]);
    }

    // -----------------------------------
    // Toggle Page Active
    // -----------------------------------
    public function toggleActive(Page $page): RedirectResponse
    {
        $page->is_active = !$page->is_active;
        $page->save();

        return redirect()->back()->with('success', 'تم تحديث حالة الصفحة بنجاح');
    }

    public function duplicate(Page $page)
    {
        try {
            $newPage = $this->pageService->duplicate($page);

            return redirect()
                ->route('pages.index')
                ->with('success', "تم تكرار الصفحة بنجاح: {$newPage->name}");

        } catch (\Exception $e) {
            return redirect()
                ->route('pages.index')
                ->with('error', 'حدث خطأ أثناء تكرار الصفحة');
        }
    }
}
