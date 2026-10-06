<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDataDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_delete_orders_services_and_users(): void
    {
        $admin = $this->createUser('Admin');
        $staff = $this->createUser('Staff');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $service = $this->createService();
        $order = $this->createOrder($admin, $customer, $service);

        $this->actingAs($staff)
            ->delete(route('orders.destroy', $order))
            ->assertForbidden();
        $this->actingAs($staff)
            ->delete(route('services.destroy', $service))
            ->assertForbidden();
        $this->actingAs($staff)
            ->delete(route('users.destroy', $customerUser))
            ->assertForbidden();
        $this->actingAs($staff)->get(route('services.index'))->assertForbidden();

        $this->actingAs($staff)->get(route('orders.index'))->assertOk()->assertDontSee('Hapus');
        $this->actingAs($staff)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('services.index'), false);

        $this->actingAs($admin)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Hapus');
        $this->get(route('dashboard'))->assertOk()->assertSee(route('services.index'), false);
        $this->get(route('users.index'))->assertOk()->assertSee('Ubah Pengguna')->assertSee('Hapus Pengguna');
        $this->get(route('users.manage.delete'))->assertOk()->assertSee('Hapus Pengguna');
        $this->actingAs($admin)
            ->get(route('services.index'))
            ->assertOk()
            ->assertSee($service->service_name);
    }

    public function test_admin_can_delete_order_and_its_details_and_status_logs(): void
    {
        $admin = $this->createUser('Admin');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $service = $this->createService();
        $order = $this->createOrder($admin, $customer, $service);
        $order->details()->create([
            'service_id' => $service->id,
            'clothing_type' => 'Celana bahan',
            'quantity' => 1,
            'price' => 30000,
        ]);
        $order->statusLogs()->create([
            'updated_by_user_id' => $admin->id,
            'status' => 'Menunggu',
            'description' => 'Pesanan dibuat.',
            'changed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->from(route('orders.index'))
            ->delete(route('orders.destroy', $order))
            ->assertRedirect(route('orders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_details', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('status_logs', ['order_id' => $order->id]);
    }

    public function test_admin_can_delete_user_and_related_customer_orders_but_not_self(): void
    {
        $admin = $this->createUser('Admin');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $service = $this->createService();
        $order = $this->createOrder($admin, $customer, $service);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden();

        $this->actingAs($admin)
            ->from(route('users.manage.delete'))
            ->delete(route('users.destroy', $customerUser))
            ->assertRedirect(route('users.manage.delete'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $customerUser->id]);
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_admin_can_delete_service_and_related_order_details_while_preserving_order(): void
    {
        $admin = $this->createUser('Admin');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $service = $this->createService();
        $order = $this->createOrder($admin, $customer, $service);
        $detail = $order->details()->create([
            'service_id' => $service->id,
            'clothing_type' => 'Celana bahan',
            'quantity' => 1,
            'price' => 30000,
        ]);

        $this->actingAs($admin)
            ->delete(route('services.destroy', $service))
            ->assertRedirect(route('services.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
        $this->assertDatabaseMissing('order_details', ['id' => $detail->id]);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    private function createUser(string $roleName): User
    {
        $role = Role::firstOrCreate(
            ['role_name' => $roleName],
            ['description' => $roleName],
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'password' => 'correct-password',
        ]);
    }

    private function createService(): Service
    {
        return Service::create([
            'service_name' => 'Permak Tes',
            'service_type' => 'Permak',
            'base_price' => 30000,
            'estimated_days' => 2,
        ]);
    }

    private function createOrder(User $admin, Customer $customer, Service $service): Order
    {
        return Order::create([
            'order_code' => 'ORD-'.strtoupper(bin2hex(random_bytes(5))),
            'queue_number' => 'Q-'.strtoupper(bin2hex(random_bytes(3))),
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'order_date' => today(),
            'current_status' => 'Menunggu',
            'total_items' => 1,
            'total_price' => $service->base_price,
        ]);
    }
}
