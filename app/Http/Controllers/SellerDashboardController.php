<?php

namespace App\Http\Controllers;

use App\Models\MysteryBox;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $sellerId = $request->user()->id;

        $restaurants = Restaurant::where('user_id', $sellerId)->latest()->take(5)->get();
        $mysteryBoxes = MysteryBox::with('restaurant')->whereHas('restaurant', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })->latest()->take(5)->get();
        $incomingOrders = Order::with(['user', 'mysteryBox.restaurant'])->whereHas('mysteryBox.restaurant', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })->latest()->take(10)->get();

        $totalSales = Order::whereHas('mysteryBox.restaurant', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })->whereIn('status', ['paid', 'completed'])->sum('total_price');

        return view('dashboards.seller', [
            'restaurants' => $restaurants,
            'mysteryBoxes' => $mysteryBoxes,
            'incomingOrders' => $incomingOrders,
            'totalSales' => $totalSales,
            'restaurantCount' => Restaurant::where('user_id', $sellerId)->count(),
            'mysteryBoxCount' => MysteryBox::whereHas('restaurant', fn ($query) => $query->where('user_id', $sellerId))->count(),
            'incomingOrderCount' => Order::whereHas('mysteryBox.restaurant', fn ($query) => $query->where('user_id', $sellerId))->count(),
        ]);
    }

    public function restaurants(Request $request): View
    {
        $restaurants = Restaurant::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('lists.restaurants', [
            'restaurants' => $restaurants,
            'title' => 'My Restaurants',
        ]);
    }

    public function mysteryBoxes(Request $request): View
    {
        $mysteryBoxes = MysteryBox::with('restaurant')
            ->whereHas('restaurant', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->get();

        return view('lists.mystery-boxes', [
            'mysteryBoxes' => $mysteryBoxes,
            'title' => 'My Mystery Boxes',
        ]);
    }

    public function orders(Request $request): View
    {
        $orders = Order::with(['user', 'mysteryBox.restaurant'])
            ->whereHas('mysteryBox.restaurant', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->get();

        return view('lists.orders', [
            'orders' => $orders,
            'title' => 'Incoming Orders',
        ]);
    }
}
