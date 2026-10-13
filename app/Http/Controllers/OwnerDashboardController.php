<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class OwnerDashboardController extends Controller
{
    public function index(): View
    {
        $reportStart = now()->startOfMonth()->subMonths(5);
        $monthEnd = now()->endOfMonth();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEndDate = $monthEnd->toDateString();

        $summary = [
            'today_orders' => Order::query()->whereDate('order_date', today())->count(),
            'month_orders' => Order::query()->whereBetween('order_date', [$monthStart, $monthEndDate])->count(),
            'active_orders' => Order::query()
                ->whereIn('current_status', ['Menunggu', 'Diproses'])
                ->count(),
            'total_orders' => Order::query()->count(),
        ];

        $dailyValues = Order::query()
            ->whereBetween('order_date', [$reportStart->toDateString(), $monthEndDate])
            ->selectRaw('order_date, COALESCE(SUM(total_price), 0) as order_value')
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get();

        $valuesByMonth = [];
        foreach ($dailyValues as $dailyValue) {
            $monthKey = Carbon::parse($dailyValue->order_date)->format('Y-m');
            $valuesByMonth[$monthKey] = ($valuesByMonth[$monthKey] ?? 0) + (float) $dailyValue->order_value;
        }

        $monthlyFinancials = collect();
        for ($offset = 5; $offset >= 0; $offset--) {
            $month = now()->startOfMonth()->subMonths($offset);
            $value = $valuesByMonth[$month->format('Y-m')] ?? 0.0;
            $monthlyFinancials->push([
                'label' => $month->locale('id')->translatedFormat('M'),
                'value' => $value,
                'value_label' => match (true) {
                    $value >= 1_000_000_000 => 'Rp '.number_format($value / 1_000_000_000, 1, ',', '.').' M',
                    $value >= 1_000_000 => 'Rp '.number_format($value / 1_000_000, 1, ',', '.').' jt',
                    $value >= 1_000 => 'Rp '.number_format($value / 1_000, 0, ',', '.').' rb',
                    default => 'Rp '.number_format($value, 0, ',', '.'),
                },
            ]);
        }
        $maximumMonthlyValue = $monthlyFinancials->max('value');
        $monthlyFinancials = $monthlyFinancials->map(function (array $month) use ($maximumMonthlyValue): array {
            $month['bar_height'] = $maximumMonthlyValue > 0
                ? round($month['value'] / $maximumMonthlyValue * 100, 2)
                : 0;

            return $month;
        });

        $recentOrders = Order::query()
            ->with('customer.user')
            ->latest('order_date')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('owner.dashboard', compact('summary', 'monthlyFinancials', 'recentOrders'));
    }
}
