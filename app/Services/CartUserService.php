<?php

namespace App\Services;

use App\Models\CartUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CartUserService
{
    protected EasyOrderService $easyOrderService;
    public function __construct(EasyOrderService $easyOrderService)
    {
        $this->easyOrderService = $easyOrderService;
    }

    public function index(): View
    {
        $cartUsers = CartUser::with('page')->orderBy('id', 'DESC')->paginate(100);
        return view('cart_users.index', compact('cartUsers'));
    }

    public function store(array $data): CartUser
    {
        return CartUser::create($data);
    }

    public function findByOrderIndex(string $orderIndex)
    {
        return CartUser::where('order_index_string', $orderIndex)->first();
    }

    public function upsertByOrderIndex(?string $orderIndexString, array $data): CartUser
    {
        $cartUser = $orderIndexString
            ? CartUser::firstOrNew(['order_index_string' => $orderIndexString])
            : new CartUser();

        $cartUser->order_index_string = $cartUser->order_index_string ?: uniqid('order_', true);

        foreach ([
            'full_name',
            'phone',
            'government',
            'address',
            'page_id',
            'offer_price',
            'quantity',
            'utm_source',
            'utm_campaign',
        ] as $field) {
            if (array_key_exists($field, $data) && !is_null($data[$field])) {
                $cartUser->{$field} = $data[$field];
            }
        }

        $cartUser->save();

        return $cartUser;
    }

    public function deleteAfterPurchase(string $orderIndex): void
    {
        CartUser::where('order_index_string', $orderIndex)->delete();
    }

    public function show(string $id): View
    {
        $cartUser = CartUser::findOrFail($id);
        return view('cart_users.show', compact('cartUser'));
    }

    public function destroy(string $id): RedirectResponse
    {
        $cartUser = CartUser::findOrFail($id);
        $cartUser->delete();

        return to_route('cart_users.index')
            ->with(['success' => 'تم حذف العميل بنجاح']);
    }

    public function completeOrder(int $id): RedirectResponse
    {
        Log::info('[CompleteOrder] Starting', ['cart_user_id' => $id]);

        $cartUser = CartUser::with('page.product')->findOrFail($id);

        Log::info('[CompleteOrder] CartUser found', [
            'cart_user' => $cartUser->toArray(),
        ]);

        $page = $cartUser->page;
        Log::info('[CompleteOrder] Page', [
            'page_id' => $page?->id,
            'page_title' => $page?->title,
        ]);

        $mainProduct = $page->product;
        Log::info('[CompleteOrder] Product', [
            'product_id' => $mainProduct?->id,
            'product_name' => $mainProduct?->name,
        ]);

        $sellPrice = $cartUser->offer_price;
        $quantity = $cartUser->quantity ?? 1;

        Log::info('[CompleteOrder] Pricing', [
            'sell_price' => $sellPrice,
            'quantity' => $quantity,
        ]);

        $syntheticRequest = Request::create('', 'POST', [
            'full_name' => $cartUser->full_name,
            'phone' => $cartUser->phone,
            'government' => $cartUser->government,
            'address' => $cartUser->address,
            'quantity' => $quantity,
            'offer_price' => $sellPrice,
            'order_index_string' => $cartUser->order_index_string,
            'utm_source' => $cartUser->utm_source,
            'utm_campaign' => $cartUser->utm_campaign,
        ]);
        Log::info('[CompleteOrder] Synthetic request built', $syntheticRequest->all());

        try {
            $order = $this->easyOrderService->createFromPage($syntheticRequest, $mainProduct, $sellPrice, $quantity, $page->slug ? route('pages.buy', $page->slug) : null);
            Log::info('[CompleteOrder] Order created successfully', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('[CompleteOrder] Failed to create order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        $cartUser->delete();

        Log::info('[CompleteOrder] CartUser deleted', ['cart_user_id' => $id]);


        return to_route('cart_users.index')
            ->with(['success' => 'تم إكمال الطلب وحذف العميل بنجاح']);
    }
}
