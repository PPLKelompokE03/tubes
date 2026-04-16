<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** Assumed kg of surplus food per mystery box unit (illustrative). */
    private const KG_FOOD_PER_UNIT = 0.5;

    /** Assumed kg CO2e avoided per kg of food waste prevented (illustrative). */
    private const KG_CO2_PER_KG_FOOD = 2.5;

    public function users(): JsonResponse
    {
        $users = User::latest()->get();

        return response()->json($users);
    }

    public function destroyUser(Request $request, User $user): JsonResponse
    {
        abort_if($user->id === $request->user()->id, 403, 'You cannot delete your own account.');

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    public function restaurants(): JsonResponse
    {
        $restaurants = Restaurant::with('user')->latest()->get();

        return response()->json($restaurants);
    }

    public function destroyRestaurant(Restaurant $restaurant): JsonResponse
    {
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully.']);
    }

    public function orders(): JsonResponse
    {
        $orders = Order::with(['user', 'mysteryBox.restaurant'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function statistics(): JsonResponse
    {
        return response()->json($this->impactStatistics());
    }

    public function impactStatistics(): array
    {
        $totalOrders = Order::count();
        $foodSavedUnits = (int) Order::whereIn('status', ['paid', 'completed'])->sum('quantity');
        $estimatedFoodSavedKg = round($foodSavedUnits * self::KG_FOOD_PER_UNIT, 2);
        $estimatedCo2ReductionKg = round($estimatedFoodSavedKg * self::KG_CO2_PER_KG_FOOD, 2);

        return [
            'total_orders' => $totalOrders,
            'food_saved_quantity' => $foodSavedUnits,
            'estimated_food_saved_kg' => $estimatedFoodSavedKg,
            'estimated_co2_reduction_kg' => $estimatedCo2ReductionKg,
        ];
    }
}
