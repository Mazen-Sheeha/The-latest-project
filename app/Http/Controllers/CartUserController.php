<?php

namespace App\Http\Controllers;

use App\Models\CartUser;
use App\Models\Page;
use App\Services\CartUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class CartUserController extends Controller
{
    protected CartUserService $cartUserService;

    public function __construct(CartUserService $cartUserService)
    {
        $this->cartUserService = $cartUserService;
    }

    public function index()
    {
        return $this->cartUserService->index();
    }

    public function store(Request $request)
    {
        return $this->cartUserService->store($request->all());
    }


    public function trackCartUser(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'order_index_string' => 'nullable|string',
            'full_name' => 'nullable|string',
            'government' => 'nullable|string',
            'address' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'offer_price' => 'nullable|numeric|min:0',
        ]);

        $domain = request()->currentDomain();

        if ($domain === null) {
            $page = Page::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        } else {
            $page = Page::query()
                ->where('slug', $slug)
                ->where('domain_id', $domain->id)
                ->where('is_active', true)
                ->firstOrFail();
        }

        $cartUser = $this->cartUserService->upsertByOrderIndex(
            $request->input('order_index_string'),
            [
                'full_name' => $request->filled('full_name') ? $request->input('full_name') : null,
                'phone' => $request->input('phone'),
                'government' => $request->filled('government') ? $request->input('government') : null,
                'address' => $request->filled('address') ? $request->input('address') : null,
                'page_id' => $page->id,
                'offer_price' => $request->input('offer_price', $page->sale_price ?? $page->original_price),
                'quantity' => $request->input('quantity', 1),
            ]
        );

        Log::info('Cart user tracked', ['cart_user_id' => $cartUser->id, 'phone' => $cartUser->phone, 'order_index_string' => $cartUser->order_index_string]);

        return response()->json([
            'success' => true,
            'order_index_string' => $cartUser->order_index_string,
        ]);
    }

    public function destroy($id)
    {
        return $this->cartUserService->destroy($id);
    }

    public function destroyAll()
    {
        CartUser::query()->delete();

        return to_route('cart_users.index')
            ->with(['success' => 'تم حذف جميع السجلات بنجاح']);
    }

}
