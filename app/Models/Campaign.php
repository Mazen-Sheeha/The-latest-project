<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Campaign extends Model
{
    protected $fillable = ['campaign', 'source', 'active', 'adset_id', 'url'];

    public function adset()
    {
        return $this->belongsTo(Adset::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function scopeWithStatistics(Builder $query, $from = null, $to = null)
    {
        return $query->with([
            'adset:id,name',
            'orders' => function ($q) use ($from, $to) {
                if ($from && $to) {
                    $q->where('created_at', '>=', $from)
                        ->where('created_at', '<', \Carbon\Carbon::parse($to)->startOfDay());
                }
            },
            'budgets' => function ($q) use ($from, $to) {
                if ($from && $to) {
                    $q->where('date', '>=', $from)
                        ->where('date', '<', $to);
                }
            },
        ])
            ->withSum(['budgets as budgets_sum_budget' => function ($q) use ($from, $to) {
                if ($from && $to) {
                    $q->where('date', '>=', $from)
                        ->where('date', '<', $to);
                }
            }], 'budget');
    }


    public function scopeFilterByRequest(Builder $query, Request $request)
    {
        if ($request->adset_id) {
            $query->where('adset_id', $request->adset_id);
        }

        if ($request->active && $request->active !== 'all') {
            $query->where("active", $request->active === 'active');
        }

        if ($request->source && $request->source !== 'all') {
            $query->where("source", "LIKE", "%" . $request->source . "%");
        }

        return $query;
    }

    public function calculateStatistics()
    {
        $orders = $this->orders->load('orderProducts');
        $orders_count = $orders->count();

        $confirmed = $orders_count - $orders
            ->whereIn('order_status', ['rejected_with_phone', 'waiting_for_confirmation', 'no_response'])
            ->count();

        $deliveredOrders = $orders->whereIn('order_status', ['exchanged', 'received']);
        $delivered = $deliveredOrders->count();

        $budgets_sum = $this->budgets->sum("budget");

        $c_p_result = $orders_count ? $budgets_sum / $orders_count : 0;
        $c_p_confirmed = $confirmed ? $budgets_sum / $confirmed : 0;
        $c_p_delivered = $delivered ? $budgets_sum / $delivered : 0;

        $confirmation_rate = $orders_count ? ($confirmed * 100) / $orders_count : 0;
        $delivered_rate = $orders_count ? ($delivered * 100) / $orders_count : 0;

        $totalProfit = $deliveredOrders->sum(function ($order) {
            $productsProfit = $order->orderProducts->sum(fn($op) => ($op->price - $op->real_price) * $op->quantity);
            $profitAfterShipping = $productsProfit - $order->shipping_price;
            return $profitAfterShipping;
        });

        $total_earn_by_pcs = $delivered > 0
            ? ($totalProfit - $budgets_sum) / $delivered
            : 0;

        return [
            'orders_count' => $orders_count,
            'confirmed' => $confirmed,
            'delivered' => $delivered,
            'c_p_result' => $c_p_result,
            'c_p_confirmed' => $c_p_confirmed,
            'c_p_delivered' => $c_p_delivered,
            'confirmation_rate' => $confirmation_rate,
            'delivered_rate' => $delivered_rate,
            'total_earn_by_pcs' => $total_earn_by_pcs,
        ];
    }
}
