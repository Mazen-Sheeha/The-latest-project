<?php

namespace App\Services;

use App\Models\CartUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartUserService
{
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
}
