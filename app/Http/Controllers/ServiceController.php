<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->withCount('orderDetails')
            ->orderBy('service_name')
            ->paginate(15);

        return view('services.index', compact('services'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Layanan dan detail pesanan yang terkait berhasil dihapus.');
    }
}
