<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_and_can_open_the_login_page(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk();
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = $this->createUser('Admin', [
            'email' => 'admin@example.com',
            'password' => 'correct-password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'correct-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_can_register_and_is_logged_in_with_a_customer_profile(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Buat akun baru');

        $this->post(route('register.store'), [
            'name' => 'Customer Baru',
            'email' => 'customer-baru@example.com',
            'phone_number' => '08123456789',
            'password' => 'customer-password',
            'password_confirmation' => 'customer-password',
        ])->assertRedirect(route('dashboard'));

        $user = User::with(['role', 'customer'])->where('email', 'customer-baru@example.com')->firstOrFail();

        $this->assertSame('Customer', $user->role->role_name);
        $this->assertSame('08123456789', $user->phone_number);
        $this->assertNotNull($user->customer);
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_registration_rejects_duplicate_email_and_mismatched_passwords(): void
    {
        $this->createUser('Customer', ['email' => 'already-used@example.com']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Customer Baru',
                'email' => 'already-used@example.com',
                'password' => 'customer-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'already-used@example.com')->count());
    }

    public function test_invalid_credentials_do_not_authenticate_the_user(): void
    {
        $this->createUser('Staff', ['email' => 'staff@example.com']);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'staff@example.com',
                'password' => 'incorrect-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_only_admin_can_view_the_user_list(): void
    {
        $admin = $this->createUser('Admin');
        $staff = $this->createUser('Staff');
        $customer = $this->createUser('Customer');

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('users.index'))->assertSee('Tambah akun');
        $this->actingAs($admin)->get(route('users.manage.edit'))
            ->assertOk()
            ->assertSee('Ubah Pengguna')
            ->assertSee('Simpan perubahan');
        $this->actingAs($admin)->get(route('users.manage.delete'))
            ->assertOk()
            ->assertSee('Hapus Pengguna')
            ->assertSee('data-confirm', false);
        $this->actingAs($admin)->get(route('users.index'))
            ->assertDontSee('Simpan perubahan')
            ->assertDontSee('data-confirm', false);
        $this->actingAs($staff)->get(route('users.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('users.manage.edit'))->assertForbidden();
        $this->actingAs($staff)->get(route('users.manage.delete'))->assertForbidden();
        $this->actingAs($customer)->get(route('users.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('users.manage.edit'))->assertForbidden();
        $this->actingAs($customer)->get(route('users.manage.delete'))->assertForbidden();
        $this->actingAs($staff)->post(route('users.store'), [])->assertForbidden();
        $this->actingAs($customer)->post(route('users.store'), [])->assertForbidden();
        $this->actingAs($staff)->patch(route('users.update', $customer), [])->assertForbidden();
        $this->actingAs($customer)->patch(route('users.update', $staff), [])->assertForbidden();
    }

    public function test_admin_can_update_user_name_email_and_role(): void
    {
        $admin = $this->createUser('Admin');
        $user = $this->createUser('Owner', ['email' => 'owner-lama@example.com']);
        $customerRole = Role::firstOrCreate(
            ['role_name' => 'Customer'],
            ['description' => 'Customer'],
        );

        $this->actingAs($admin)
            ->patch(route('users.update', $user), [
                'name' => 'Nama Baru',
                'email' => 'owner-baru@example.com',
                'role_id' => $customerRole->id,
            ])
            ->assertRedirect(route('users.manage.edit'))
            ->assertSessionHas('success', 'Data pengguna berhasil diperbarui.');

        $user->refresh()->load('role', 'customer');
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('owner-baru@example.com', $user->email);
        $this->assertSame('Customer', $user->role->role_name);
        $this->assertNotNull($user->customer);
    }

    public function test_admin_cannot_change_own_role_and_update_rejects_duplicate_email(): void
    {
        $admin = $this->createUser('Admin', ['email' => 'admin@example.com']);
        $user = $this->createUser('Owner', ['email' => 'owner@example.com']);
        $ownerRole = Role::firstOrCreate(
            ['role_name' => 'Owner'],
            ['description' => 'Owner'],
        );

        $this->actingAs($admin)
            ->patch(route('users.update', $admin), [
                'name' => 'Admin Baru',
                'email' => 'admin@example.com',
                'role_id' => $ownerRole->id,
            ])
            ->assertRedirect(route('users.manage.edit'))
            ->assertSessionHasErrors(['role_id'], null, 'updateUser'.$admin->id);

        $this->assertSame('Admin', $admin->fresh()->role->role_name);
        $this->actingAs($admin)
            ->from(route('users.manage.edit'))
            ->patch(route('users.update', $user), [
                'name' => 'Owner Baru',
                'email' => 'admin@example.com',
                'role_id' => $ownerRole->id,
            ])
            ->assertRedirect(route('users.manage.edit'))
            ->assertSessionHasErrors(['email'], null, 'updateUser'.$user->id);

        $this->assertSame('owner@example.com', $user->fresh()->email);
    }

    public function test_admin_can_create_admin_owner_and_customer_accounts(): void
    {
        $admin = $this->createUser('Admin');

        foreach (['Admin', 'Owner', 'Customer'] as $index => $roleName) {
            $role = Role::firstOrCreate(
                ['role_name' => $roleName],
                ['description' => $roleName],
            );
            $email = strtolower($roleName).$index.'@example.com';

            $this->actingAs($admin)
                ->post(route('users.store'), [
                    'name' => $roleName.' Baru',
                    'email' => $email,
                    'phone_number' => '08123456789',
                    'role_id' => $role->id,
                    'password' => 'initial-password',
                    'password_confirmation' => 'initial-password',
                ])
                ->assertRedirect(route('users.index'))
                ->assertSessionHas('success');

            $user = User::with(['role', 'customer'])->where('email', $email)->firstOrFail();
            $this->assertSame($roleName, $user->role->role_name);
            $this->assertTrue(Hash::check('initial-password', $user->password));
            $this->assertSame($roleName === 'Customer', $user->customer !== null);
        }

        $staffRole = Role::firstOrCreate(
            ['role_name' => 'Staff'],
            ['description' => 'Staff'],
        );
        $this->actingAs($admin)
            ->from(route('users.index'))
            ->post(route('users.store'), [
                'name' => 'Staff Baru',
                'email' => 'staff-baru@example.com',
                'role_id' => $staffRole->id,
                'password' => 'initial-password',
                'password_confirmation' => 'initial-password',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('users', ['email' => 'staff-baru@example.com']);
        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertDontSee('Buat akun Admin, Staff');
    }

    public function test_admin_account_creation_rejects_duplicate_email_and_invalid_passwords(): void
    {
        $admin = $this->createUser('Admin');
        $role = Role::firstOrCreate(
            ['role_name' => 'Owner'],
            ['description' => 'Staff'],
        );
        $this->createUser('Customer', ['email' => 'already-used@example.com']);

        $this->actingAs($admin)
            ->from(route('users.index'))
            ->post(route('users.store'), [
                'name' => 'Akun Baru',
                'email' => 'already-used@example.com',
                'role_id' => $role->id,
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertSame(2, User::count());
    }

    public function test_each_role_can_view_the_dashboard(): void
    {
        foreach (['Admin', 'Staff', 'Customer'] as $roleName) {
            $this->actingAs($this->createUser($roleName))->get(route('dashboard'))->assertOk();
        }

        $this->actingAs($this->createUser('Owner'))
            ->get(route('dashboard'))
            ->assertRedirect(route('owner.dashboard'));
    }

    public function test_dashboard_shows_live_business_totals(): void
    {
        $admin = $this->createUser('Admin');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);

        Order::create([
            'order_code' => 'ORD-TEST-001',
            'queue_number' => 'Q-001',
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'order_date' => now()->toDateString(),
            'current_status' => 'Menunggu',
            'total_items' => 2,
            'total_price' => 125000.50,
        ]);

        Service::create([
            'service_name' => 'Permak Tes',
            'service_type' => 'Permak',
            'base_price' => 25000,
            'estimated_days' => 2,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('stats', fn (array $stats): bool => $stats['users'] === 2
                && $stats['customers'] === 1
                && $stats['orders'] === 1
                && $stats['services'] === 1
                && $stats['order_value'] === 125000.5
            );
    }

    public function test_staff_can_create_order_and_update_its_status(): void
    {
        $staff = $this->createUser('Staff');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $service = Service::create([
            'service_name' => 'Permak Celana',
            'service_type' => 'Permak',
            'base_price' => 30000,
            'estimated_days' => 2,
        ]);

        $this->actingAs($staff)
            ->post(route('orders.store'), [
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'clothing_type' => 'Celana bahan',
                'quantity' => 2,
                'order_date' => '2026-10-04',
                'estimated_completion_date' => '2026-10-06',
                'note' => 'Kecilkan bagian pinggang',
            ])
            ->assertRedirect(route('orders.index'));

        $order = Order::with('details')->firstOrFail();
        $this->assertSame(60000.0, (float) $order->total_price);
        $this->assertSame(2, $order->total_items);
        $this->assertSame($staff->id, $order->user_id);
        $this->assertSame('Celana bahan', $order->details->first()->clothing_type);
        $this->assertDatabaseHas('status_logs', [
            'order_id' => $order->id,
            'updated_by_user_id' => $staff->id,
            'status' => 'Menunggu',
        ]);

        $this->patch(route('orders.status', $order), ['status' => 'Diproses'])
            ->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'current_status' => 'Diproses',
        ]);
        $this->assertDatabaseHas('status_logs', [
            'order_id' => $order->id,
            'status' => 'Diproses',
        ]);

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Celana bahan')
            ->assertSee('Diproses');
    }

    public function test_staff_can_add_a_customer_while_creating_an_order(): void
    {
        $staff = $this->createUser('Staff');
        $service = Service::create([
            'service_name' => 'Jahit Rok',
            'service_type' => 'Jahit',
            'base_price' => 80000,
            'estimated_days' => 4,
        ]);

        $this->actingAs($staff)
            ->post(route('orders.store'), [
                'new_customer_name' => 'Pelanggan Baru',
                'new_customer_email' => 'pelanggan-baru@example.com',
                'new_customer_password' => 'password-awal',
                'new_customer_phone' => '08123456789',
                'service_id' => $service->id,
                'clothing_type' => 'Rok panjang',
                'quantity' => 1,
                'order_date' => '2026-10-04',
            ])
            ->assertRedirect(route('orders.index'));

        $customerUser = User::with('customer')->where('email', 'pelanggan-baru@example.com')->firstOrFail();
        $this->assertSame('Customer', $customerUser->role->role_name);
        $this->assertSame('08123456789', $customerUser->phone_number);
        $this->assertNotNull($customerUser->customer);
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customerUser->customer->id,
            'user_id' => $staff->id,
            'total_price' => 80000,
        ]);
    }

    public function test_customer_can_create_and_view_only_their_own_orders_from_dashboard(): void
    {
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $otherCustomerUser = $this->createUser('Customer');
        $otherCustomer = Customer::create(['user_id' => $otherCustomerUser->id]);
        $service = Service::create([
            'service_name' => 'Permak Baju',
            'service_type' => 'Permak',
            'base_price' => 25000,
            'estimated_days' => 2,
        ]);
        $otherOrder = Order::create([
            'order_code' => 'ORD-OTHER-001',
            'queue_number' => 'Q-OTHER',
            'customer_id' => $otherCustomer->id,
            'user_id' => $otherCustomerUser->id,
            'order_date' => '2026-10-03',
            'current_status' => 'Menunggu',
            'total_items' => 1,
            'total_price' => 25000,
        ]);

        $this->actingAs($customerUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Buat pesanan baru')
            ->assertSee('Pesanan saya')
            ->assertDontSee($otherOrder->order_code);

        $this->actingAs($customerUser)
            ->post(route('customer.orders.store'), [
                'customer_id' => $otherCustomer->id,
                'service_id' => $service->id,
                'payment_method' => 'QRIS',
                'clothing_type' => 'Kemeja lengan panjang',
                'quantity' => 2,
                'order_date' => '2026-10-04',
                'estimated_completion_date' => '2026-10-06',
                'note' => 'Pendekkan lengan',
            ])
            ->assertRedirect(route('dashboard'));

        $order = Order::with('details')->where('user_id', $customerUser->id)->firstOrFail();
        $this->assertSame($customer->id, $order->customer_id);
        $this->assertSame($customerUser->id, $order->user_id);
        $this->assertSame(50000.0, (float) $order->total_price);
        $this->assertSame('QRIS', $order->payment_method);
        $this->assertSame('Kemeja lengan panjang', $order->details->first()->clothing_type);

        $order->update(['current_status' => 'Selesai']);

        $this->actingAs($customerUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Rp 50.000')
            ->assertSee('QRIS')
            ->assertSee('Pesanan siap diambil!')
            ->assertDontSee('Ringkasan usaha')
            ->assertDontSee('Total Pengguna')
            ->assertDontSee($otherOrder->order_code);
    }

    public function test_only_customers_can_submit_customer_dashboard_orders(): void
    {
        $admin = $this->createUser('Admin');

        $this->actingAs($admin)
            ->post(route('customer.orders.store'), [])
            ->assertForbidden();
    }

    public function test_customers_cannot_access_order_management(): void
    {
        $customer = $this->createUser('Customer');

        $this->actingAs($customer)->get(route('orders.index'))->assertForbidden();
        $this->actingAs($customer)->post(route('orders.store'))->assertForbidden();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $this->actingAs($this->createUser('Customer'))
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_dashboard(): void
    {
        $this->actingAs($this->createUser('Admin'))
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_role_seeder_creates_admin_from_configured_credentials(): void
    {
        config([
            'admin.name' => 'Administrator',
            'admin.email' => 'kiya@gmail.com',
            'admin.password' => '150905',
            'owner.name' => 'Pemilik Tes',
            'owner.email' => 'owner@example.com',
            'owner.password' => 'owner-test-password',
        ]);

        $this->seed(RoleSeeder::class);

        $admin = User::with('role')->where('email', 'kiya@gmail.com')->firstOrFail();
        $owner = User::with('role')->where('email', 'owner@example.com')->firstOrFail();

        $this->assertSame('Admin', $admin->role->role_name);
        $this->assertTrue(Hash::check('150905', $admin->password));
        $this->assertSame('Owner', $owner->role->role_name);
        $this->assertTrue(Hash::check('owner-test-password', $owner->password));
    }

    public function test_service_seeder_can_be_run_repeatedly_without_duplicates(): void
    {
        $this->seed(ServiceSeeder::class);
        $this->seed(ServiceSeeder::class);

        $this->assertSame(3, Service::count());
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
}
