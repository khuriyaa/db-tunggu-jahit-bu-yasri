<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const STATUSES = ['Menunggu', 'Diproses', 'Selesai', 'Diambil'];

    public function index(): View
    {
        $orders = Order::query()
            ->with(['customer.user', 'details.service'])
            ->latest('order_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'customers' => Customer::query()->with('user')->orderBy('id')->get(),
            'services' => Service::query()->orderBy('service_name')->get(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'required_without:new_customer_name', 'integer', 'exists:customers,id'],
            'new_customer_name' => ['nullable', 'required_without:customer_id', 'string', 'max:255'],
            'new_customer_email' => ['required_with:new_customer_name', 'email', 'max:255', 'unique:users,email'],
            'new_customer_password' => ['required_with:new_customer_name', 'string', 'min:8', 'max:255'],
            'new_customer_phone' => ['required_with:new_customer_name', 'string', 'regex:/^[0-9]+$/', 'max:30'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'clothing_type' => ['required', 'string', 'max:120'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'order_date' => ['required', 'date'],
            'estimated_completion_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'note' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['nullable', 'string', Rule::in(Order::PAYMENT_METHODS)],
        ]);

        DB::transaction(function () use ($validated): void {
            $customerId = $validated['customer_id'] ?? null;

            if (! empty($validated['new_customer_name'])) {
                $customerRole = Role::firstOrCreate(
                    ['role_name' => 'Customer'],
                    ['description' => 'Pelanggan usaha jahit'],
                );
                $customerUser = User::create([
                    'role_id' => $customerRole->id,
                    'name' => $validated['new_customer_name'],
                    'email' => $validated['new_customer_email'],
                    'phone_number' => $validated['new_customer_phone'] ?? null,
                    'password' => $validated['new_customer_password'],
                ]);
                $customerId = $customerUser->customer()->create()->id;
            }

            if (! $customerId) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Pilih pelanggan atau isi data pelanggan baru.',
                ]);
            }

            $this->createOrder($validated, $customerId);
        });

        return redirect()->route('orders.index')->with('success', 'Pesanan telah dibuat.');
    }

    public function storeForCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^[0-9]+$/', 'max:30'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'clothing_type' => ['required', 'string', 'max:120'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'order_date' => ['required', 'date'],
            'estimated_completion_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'note' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', 'string', Rule::in(Order::PAYMENT_METHODS)],
        ]);

        $customer = $request->user()->customer;

        if (! $customer) {
            abort(403, 'Akun Customer belum memiliki profil pelanggan.');
        }

        DB::transaction(function () use ($request, $validated, $customer): void {
            $request->user()->update(['phone_number' => $validated['phone_number']]);
            $this->createOrder($validated, $customer->id);
        });

        return redirect()->route('customer.orders.create')->with('success', 'Pesanan telah dibuat.');
    }

    public function updateCustomerPayment(Request $request, int $orderId): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string', Rule::in(Order::CUSTOMER_PAYMENT_METHODS)],
        ]);

        $customer = $request->user()->customer;

        if (! $customer) {
            abort(403, 'Akun Customer belum memiliki profil pelanggan.');
        }

        $order = Order::query()
            ->where('customer_id', $customer->id)
            ->findOrFail($orderId);

        if ($order->payment_reported_at || $order->payment_verified_at) {
            throw ValidationException::withMessages([
                'payment_method' => 'Metode pembayaran tidak dapat diubah setelah pembayaran dilaporkan.',
            ]);
        }

        $order->update(['payment_method' => $validated['payment_method']]);

        return redirect()->route('customer.payments.index')
            ->with('success', "Metode pembayaran pesanan {$order->order_code} berhasil disimpan. Pembayaran belum terverifikasi otomatis.");
    }

    public function reportCustomerPayment(Request $request, int $orderId): RedirectResponse
    {
        $customer = $request->user()->customer;

        if (! $customer) {
            abort(403, 'Akun Customer belum memiliki profil pelanggan.');
        }

        DB::transaction(function () use ($customer, $orderId): void {
            $order = Order::query()
                ->where('customer_id', $customer->id)
                ->lockForUpdate()
                ->findOrFail($orderId);

            if ($order->payment_reported_at || $order->payment_verified_at) {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran pesanan ini sudah dilaporkan.',
                ]);
            }

            if (! $order->payment_method) {
                throw ValidationException::withMessages([
                    'payment' => 'Pilih metode pembayaran sebelum melaporkan pembayaran.',
                ]);
            }

            $order->update(['payment_reported_at' => now()]);
        });

        return redirect()->route('customer.payments.index')
            ->with('success', 'Laporan pembayaran berhasil dikirim. Admin akan memeriksa pembayaran Anda.');
    }

    public function verifyCustomerPayment(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! $lockedOrder->payment_reported_at || $lockedOrder->payment_verified_at) {
                throw ValidationException::withMessages([
                    'payment' => 'Laporan pembayaran ini sudah diproses atau tidak tersedia.',
                ]);
            }

            $lockedOrder->update(['payment_verified_at' => now()]);
        });

        return redirect()->route('dashboard')
            ->with('success', "Pembayaran pesanan {$order->order_code} berhasil dikonfirmasi.");
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
        ]);

        DB::transaction(function () use ($order, $validated): void {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $previousStatus = $lockedOrder->current_status;
            $newStatus = $validated['status'];

            if ($previousStatus === $newStatus) {
                return;
            }

            $lockedOrder->update(['current_status' => $newStatus]);
            $lockedOrder->statusLogs()->create([
                'updated_by_user_id' => Auth::id(),
                'status' => $newStatus,
                'description' => "Status diubah dari {$previousStatus} menjadi {$newStatus}.",
                'changed_at' => now(),
            ]);
        });

        return redirect()->route('orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Pesanan dan detail terkait berhasil dihapus.');
    }

    private function createOrder(array $validated, int $customerId): void
    {
        $service = Service::query()->whereKey($validated['service_id'])->lockForUpdate()->first();

        if (! $service) {
            throw ValidationException::withMessages([
                'service_id' => 'Layanan yang dipilih sudah tidak tersedia.',
            ]);
        }

        $reference = Str::upper(Str::random(6));
        $order = Order::create([
            'order_code' => 'ORD-'.now()->format('Ymd').'-'.$reference,
            'queue_number' => 'Q-'.$reference,
            'customer_id' => $customerId,
            'user_id' => Auth::id(),
            'order_date' => $validated['order_date'],
            'estimated_completion_date' => $validated['estimated_completion_date'] ?? null,
            'current_status' => 'Menunggu',
            'payment_method' => $validated['payment_method'] ?? null,
            'total_items' => $validated['quantity'],
            'total_price' => $service->base_price * $validated['quantity'],
        ]);

        $order->details()->create([
            'service_id' => $service->id,
            'clothing_type' => $validated['clothing_type'],
            'quantity' => $validated['quantity'],
            'price' => $service->base_price,
            'note' => $validated['note'] ?? null,
        ]);

        $order->statusLogs()->create([
            'updated_by_user_id' => Auth::id(),
            'status' => 'Menunggu',
            'description' => 'Pesanan dibuat.',
            'changed_at' => now(),
        ]);
    }
}
