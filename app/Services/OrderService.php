<?php

namespace App\Services;

use App\Exports\OrdersExport;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class OrderService
{
    public function index(Request $request)
    {
        $query = Order::query();

        $query->with(['products', 'orderProducts', 'blockedNumbers', 'ordersWithSamePhone'])
            ->withCount('ordersWithSamePhone');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', "LIKE", "%" . $request->search . "%")
                    ->orWhere("phone", $request->search)
                    ->orWhere("tracking_number", "LIKE", '%' . $request->search . "%");
            });
        }

        if ($request->city && $request->city !== "all") {
            $query->where('city', "LIKE", "%" . $request->city . '%');
        }

        if ($request->upsell && $request->upsell !== "all") {
            if ($request->upsell === 'upsell') {
                $query->whereHas('orderProducts', function ($q) {}, '>', 1);
            } elseif ($request->upsell === 'not_upsell') {
                $query->has('orderProducts', '=', 1);
            }
        }

        if ($request->products_ids && !in_array('all', $request->products_ids)) {
            $query->whereHas('orderProducts', function ($q) use ($request) {
                $q->whereIn('product_id', $request->products_ids);
            });
        }

        if ($request->shipping_company_id && $request->shipping_company_id !== "all") {
            $query->whereHas('products', function ($q) use ($request) {
                $q->where('shipping_company_id', $request->shipping_company_id);
            });
        }

        if ($request->from && $request->to) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        if ($request->paid && $request->paid !== 'all') {
            $query->where("paid", $request->paid == "paid" ? true : ($request->paid == "not_paid" ? false : true));
        }

        if ($request->min_price || $request->max_price) {
            $query->whereHas('orderProducts', function ($q) use ($request) {
                $q->selectRaw('order_id, SUM(price * quantity) as total_price')
                    ->groupBy('order_id')
                    ->havingRaw('
                        (? IS NULL OR SUM(price * quantity) >= ?)
                        AND
                        (? IS NULL OR SUM(price * quantity) <= ?)
                    ', [
                        $request->min_price,
                        $request->min_price,
                        $request->max_price,
                        $request->max_price
                    ]);
            });
        }


        if ($request->order_status && $request->order_status !== "all") {
            $query->where("order_status", $request->order_status);
        }

        $orders = $query
            ->orderByRaw("
                CASE 
                    WHEN order_status = 'postponed' AND date_of_postponement > NOW() THEN 1
                    ELSE 0
                END ASC
            ")
            ->orderBy('created_at', 'DESC')
            ->paginate(70)
            ->withQueryString();

        $orders->getCollection()->transform(function ($order) {
            $order->total = $order->orderProducts->sum(fn($product) => $product->price * $product->quantity);
            return $order;
        });
        $shipping_companies = ShippingCompany::select("id", 'name')->get();
        $products = Product::select("id", 'name', 'code')->get();

        return view('orders.index', compact('orders', 'shipping_companies', 'products'));
    }



    public function show(string $id)
    {
        $order = Order::with(['products' => function ($query) {
            $query->withPivot(['price', 'quantity']);
        }])->findOrFail($id);

        $total = $order->products->sum(function ($product) {
            return $product->pivot->price * $product->pivot->quantity;
        });

        return view('orders.show', compact('order', 'total'));
    }

    public function create()
    {
        $products = Product::with('shipping_company')->get();
        if ($products->count() == 0) {
            if (Gate::allows('access-products')) {
                return ShippingCompany::count() === 0 ?
                    to_route('shipping_companies.index')->withErrors(['message' => "لا يوجد منتوجات للطلب ، من فضلك أضف شركة شحن ثم أضف منتوج تابع لهذه الشركة ثم يمكنك بعدها إضافة طلبات"])
                    : to_route('products.create')->withErrors(['message' => 'لا توجد منتوجات للطلب ، أضف منتوج']);
            }
            return back()->withErrors(['message' => 'لا توجد منتوجات للطلب']);
        }
        $campaigns = Campaign::all();
        return view('orders.create', compact("products", 'campaigns'));
    }

    public function store(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        if (isset($validated['campaign_id'])) {
            $validated['url'] = Campaign::findOrFail($validated['campaign_id'])->url;
        }
        try {
            DB::beginTransaction();
            $order = Order::create($validated);
            $productData = [];
            foreach ($validated['product_ids'] as  $proId) {
                $product = Product::findOrFail($proId);
                $product->sales_number = (int) $product->sales_number + (int) $validated['quantities'][$proId];
                $product->save();
                $productData[$proId] = [
                    'price' => $validated['prices'][$proId],
                    'quantity' => $validated['quantities'][$proId],
                    'real_price' => $product->price
                ];
            }
            $order->products()->sync($productData);
            DB::commit();
            return to_route('orders.index')->with(['success' => 'تم إضافة الطلب بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => "حدث خطأ أثناء إضافة المنتوج"]);
        }
    }

    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        $products = Product::with('shipping_company')->get();
        if ($products->count() == 0) {
            if (Gate::allows('access-products')) {
                return ShippingCompany::count() === 0 ?
                    to_route('shipping_companies.index')->withErrors(['message' => "لا يوجد منتوجات ، من فضلك أضف شركة شحن ثم أضف منتوج تابع لهذه الشركة ثم يمكنك بعدها إضافة طلبات"])
                    : to_route('products.create')->withErrors(['message' => 'لا توجد منتوجات ، أضف منتوج']);
            }
            return back()->withErrors(['message' => 'لا توجد منتوجات']);
        }
        $campaigns = Campaign::all();
        return view('orders.edit', compact('order', 'products', 'campaigns'));
    }

    public function update(UpdateOrderRequest $request, string $id)
    {
        $validated = $request->validated();
        $order = Order::findOrFail($id);
        if (isset($validated['campaign_id'])) {
            $validated['url'] = Campaign::findOrFail($validated['campaign_id'])->url;
        }
        try {
            DB::beginTransaction();
            $order->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'city' => $validated['city'],
                'notes' => $validated['notes'] ?? null,
                'address' => $validated['address'],
                'tracking_number' => $validated['tracking_number'] ?? null,
                'url' => $validated['url'],
                'shipping_price' => $validated['shipping_price'] ?? 0,
                'campaign_id' => $validated['campaign_id'] ?? null,
            ]);

            $productData = [];
            $rejectedStatuses = ['rejected_with_phone', 'rejected_in_shipping'];

            foreach ($validated['product_ids'] as $proId) {
                $product = Product::findOrFail($proId);
                $orderProduct = OrderProduct::where('order_id', $order->id)
                    ->where('product_id', $product->id)
                    ->first();

                $currentQuantity = $orderProduct ? $orderProduct->quantity : 0;
                $newQuantity = (int)$validated['quantities'][$proId];
                $quantityDifference = $newQuantity - $currentQuantity;

                if (!in_array($order->order_status, $rejectedStatuses)) {
                    if ($order->order_status === "exchanged") {
                        $quantityDifference *= 2;
                    }
                    $product->sales_number += $quantityDifference;
                    $product->save();
                }

                $productData[$proId] = [
                    'price' => $validated['prices'][$proId],
                    'quantity' => $newQuantity,
                    'real_price' => $product->price
                ];
            }
            $order->products()->sync($productData);
            DB::commit();
            return to_route('orders.index')->with(['success' => 'تم تعديل الطلب بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => "حدث خطأ أثناء تعديل الطلب: " . $e->getMessage()]);
        }
    }

    public function changeOrderStatus(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        try {
            DB::beginTransaction();
            foreach ($order->orderProducts as $orderProduct) {
                $product = Product::findOrFail($orderProduct->product_id);
                $newStatus = $request->status;
                $oldStatus = $order->order_status;
                $rejectedStatuses = ['rejected_with_phone', 'rejected_in_shipping'];
                switch ($newStatus) {
                    case 'exchanged':
                        if ($oldStatus !== 'exchanged') {
                            $qtyToUpdateWith =  $orderProduct->quantity;
                            if (in_array($oldStatus, $rejectedStatuses)) $qtyToUpdateWith = $qtyToUpdateWith * 2;
                            $product->sales_number += $qtyToUpdateWith;
                        }
                        break;
                    case "rejected_with_phone":
                    case "rejected_in_shipping":
                        if (!in_array($oldStatus, $rejectedStatuses)) {
                            if ($oldStatus === 'exchanged') {
                                $product->sales_number -= $orderProduct->quantity * 2;
                            } else {
                                $product->sales_number -= $orderProduct->quantity;
                            }
                        }
                        break;
                    case 'postponed':
                        if (!$request->date) {
                            DB::rollBack();
                            return response()->json(['success' => false, 'message' => "لابد من إدخال التاريخ"]);
                        }
                        $order->date_of_postponement = $request->date;
                        break;
                    default:
                        switch ($oldStatus) {
                            case 'rejected_with_phone':
                            case 'rejected_in_shipping':
                                $product->sales_number += $orderProduct->quantity;
                                break;
                            case 'exchanged':
                                $product->sales_number -= $orderProduct->quantity;
                                break;
                            case 'postponed':
                                $order->date_of_postponement = NULL;
                                break;
                            default:
                                break;
                        }
                        break;
                }
                $product->save();
            }
            $order->order_status = $request->status;
            $order->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => "تم تغيير حالة الطلب بنجاح"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => true, 'message' => "حدث خطأ أثناء تغيير حالة الطلب "]);
        }
    }

    public function changePaymentStatus(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $order->paid = !$order->paid;
        $order->save();
        return response()->json(['success' => true, 'message' => $order->paid ? "تم تأكيد الدفع للطلب $order->id" : "تم تغيير حالة الدفع للطلب $order->id"]);
    }

    public function changeTrackingNumber(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $order->tracking_number = $request->tracking_number;
        $order->save();
        return response()->json(['success' => true, 'message' => "تم تغيير رقم التتبع بنجاح"]);
    }

    public function export()
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    }

    public function destroy(string $id)
    {
        if (!Gate::allows('access-delete-any-thing')) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا']);
        };
        $order = Order::findOrFail($id);
        try {
            DB::beginTransaction();
            if (
                $order->order_status === 'waiting_for_confirmation'
                || $order->order_status === "no_response"
                || $order->order_status === "postponed"
            ) {
                foreach ($order->orderProducts as $order_product) {
                    $product = Product::findOrFail($order_product->product_id);
                    $product->sales_number = (float) $product->sales_number - (float) $order_product->quantity;
                    $product->save();
                }
            }
            $order->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'تم حذف الطلب بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ برجاء المحاولة في وقت لاحق']);
        }
    }
}
