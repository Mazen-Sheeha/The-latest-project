<?php

namespace App\Http\Controllers;

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
    public function showUpsellPage(string $slug, int $orderId = 0): View
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

        // If orderId is 0, it means we're coming from buy page (no order created yet)
        $order = null;
        $orderData = null;

        $orderIndexString = null;
        if ($orderId > 0) {
            $order = Order::findOrFail($orderId);
        } else {
            // Get order data from session (from buy page form)
            $orderData = session('order_data');
            $orderOfferPrice = session('offer_price');
            if (isset($orderData['order_index_string'])) {
                $orderIndexString = $orderData['order_index_string'];
            }
        }

        return view('pages.upsell', [
            'page' => $page,
            'order' => $order,
            'orderData' => $orderData,
            'offerPrice' => $orderOfferPrice ?? null,
            'orderIndexString' => $orderIndexString,
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
            $page = Page::with('product', 'reviews')
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        } else {
            $page = Page::with('product', 'reviews')
                ->where('slug', $slug)
                ->where('domain_id', $domain->id)
                ->where('is_active', true)
                ->firstOrFail();
        }

        $cartUser = null;
        if ($request->input('order_index_string')) {
            $cartUser = $this->cartUserService->findByOrderIndex($request->input('order_index_string'));
        }

        if ($page->upsellProducts->count() > 0) {
            session([
                'order_data' => $request->only('full_name', 'phone', 'government', 'address', 'quantity', 'order_index_string'),
                'page_id' => $page->id,
                'offer_price' => $request->input('offer_price', null) ?? $page->sale_price ?? $page->original_price,
                'orderIndexString' => $request->input('order_index_string'),
            ]);

            return redirect()->route('pages.showUpsellPage', [
                'slug' => $page->slug,
                'orderId' => 0,
            ]);
        }

        $quantity = (int) $request->input('quantity', 1);
        $sellPrice = $request->input('offer_price', null) ?? $page->sale_price ?? $page->original_price;
        $order = $easyOrderService->createFromPage($request, $page->product, $sellPrice, $quantity);

        if ($cartUser) {
            $this->cartUserService->deleteAfterPurchase($cartUser->order_index_string);
        }

        Log::info('Order created successfully', ['final_price' => $sellPrice, 'order_id' => $order->id]);
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
        EasyOrderService $easyOrderService
    ): RedirectResponse {
        // dd($request->all());
        $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'government' => 'required|string',
            'address' => 'required|string',
        ]);

        Log::info('Submitting order from upsell page', $request->all());

        $page = Page::findOrFail($request->page_id);

        // Get the selected upsell product IDs from the form
        $upsellProductIds = $request->input('selected_upsell_products', []);

        // Create order with main product
        $mainProduct = $page->product;
        $quantity = (int) $request->input('quantity', 1);
        $sellPrice = $request->input('offer_price', null) ?? $page->sale_price ?? $page->original_price;
        $order = $easyOrderService->createFromPage($request, $mainProduct, $sellPrice, $quantity);

        // Add selected upsell products to the same order
        if (!empty($upsellProductIds)) {
            foreach ($upsellProductIds as $productId) {
                $upsellProduct = $page->upsellProducts()->where('product_id', $productId)->first();

                if ($upsellProduct) {
                    // Add the upsell product to the order with custom price if set
                    $price = $upsellProduct->pivot->price ?? $upsellProduct->price;

                    $order->products()->attach($productId, [
                        'price' => $price,
                        'quantity' => 1,
                        'real_price' => $price,
                    ]);
                    $upsellProduct->increment('sales_number');
                }
            }
        }

        $upsellTotal = $page->upsellProducts()
            ->whereIn('product_id', $upsellProductIds)
            ->get()
            ->sum(function ($product) {
                return $product->pivot->price ?? $product->price;
            });

        $finalPrice = $sellPrice + $upsellTotal;

        Log::info('Order created successfully from Upesll', ['final_price' => $finalPrice, 'order_id' => $order->id]);


        $this->cartUserService->deleteAfterPurchase($request->input('order_index_string'));

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
    public function index(): View
    {
        return $this->pageService->index();
    }

    public function create(): View
    {
        $products = Product::all();
        $domains = Domain::all();
        $pixels = Pixel::where('is_active', true)->get();
        return view('pages.create', compact('products', 'domains', 'pixels'));
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->pageService->store($request);
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
        // Eager load the pixels relationship for the edit form
        $page->load('pixels');
        return $this->pageService->edit($page, $products, $domains, $pixels);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        return $this->pageService->update($request, $page);
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
    // Toggle Page Active
    // -----------------------------------
    public function toggleActive(Page $page): RedirectResponse
    {
        $page->is_active = !$page->is_active;
        $page->save();

        return redirect()->back()->with('success', 'تم تحديث حالة الصفحة بنجاح');
    }
}
