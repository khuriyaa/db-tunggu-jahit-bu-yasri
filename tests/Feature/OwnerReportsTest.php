<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_sign_in_and_open_owner_reports_while_other_roles_cannot(): void
    {
        $owner = $this->createUser('Owner', ['email' => 'owner@example.com']);

        $this->post(route('login.store'), [
            'email' => 'owner@example.com',
            'password' => 'correct-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($owner);
        $this->get(route('dashboard'))->assertRedirect(route('owner.dashboard'));
        $this->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan pesanan')
            ->assertDontSee('Ringkasan metode pembayaran')
            ->assertDontSee('Nilai pesanan bulan ini')
            ->assertViewHas('summary', function (array $summary): bool {
                return isset($summary['today_orders'], $summary['month_orders'], $summary['active_orders'], $summary['total_orders'])
                    && ! isset($summary['month_value']);
            });
        $this->get(route('owner.reports'))
            ->assertOk()
            ->assertSee('Laporan pesanan & keuangan')
            ->assertDontSee('Pesanan hari ini')
            ->assertSee('Rincian metode pembayaran');

        foreach (['Admin', 'Staff', 'Customer'] as $roleName) {
            $this->actingAs($this->createUser($roleName))->get(route('owner.dashboard'))->assertForbidden();
            $this->actingAs($this->createUser($roleName))->get(route('owner.reports'))->assertForbidden();
        }
    }

    public function test_owner_report_shows_live_order_and_financial_summaries(): void
    {
        $owner = $this->createUser('Owner');
        $staff = $this->createUser('Staff');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $monthStart = today()->startOfMonth();

        $todayOrder = $this->createOrder($staff, $customer, today()->toDateString(), 'Menunggu', 50000, 'QRIS');
        $this->createOrder($staff, $customer, today()->subDay()->toDateString(), 'Diproses', 75000, null);
        $this->createOrder($staff, $customer, $monthStart->copy()->subDay()->toDateString(), 'Selesai', 20000, null);

        $response = $this->actingAs($owner)->get(route('owner.reports'));

        $response->assertOk()
            ->assertSee($todayOrder->order_code)
            ->assertSee('Nilai pesanan bulan ini')
            ->assertSee('Rp 125.000')
            ->assertSee('Belum dipilih')
            ->assertSee('bukan konfirmasi pembayaran diterima')
            ->assertViewHas('summary', function (array $summary): bool {
                return $summary['month_value'] === 125000.0
                    && $summary['total_value'] === 145000.0;
            })
            ->assertViewHas('monthlyReports', function ($reports): bool {
                return $reports->count() === 6
                    && $reports->sum('value') === 145000.0;
            });

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertViewHas('summary', function (array $summary): bool {
                return $summary['today_orders'] === 1
                    && $summary['month_orders'] === 2
                    && $summary['active_orders'] === 2
                    && $summary['total_orders'] === 3
                    && ! isset($summary['month_value']);
            });
    }

    private function createUser(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate(
            ['role_name' => $roleName],
            ['description' => $roleName],
        );

        return User::factory()->create(array_merge([
            'role_id' => $role->id,
            'password' => 'correct-password',
        ], $attributes));
    }

    private function createOrder(
        User $staff,
        Customer $customer,
        string $orderDate,
        string $status,
        int $totalPrice,
        ?string $paymentMethod,
    ): Order {
        return Order::create([
            'order_code' => 'ORD-'.str_replace('-', '', $orderDate).'-'.strtoupper(bin2hex(random_bytes(2))),
            'queue_number' => 'Q-'.strtoupper(bin2hex(random_bytes(2))),
            'customer_id' => $customer->id,
            'user_id' => $staff->id,
            'order_date' => $orderDate,
            'current_status' => $status,
            'payment_method' => $paymentMethod,
            'total_items' => 1,
            'total_price' => $totalPrice,
        ]);
    }
}
