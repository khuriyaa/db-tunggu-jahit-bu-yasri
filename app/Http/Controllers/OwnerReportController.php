<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class OwnerReportController extends Controller
{
    public function index(): View
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthOrders = Order::query()->whereBetween('order_date', [
            $monthStart->toDateString(),
            $monthEnd->toDateString(),
        ]);

        $summary = [
            'month_value' => (float) (clone $monthOrders)->sum('total_price'),
            'total_value' => (float) Order::query()->sum('total_price'),
        ];

        $reportStart = $monthStart->copy()->subMonths(5);
        $dailyTotals = Order::query()
            ->whereBetween('order_date', [
                $reportStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->selectRaw('order_date, COUNT(*) as order_count, COALESCE(SUM(total_price), 0) as order_value')
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get();

        $totalsByMonth = [];
        foreach ($dailyTotals as $dailyTotal) {
            $monthKey = Carbon::parse($dailyTotal->order_date)->format('Y-m');
            $totalsByMonth[$monthKey] ??= ['orders' => 0, 'value' => 0.0];
            $totalsByMonth[$monthKey]['orders'] += (int) $dailyTotal->order_count;
            $totalsByMonth[$monthKey]['value'] += (float) $dailyTotal->order_value;
        }

        $monthlyReports = collect();
        for ($offset = 5; $offset >= 0; $offset--) {
            $month = now()->startOfMonth()->subMonths($offset);
            $monthKey = $month->format('Y-m');
            $monthTotals = $totalsByMonth[$monthKey] ?? ['orders' => 0, 'value' => 0.0];
            $monthlyReports->push([
                'label' => $month->locale('id')->translatedFormat('F Y'),
                'orders' => $monthTotals['orders'],
                'value' => $monthTotals['value'],
            ]);
        }

        $paymentReports = (clone $monthOrders)
            ->selectRaw('payment_method, COUNT(*) as order_count, COALESCE(SUM(total_price), 0) as order_value')
            ->groupBy('payment_method')
            ->orderBy('payment_method')
            ->get();

        $recentOrders = Order::query()
            ->with('customer.user')
            ->latest('order_date')
            ->latest('id')
            ->limit(10)
            ->get();

        return view('owner.reports', compact(
            'summary',
            'monthlyReports',
            'paymentReports',
            'recentOrders',
        ));
    }
}
