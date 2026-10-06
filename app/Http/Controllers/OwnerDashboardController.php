<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;

class OwnerDashboardController extends Controller
{
    public function index(): View
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $summary = [
            'today_orders' => Order::query()->whereDate('order_date', today())->count(),
            'month_orders' => Order::query()->whereBetween('order_date', [$monthStart, $monthEnd])->count(),
            'active_orders' => Order::query()
                ->whereIn('current_status', ['Menunggu', 'Diproses'])
                ->count(),
            'total_orders' => Order::query()->count(),
        ];

        $recentOrders = Order::query()
            ->with('customer.user')
            ->latest('order_date')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('owner.dashboard', compact('summary', 'recentOrders'));
    }
}
