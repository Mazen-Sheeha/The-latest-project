<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Adset extends Model
{
    protected $fillable = ['name', 'active'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Campaign::class);
    }

    public function budgets()
    {
        return $this->hasManyThrough(Budget::class, Campaign::class);
    }

    public function scopeWithStatistics(Builder $query, $from = null, $to = null)
    {
        return $query->with([
            'orders' => function ($q) use ($from, $to) {
                if ($from && $to) {
                    $q->where('orders.created_at', '>=', $from)
                        ->where('orders.created_at', '<', \Carbon\Carbon::parse($to)->startOfDay());
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

    public function calculateStatistics()
    {
        $orders = $this->orders;

        $orders_count = $orders->count();

        $confirmed = $orders_count - $orders
            ->whereIn('order_status', ['rejected_with_phone', 'waiting_for_confirmation', 'no_response'])
            ->count();

        $delivered = $orders->whereIn('order_status', ['exchanged', 'received'])->count();

        $budgets_sum = $this->budgets->sum("budget");

        $c_p_result = $orders_count ? $budgets_sum / $orders_count : 0;
        $c_p_confirmed = $confirmed ? $budgets_sum / $confirmed : 0;
        $c_p_delivered = $delivered ? $budgets_sum / $delivered : 0;

        $confirmation_rate = $orders_count ? ($confirmed * 100) / $orders_count : 0;
        $delivered_rate = $orders_count ? ($delivered * 100) / $orders_count : 0;

        $totalProfit = $orders->whereIn('order_status', ['exchanged', 'received'])->sum(function ($order) {
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
