<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\PaymentSetting;
use App\Models\Service;
use App\Models\StatusLog;
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

        if ($user->role?->role_name === 'Customer') {
            return redirect()->route('customer.orders.create');
        }

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

        $paymentNotificationsQuery = Order::query()
            ->whereNotNull('payment_reported_at')
            ->whereNull('payment_verified_at');
        $paymentNotificationsCount = $user->role?->role_name === 'Admin'
            ? (clone $paymentNotificationsQuery)->count()
            : 0;
        $paymentNotifications = $user->role?->role_name === 'Admin'
            ? $paymentNotificationsQuery
                ->with('customer.user')
                ->latest('payment_reported_at')
                ->limit(10)
                ->get()
            : collect();

        return view('dashboard', [
            'stats' => $stats,
            'isCustomer' => false,
            'paymentNotifications' => $paymentNotifications,
            'paymentNotificationsCount' => $paymentNotificationsCount,
        ]);
    }

    public function createCustomerOrder(): View
    {
        $customer = auth()->user()->customer;

        if (! $customer) {
            abort(403, 'Akun Customer belum memiliki profil pelanggan.');
        }

        return view('dashboard', [
            'isCustomer' => true,
            'customerPage' => 'create',
            'services' => Service::query()->orderBy('service_name')->get(),
            'statusUpdates' => $this->customerStatusUpdates($customer),
        ]);
    }

    public function customerOrders(): View
    {
        $customer = auth()->user()->customer;

        if (! $customer) {
            abort(403, 'Akun Customer belum memiliki profil pelanggan.');
        }

        $customerOrders = Order::query()
            ->with('details.service')
            ->where('customer_id', $customer->id)
            ->latest('order_date')
            ->latest('id')
            ->paginate(15);

        return view('dashboard', [
            'isCustomer' => true,
            'customerPage' => 'orders',
            'customerOrders' => $customerOrders,
            'statusUpdates' => $this->customerStatusUpdates($customer),
        ]);
    }

    public function customerPayments(): View
    {
        $customer = auth()->user()->customer;

        if (! $customer) {
            abort(403, 'Akun Customer belum memiliki profil pelanggan.');
        }

        $customerOrders = Order::query()
            ->where('customer_id', $customer->id)
            ->latest('order_date')
            ->latest('id')
            ->paginate(15);

        return view('dashboard', [
            'isCustomer' => true,
            'customerPage' => 'payments',
            'customerOrders' => $customerOrders,
            'paymentSetting' => PaymentSetting::query()->find(1),
            'statusUpdates' => $this->customerStatusUpdates($customer),
        ]);
    }

    private function customerStatusUpdates(Customer $customer)
    {
        return StatusLog::query()
            ->with('order')
            ->whereHas('order', fn ($query) => $query->where('customer_id', $customer->id))
            ->where('description', 'like', 'Status diubah dari %')
            ->latest('changed_at')
            ->limit(10)
            ->get();
    }
}
