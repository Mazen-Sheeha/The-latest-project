<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeService
{
    public function home()
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $ordersPerDay = DB::table('orders')
            ->selectRaw('DAY(created_at) as day, COUNT(*) as count')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupByRaw('DAY(created_at)')
            ->pluck('count', 'day');

        $daysInMonth = Carbon::now()->daysInMonth;
        $dailyOrders = [];
        $daysLabels = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $daysLabels[] = $i;
            $dailyOrders[] = $ordersPerDay[$i] ?? 0;
        }

        $today = Carbon::today();

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SATURDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::FRIDAY);

        $stats = [
            'today'      => Order::whereDate('created_at', $today)->count(),
            'this_week'  => Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'this_month' => Order::whereBetween('created_at', [$startOfMonth, $now])->count(),
            'total'      => Order::count(),
        ];

        return view('home', compact('dailyOrders', 'daysLabels', 'stats'));
    }
}
