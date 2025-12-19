<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Website;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EasyOrderService
{
    public function webhook(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            $cartItems = $data['cart_items'] ?? [];

            if (Order::where('ref', $data['id'])->exists()) {
                return response()->json(['message' => 'Upsell or duplicate order'], 200);
            }

            $firstSku = $cartItems[0]['product']['sku'] ?? null;
            $shippingPrice = Product::where("code", $firstSku)->first()?->shipping_company->price ?? 0;
            $source = $data['utm_source'] ?? null;
            $campaignName = $data['utm_campaign'] ?? null;

            $campaign = Campaign::where('source', $source)
                ->where('campaign', $campaignName)
                ->first();

            $newProductCodes = collect($cartItems)
                ->pluck('product.sku')
                ->filter()
                ->values();

            $oldOrder = Order::where('name', $data['full_name'])
                ->where('phone', $data['phone'])
                ->where('order_status', 'waiting_for_confirmation')
                ->whereHas('products', function ($q) use ($newProductCodes) {
                    $q->whereIn('code', $newProductCodes);
                })
                ->first();

            if ($oldOrder) {
                $oldOrder->delete();
            }

            $order = Order::create([
                'name' => $data['full_name'],
                'phone' => $data['phone'],
                'url' => $this->getDomainFromStore($data['store_id']) ? 'https://www.' . $this->getDomainFromStore($data['store_id']) . '/products/' . ($cartItems[0]['product']['slug'] ?? '') . ((isset($data['utm_campaign']) ? "?utm_campaign=" . $data['utm_campaign'] . (isset($data['utm_source']) ? "&utm_source=" . $data['utm_source'] : "") : "")) : NULL,
                'order_status' => 'waiting_for_confirmation',
                'shipping_price' => $shippingPrice,
                'city' => $data['government'],
                'address' => $data['address'],
                'ref' => $data['id'],
                'campaign_id' => $campaign?->id,
            ]);

            $productData = [];

            foreach ($cartItems as $item) {
                $sku = $item['product']['sku'] ?? null;
                $qty = $item['quantity'] ?? 1;
                $product = Product::where('code', $sku)->first();

                if (!$product) continue;

                $salePrice = $item['price'] && $item['price'] !== 0
                    ? $item['price'] : $product->price;

                $price = (float) $product->price;

                $product->sales_number += $qty;
                $product->save();

                $productData[$product->id] = [
                    'price' => $salePrice,
                    'quantity' => $qty,
                    'real_price' => $price
                ];
            }

            $order->products()->sync($productData);

            $notifiableUsers = User::where('id', 1)
                ->orWhereHas('permissions', function ($query) {
                    $query->where('name', 'صلاحية الطلبات');
                })->get();

            foreach ($notifiableUsers as $user) {
                $user->notify(new NewOrderNotification($order));
            }

            DB::commit();
            return response()->json(['message' => 'تم حفظ الطلب بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطأ أثناء معالجة الطلب',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    private function getDomainFromStore($storeId)
    {
        $storesList = Website::pluck('domain', 'key')->toArray();

        return $storesList[$storeId] ?? null;
    }
}
