<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(AdminController $admin): View
    {
        $recentOrders = Order::with(['user', 'mysteryBox.restaurant'])
            ->latest()
            ->take(10)
            ->get();

        $impact = $admin->impactStatistics();

        return view('dashboards.admin', [
            'totalUsers' => User::count(),
            'totalRestaurants' => Restaurant::count(),
            'totalOrders' => Order::count(),
            'totalSales' => Order::whereIn('status', ['paid', 'completed'])->sum('total_price'),
            'recentOrders' => $recentOrders,
            'foodSavedQuantity' => $impact['food_saved_quantity'],
            'estimatedFoodSavedKg' => $impact['estimated_food_saved_kg'],
            'estimatedCo2ReductionKg' => $impact['estimated_co2_reduction_kg'],
        ]);
    }
}
