<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPaymentConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_payment_report_notifies_admin_and_owner_until_admin_verifies_it(): void
    {
        $admin = $this->createUser('Admin');
        $owner = $this->createUser('Owner');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $order = $this->createOrder($admin, $customer);

        $this->actingAs($customerUser)
            ->post(route('customer.payments.report', $order))
            ->assertRedirect(route('customer.payments.index'))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertNotNull($order->payment_reported_at);
        $this->assertNull($order->payment_verified_at);

        $this->actingAs($customerUser)
            ->get(route('customer.payments.index'))
            ->assertOk()
            ->assertSee('Menunggu verifikasi');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Notifikasi pembayaran')
            ->assertSee($order->order_code)
            ->assertSee($customerUser->name)
            ->assertSee('Konfirmasi diterima');

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Notifikasi pembayaran')
            ->assertSee($order->order_code)
            ->assertSee($customerUser->name)
            ->assertSee('Menunggu verifikasi Admin');

        $this->actingAs($customerUser)
            ->patch(route('customer.payments.update', $order), ['payment_method' => 'Tunai'])
            ->assertSessionHasErrors('payment_method');

        $this->actingAs($customerUser)
            ->post(route('customer.payments.report', $order))
            ->assertSessionHasErrors('payment');

        $this->actingAs($owner)
            ->patch(route('admin.orders.payment.verify', $order))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('admin.orders.payment.verify', $order))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertNotNull($order->payment_verified_at);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tidak ada laporan pembayaran yang menunggu verifikasi')
            ->assertDontSee('Konfirmasi diterima');

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Tidak ada laporan pembayaran yang menunggu verifikasi')
            ->assertViewHas('paymentNotificationsCount', 0);
    }

    public function test_customer_cannot_report_another_customers_payment_or_report_without_a_method(): void
    {
        $admin = $this->createUser('Admin');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $anotherCustomerUser = $this->createUser('Customer');
        $anotherCustomer = Customer::create(['user_id' => $anotherCustomerUser->id]);
        $order = $this->createOrder($admin, $anotherCustomer);
        $unselectedOrder = $this->createOrder($admin, $customer, null);

        $this->actingAs($customerUser)
            ->post(route('customer.payments.report', $order))
            ->assertNotFound();

        $this->actingAs($customerUser)
            ->from(route('customer.payments.index'))
            ->post(route('customer.payments.report', $unselectedOrder))
            ->assertRedirect(route('customer.payments.index'))
            ->assertSessionHasErrors('payment');

        $this->assertNull($unselectedOrder->fresh()->payment_reported_at);
    }

    private function createOrder(User $creator, Customer $customer, ?string $paymentMethod = 'GoPay'): Order
    {
        return Order::create([
            'order_code' => 'ORD-PAYMENT-'.strtoupper(bin2hex(random_bytes(3))),
            'queue_number' => 'Q-'.strtoupper(bin2hex(random_bytes(3))),
            'customer_id' => $customer->id,
            'user_id' => $creator->id,
            'order_date' => today()->toDateString(),
            'current_status' => 'Menunggu',
            'payment_method' => $paymentMethod,
            'total_items' => 1,
            'total_price' => 45000,
        ]);
    }

    private function createUser(string $roleName): User
    {
        $role = Role::firstOrCreate(
            ['role_name' => $roleName],
            ['description' => $roleName],
        );

        return User::factory()->create(['role_id' => $role->id]);
    }
}
