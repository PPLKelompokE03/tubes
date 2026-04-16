<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $orders = Order::with('mysteryBox')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $allOrdersQuery = Order::where('user_id', $userId);

        return view('dashboards.customer', [
            'orders' => $orders,
            'totalOrders' => (clone $allOrdersQuery)->count(),
            'paidOrders' => (clone $allOrdersQuery)->whereIn('status', ['paid', 'completed'])->count(),
            'totalSpent' => (clone $allOrdersQuery)->whereIn('status', ['paid', 'completed'])->sum('total_price'),
        ]);
    }

    public function orders(Request $request): View
    {
        $orders = Order::with(['user', 'mysteryBox.restaurant'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('lists.orders', [
            'orders' => $orders,
            'title' => 'My Orders',
        ]);
    }

    public function restaurants(): View
    {
        $restaurants = Restaurant::withCount('mysteryBoxes')
            ->latest()
            ->get();

        return view('customer.restaurants.index', [
            'restaurants' => $restaurants,
        ]);
    }

    public function restaurantMenus(Restaurant $restaurant): View
    {
        $mysteryBoxes = $restaurant->mysteryBoxes()
            ->where('stock', '>', 0)
            ->latest()
            ->get();

        return view('customer.restaurants.show', [
            'restaurant' => $restaurant,
            'mysteryBoxes' => $mysteryBoxes,
        ]);
    }
}
