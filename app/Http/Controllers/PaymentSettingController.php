<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class PaymentSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.payment-settings', [
            'paymentSetting' => PaymentSetting::query()->find(1),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'qr_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $newPath = $validated['qr_image']->store('payment', 'local');
        if (! $newPath) {
            throw new RuntimeException('QRIS merchant image could not be stored.');
        }

        $oldPath = null;
        try {
            DB::transaction(function () use ($newPath, &$oldPath): void {
                $setting = PaymentSetting::query()->lockForUpdate()->find(1);
                $oldPath = $setting?->qr_image_path;

                PaymentSetting::query()->updateOrCreate(
                    ['id' => 1],
                    ['qr_image_path' => $newPath],
                );
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($newPath);
            throw $exception;
        }

        $this->deletePreviousImage($oldPath, $newPath);

        return redirect()->route('admin.payment-settings.edit')
            ->with('success', 'QRIS merchant berhasil diperbarui.');
    }

    public function destroy(): RedirectResponse
    {
        $setting = PaymentSetting::query()->find(1);
        if (! $setting) {
            return redirect()->route('admin.payment-settings.edit')
                ->with('success', 'Belum ada QRIS merchant yang dipasang.');
        }

        $oldPath = $setting->qr_image_path;
        $setting->delete();
        $this->deletePreviousImage($oldPath, null);

        return redirect()->route('admin.payment-settings.edit')
            ->with('success', 'QRIS merchant berhasil dihapus.');
    }

    public function showQr(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $path = PaymentSetting::query()->find(1)?->qr_image_path;
        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404, 'QRIS merchant belum tersedia.');
        }

        return Storage::disk('local')->response($path);
    }

    private function deletePreviousImage(?string $oldPath, ?string $newPath): void
    {
        if (! $oldPath || $oldPath === $newPath) {
            return;
        }

        if (! Storage::disk('local')->delete($oldPath)) {
            Log::warning('Could not remove the previous merchant QRIS image.', [
                'path' => $oldPath,
            ]);
        }
    }
}
