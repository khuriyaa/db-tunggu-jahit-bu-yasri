<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\PaymentSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_replace_and_remove_merchant_qr_and_customer_can_view_it(): void
    {
        Storage::fake('local');

        $admin = $this->createUser('Admin');
        $customerUser = $this->createUser('Customer');
        $customer = Customer::create(['user_id' => $customerUser->id]);
        $order = Order::create([
            'order_code' => 'ORD-QRIS-001',
            'queue_number' => 'Q-QRIS-001',
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'order_date' => today()->toDateString(),
            'current_status' => 'Menunggu',
            'payment_method' => 'QRIS',
            'total_items' => 1,
            'total_price' => 45000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payment-settings.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Pembayaran');

        $this->actingAs($admin)
            ->post(route('admin.payment-settings.update'), [
                'qr_image' => UploadedFile::fake()->image('merchant-qris.png'),
            ])
            ->assertRedirect(route('admin.payment-settings.edit'))
            ->assertSessionHas('success');

        $firstPath = PaymentSetting::query()->findOrFail(1)->qr_image_path;
        Storage::disk('local')->assertExists($firstPath);

        $this->actingAs($customerUser)
            ->get(route('customer.payments.index'))
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee(route('payment.qr'), false);

        $this->actingAs($customerUser)
            ->patch(route('customer.payments.update', $order->id), ['payment_method' => 'Tunai'])
            ->assertRedirect(route('customer.payments.index'));

        $this->actingAs($customerUser)
            ->get(route('customer.payments.index'))
            ->assertOk()
            ->assertDontSee(route('payment.qr'), false);

        $this->actingAs($customerUser)
            ->patch(route('customer.payments.update', $order->id), ['payment_method' => 'QRIS'])
            ->assertRedirect(route('customer.payments.index'));

        $this->actingAs($customerUser)
            ->get(route('payment.qr'))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');

        $this->actingAs($admin)
            ->post(route('admin.payment-settings.update'), [
                'qr_image' => UploadedFile::fake()->image('merchant-qris-new.png'),
            ])
            ->assertRedirect(route('admin.payment-settings.edit'));

        $newPath = PaymentSetting::query()->findOrFail(1)->qr_image_path;
        $this->assertNotSame($firstPath, $newPath);
        Storage::disk('local')->assertMissing($firstPath);
        Storage::disk('local')->assertExists($newPath);

        $this->actingAs($admin)
            ->delete(route('admin.payment-settings.destroy'))
            ->assertRedirect(route('admin.payment-settings.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('payment_settings', ['id' => 1]);
        Storage::disk('local')->assertMissing($newPath);
        $this->actingAs($customerUser)->get(route('payment.qr'))->assertNotFound();
    }

    public function test_only_admin_can_manage_merchant_qr_and_only_admin_or_customer_can_view_it(): void
    {
        Storage::fake('local');

        $admin = $this->createUser('Admin');
        $staff = $this->createUser('Staff');
        $customer = $this->createUser('Customer');

        $this->actingAs($staff)->get(route('admin.payment-settings.edit'))->assertForbidden();
        $this->actingAs($staff)
            ->post(route('admin.payment-settings.update'), [
                'qr_image' => UploadedFile::fake()->image('unauthorized.png'),
            ])
            ->assertForbidden();
        $this->actingAs($staff)->get(route('payment.qr'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.payment-settings.edit'))->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.payment-settings.update'), [
                'qr_image' => UploadedFile::fake()->image('invalid.gif'),
            ])
            ->assertSessionHasErrors('qr_image');

        $this->assertDatabaseCount('payment_settings', 0);
        Storage::disk('local')->assertDirectoryEmpty('payment');

        $this->actingAs($admin)
            ->get(route('payment.qr'))
            ->assertNotFound();
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
