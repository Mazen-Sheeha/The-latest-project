<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Services\EasyOrderService;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PageController extends Controller
{
    protected PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    // -----------------------------------
    // Display Buy Page
    // -----------------------------------
    public function showBuyPage(Request $request, string $slug): View
    {
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

        if (!$page || !$page->is_active) {
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

        if (!$page || !$page->is_active) {
            return view('pages.inactive_page');
        }

        // If orderId is 0, it means we're coming from buy page (no order created yet)
        $order = null;
        $orderData = null;

        if ($orderId > 0) {
            $order = Order::findOrFail($orderId);
        } else {
            // Get order data from session (from buy page form)
            $orderData = session('order_data');
        }

        return view('pages.upsell', [
            'page' => $page,
            'order' => $order,
            'orderData' => $orderData,
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

        if ($page->upsellProducts->count() > 0) {
            session([
                'order_data' => $request->only('full_name', 'phone', 'government', 'address', 'quantity'),
                'page_id' => $page->id,
            ]);

            return redirect()->route('pages.showUpsellPage', [
                'slug' => $page->slug,
                'orderId' => 0,
            ]);
        }

        $order = $easyOrderService->createFromPage($request, $page->product);

        return redirect()->route('pages.buy', [
            'page' => $page,
            'success' => 1,
        ]);
    }

    // -----------------------------------
    // Submit Order from Upsell Page
    // -----------------------------------
    public function submitOrderFromUpsellPage(
        Request $request,
        EasyOrderService $easyOrderService
    ): RedirectResponse {
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
        $order = $easyOrderService->createFromPage($request, $mainProduct);

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

        return redirect()->route('pages.buy', [
            'page' => $page,
            'success' => 1,
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
        return view('pages.create', compact('products', 'domains'));
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
        return $this->pageService->edit($page, $products, $domains);
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
        if (file_exists($imagePath))
            unlink($imagePath);

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
