<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user->role?->role_name === 'Owner') {
            return redirect()->route('owner.dashboard');
        }

        $isCustomer = $user->role?->role_name === 'Customer';
        $customer = $isCustomer ? $user->customer : null;
        $stats = null;

        if (! $isCustomer) {
            $totals = DB::query()
                ->selectSub(User::query()->selectRaw('COUNT(*)'), 'users')
                ->selectSub(Customer::query()->selectRaw('COUNT(*)'), 'customers')
                ->selectSub(Order::query()->selectRaw('COUNT(*)'), 'orders')
                ->selectSub(Service::query()->selectRaw('COUNT(*)'), 'services')
                ->selectSub(Order::query()->selectRaw('COALESCE(SUM(total_price), 0)'), 'order_value')
                ->first();

            if (! $totals) {
                throw new RuntimeException('Dashboard totals query returned no result.');
            }

            $stats = [
                'users' => (int) $totals->users,
                'customers' => (int) $totals->customers,
                'orders' => (int) $totals->orders,
                'services' => (int) $totals->services,
                'order_value' => (float) $totals->order_value,
            ];
        }

        $services = $isCustomer ? Service::query()->orderBy('service_name')->get() : collect();
        $customerOrders = $isCustomer && $customer
            ? Order::query()
                ->with('details.service')
                ->where('customer_id', $customer->id)
                ->latest('order_date')
                ->latest('id')
                ->limit(5)
                ->get()
            : collect();
        $completedOrders = $isCustomer && $customer
            ? Order::query()
                ->where('customer_id', $customer->id)
                ->where('current_status', 'Selesai')
                ->latest('updated_at')
                ->limit(5)
                ->get(['id', 'order_code', 'updated_at'])
            : collect();

        return view('dashboard', compact('stats', 'isCustomer', 'services', 'customerOrders', 'completedOrders'));
    }
}
