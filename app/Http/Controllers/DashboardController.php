<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with('mysteryBox')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('dashboards.customer', [
            'orders' => $orders,
            'totalOrders' => $orders->count(),
        ]);
    }

    public function orders(Request $request): View
    {
        $orders = Order::with('mysteryBox.restaurant')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('lists.orders', [
            'orders' => $orders,
            'title' => 'My Orders',
        ]);
    }
}
