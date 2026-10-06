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
            'new_customer_email' => ['nullable', 'required_with:new_customer_name', 'email', 'max:255', 'unique:users,email'],
            'new_customer_password' => ['nullable', 'required_with:new_customer_name', 'string', 'min:8', 'max:255'],
            'new_customer_phone' => ['nullable', 'string', 'max:30'],
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

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil ditambahkan.');
    }

    public function storeForCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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

        DB::transaction(fn () => $this->createOrder($validated, $customer->id));

        return redirect()->route('dashboard')->with('success', 'Pesanan berhasil dikirim dan sedang menunggu konfirmasi.');
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
