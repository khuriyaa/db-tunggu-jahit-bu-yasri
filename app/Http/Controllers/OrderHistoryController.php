<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderHistoryController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['customer.user', 'details.service'])
            ->latest('order_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('orders.history', compact('orders'));
    }
}
