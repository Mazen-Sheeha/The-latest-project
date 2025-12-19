<?php

namespace App\Services;

use App\Models\{Budget, Order, Product};
use Illuminate\Http\Request;

class StatisticService
{
    public function statistics(Request $request)
    {
        $products = Product::all();

        $orderQuery = Order::query()->with('orderProducts');
        $budgetQuery = Budget::query();

        if ($request->filled('city') && $request->city !== 'all') {
            $orderQuery->where('city', 'LIKE', "%{$request->city}%");
        }

        if ($request->filled('upsell') && $request->upsell !== 'all') {
            $orderQuery->whereHas('orderProducts', function ($q) {}, $request->upsell === 'upsell' ? '>' : '=', 1);
        }

        if ($request->filled('products_ids') && !in_array('all', $request->products_ids)) {
            $orderQuery->whereHas('orderProducts', function ($q) use ($request) {
                $q->whereIn('product_id', $request->products_ids);
            });
            $productsCodes = $products
                ->whereIn('id', $request->products_ids)
                ->pluck('code')
                ->toArray();
            $budgetQuery->with('campaign')->whereHas(
                'campaign.adset',
                function ($q) use ($productsCodes) {
                    $q->whereIn('name', $productsCodes);
                }
            );
        }


        if ($request->filled(['from', 'to'])) {
            $orderQuery->where('created_at', '>=', $request->from)
                ->where('created_at', '<', \Carbon\Carbon::parse($request->to)->startOfDay());

            $budgetQuery->where('date', '>=', $request->from)
                ->where('date', '<', $request->to);
        }


        $orders = $orderQuery->get();

        $ordersCount = $orders->count();
        $allOrdersCount = Order::count();
        $amountSpentOnAds = $budgetQuery->sum('budget');

        $productsSalesCost = $orders->whereIn('order_status', ['received', 'exchanged'])->sum(fn($order) => $order->orderProducts->sum(fn($p) => $p->quantity * ($order->order_status == 'exchanged' ? 2 : 1) * $p->real_price));

        $deliveredOrders = $orders->whereIn('order_status', ['received', 'exchanged']);
        $deliveredPiecesCount = $deliveredOrders->sum(fn($order) => $order->orderProducts->sum('quantity'));
        $deliveredCash = $deliveredOrders->sum(fn($order) => $order->orderProducts->sum(fn($p) => $p->price * $p->quantity));
        $unpaidCash = $deliveredOrders->where('paid', false)->sum(fn($order) => $order->orderProducts->sum(fn($p) => $p->price * $p->quantity) - $order->shipping_price);

        $totalDeliveryCost = $deliveredOrders
            ->sum(fn($order) => $order->shipping_price);

        $exchangeCost = $orders->where('order_status', 'exchanged')
            ->sum(fn($order) => $order->orderProducts->sum(fn($p) => $p->real_price * $p->quantity));

        $deliveringNowCount = $orders->where('order_status', 'sent')->count();
        $totalExchange = $orders->where('order_status', 'exchanged')->count();
        $canceledOrdersCount = $orders->where('order_status', 'rejected_in_shipping')->count();
        $ordersDelivered = $deliveredOrders->count();

        $confirmedCount = $orders->whereNotIn('order_status', ['rejected_with_phone', 'waiting_for_confirmation', 'no_response'])->count();
        $notConfirmedCount = $ordersCount - $confirmedCount;
        $confirmationRate = $ordersCount > 0 ? ($confirmedCount * 100 / $ordersCount) : 0;

        $deliveryRate = $confirmedCount > 0 ? ($ordersDelivered * 100 / $confirmedCount) : 0;

        $upsellEligible = $deliveredOrders->count();
        $upsellCount = $deliveredOrders->filter(fn($o) => $o->orderProducts->count() > 1)->count();
        $upsellRate = $upsellEligible > 0 ? ($upsellCount * 100 / $upsellEligible) : 0;
        $dailyDelivered = $deliveredOrders
            ->groupBy(fn($order) => $order->created_at->format('Y-m-d'))
            ->map(fn($group) => $group->count());

        $deliveredDates = $dailyDelivered->keys()->toArray();
        $deliveredCounts = $dailyDelivered->values()->toArray();
        $totalProfit = $deliveredOrders->sum(function ($order) {
            $profit = $order->orderProducts->sum(fn($p) => ($p->price - ($p->real_price * ($order->order_status == 'exchanged' ? 2 : 1))) * $p->quantity);
            return $profit - ($order->shipping_price * ($order->order_status == 'exchanged' ? 2 : 1));
        }) - $amountSpentOnAds;

        $totalCash = $orders->sum(fn($order) => $order->orderProducts->sum(fn($p) => $p->price * $p->quantity));

        return view('statistics', compact(
            'ordersCount',
            'productsSalesCost',
            'deliveredPiecesCount',
            'deliveringNowCount',
            'totalExchange',
            'canceledOrdersCount',
            'ordersDelivered',
            'deliveredCash',
            'totalCash',
            'amountSpentOnAds',
            'totalProfit',
            'unpaidCash',
            'totalDeliveryCost',
            'exchangeCost',
            'confirmationRate',
            'deliveryRate',
            'upsellRate',
            'products',
            'confirmedCount',
            'notConfirmedCount',
            'deliveredDates',
            'deliveredCounts'
        ));
    }
}
