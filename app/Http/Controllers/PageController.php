<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Services\EasyOrderService;
use App\Services\PageService;
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

    public function showBuyPage(string $slug): View
    {
        $page = Page::with('product', 'reviews')->where(['slug' => $slug])->first();

        if (!$page || !$page->is_active) {
            return view('pages.inactive_page');
        }


        return view('pages.buy', [
            'page' => $page,
            'product' => $page->product,
        ]);
    }

    public function showUpsellPage(string $slug, int $orderId): View
    {
        $page = Page::with('product', 'reviews')->where(['slug' => $slug])->first();

        if (!$page || !$page->is_active) {
            return view('pages.inactive_page');
        }

        $order = Order::findOrFail($orderId);

        return view('pages.upsell', [
            'page' => $page,
            'order' => $order,
        ]);
    }

    public function submitOrderFromUpsellPage(
        Request $request,
        Product $product,
        EasyOrderService $easyOrderService
    ): RedirectResponse {
        $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'government' => 'required|string',
            'address' => 'required|string',
        ]);

        Log::info('Submitting order from upsell page', $request->all());

        $easyOrderService->createFromPage($request, $product);

        return redirect()->back()->with('success', 'تم إرسال الطلب بنجاح');
    }

    public function submitOrder(
        Request $request,
        Page $page,
        EasyOrderService $easyOrderService
    ): RedirectResponse {
        $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'government' => 'required|string',
            'address' => 'required|string',
        ]);

        Log::info('Submitting order', $request->all());

        $order = $easyOrderService->createFromPage($request, $page->product);

        if ($page->upsellProducts->count() > 0) {
            return redirect()->route('pages.showUpsellPage', [
                'slug' => $page->slug,
                'orderId' => $order->id,
            ]);
        }

        return redirect()->back()->with('success', 'تم إرسال الطلب بنجاح');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return $this->pageService->index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::all();
        return view('pages.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->pageService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page): View
    {
        return $this->pageService->show($page);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page): View
    {
        $products = Product::all();
        return $this->pageService->edit($page, $products);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        return $this->pageService->update($request, $page);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page): RedirectResponse
    {
        return $this->pageService->destroy($page);
    }

    public function deleteImage(Page $page, Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0'
        ]);

        $index = $request->input('index');
        $images = $page->images ?? [];

        if (!isset($images[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'الصورة غير موجودة'
            ], 404);
        }

        $imagePath = $images[$index];

        if (file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
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

    public function toggleActive(Page $page): RedirectResponse
    {
        $page->is_active = !$page->is_active;
        $page->save();

        return redirect()->back()->with('success', 'تم تحديث حالة الصفحة بنجاح');
    }

}
