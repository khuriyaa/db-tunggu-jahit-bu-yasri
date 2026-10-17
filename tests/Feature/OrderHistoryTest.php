<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_owner_can_view_all_orders_including_in_progress_and_completed(): void
    {
        $admin = $this->createUser('Admin');
        $owner = $this->createUser('Owner');
        $staff = $this->createUser('Staff');
        $customer = Customer::create([
            'user_id' => $this->createUser('Customer')->id,
        ]);

        $processingOrder = $this->createOrder($staff, $customer, 'Diproses');
        $completedOrder = $this->createOrder($staff, $customer, 'Selesai');
        $waitingOrder = $this->createOrder($staff, $customer, 'Menunggu');

        foreach ([$admin, $owner] as $user) {
            $this->actingAs($user)
                ->get(route('orders.history'))
                ->assertOk()
                ->assertSee('Riwayat Pesanan')
                ->assertSee('Seluruh pesanan')
                ->assertSee($processingOrder->order_code)
                ->assertSee($completedOrder->order_code)
                ->assertSee($waitingOrder->order_code)
                ->assertSee('Diproses')
                ->assertSee('Selesai')
                ->assertViewHas('orders', function ($orders): bool {
                    return $orders->total() === 3;
                });
        }

        $this->actingAs($admin)->get(route('dashboard'))->assertSee('Riwayat Pesanan');
        $this->actingAs($owner)->get(route('owner.dashboard'))->assertSee('Riwayat Pesanan');
        $this->actingAs($owner)->get(route('owner.reports'))->assertSee('Riwayat Pesanan');
    }

    public function test_only_admin_and_owner_can_view_order_history(): void
    {
        foreach (['Staff', 'Customer'] as $roleName) {
            $this->actingAs($this->createUser($roleName))
                ->get(route('orders.history'))
                ->assertForbidden();
        }
    }

    private function createUser(string $roleName): User
    {
        $role = Role::firstOrCreate(
            ['role_name' => $roleName],
            ['description' => $roleName],
        );

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function createOrder(User $staff, Customer $customer, string $status): Order
    {
        return Order::create([
            'order_code' => 'ORD-'.strtoupper(bin2hex(random_bytes(3))),
            'queue_number' => 'Q-'.strtoupper(bin2hex(random_bytes(2))),
            'customer_id' => $customer->id,
            'user_id' => $staff->id,
            'order_date' => today()->toDateString(),
            'current_status' => $status,
            'payment_method' => 'GoPay',
            'total_items' => 1,
            'total_price' => 50000,
        ]);
    }
}
