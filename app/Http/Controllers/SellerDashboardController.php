<?php

namespace App\Http\Controllers;

use App\Models\MysteryBox;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    private function restaurantSessionKey(Restaurant $restaurant): string
    {
        return 'seller.menu_access.'.auth()->id().'.'.$restaurant->id;
    }

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

    public function createRestaurant(Request $request): View
    {
        return view('seller.restaurants.form', [
            'restaurant' => null,
        ]);
    }

    public function editRestaurant(Request $request, Restaurant $restaurant): View
    {
        abort_unless($restaurant->user_id === $request->user()->id, 403);

        return view('seller.restaurants.form', [
            'restaurant' => $restaurant,
        ]);
    }

    public function createMysteryBox(Request $request): View
    {
        $selectedRestaurantId = $request->integer('restaurant_id') ?: null;
        $restaurants = Restaurant::where('user_id', $request->user()->id)->orderBy('name')->get();

        if ($selectedRestaurantId) {
            $restaurant = $restaurants->firstWhere('id', $selectedRestaurantId);
            abort_unless($restaurant, 404);
            abort_unless($request->session()->has($this->restaurantSessionKey($restaurant)), 403, 'Masuk PIN restoran dulu untuk kelola menu.');
        }

        return view('seller.mystery-boxes.form', [
            'mysteryBox' => null,
            'restaurants' => $restaurants,
            'selectedRestaurantId' => $selectedRestaurantId,
        ]);
    }

    public function editMysteryBox(Request $request, MysteryBox $mysteryBox): View
    {
        abort_unless($mysteryBox->restaurant->user_id === $request->user()->id, 403);
        abort_unless($request->session()->has($this->restaurantSessionKey($mysteryBox->restaurant)), 403, 'Masuk PIN restoran dulu untuk kelola menu.');

        $restaurants = Restaurant::where('user_id', $request->user()->id)->orderBy('name')->get();

        return view('seller.mystery-boxes.form', [
            'mysteryBox' => $mysteryBox->load('restaurant'),
            'restaurants' => $restaurants,
        ]);
    }

    public function restaurantMenus(Request $request, Restaurant $restaurant): View
    {
        abort_unless($restaurant->user_id === $request->user()->id, 403);
        abort_unless($request->session()->has($this->restaurantSessionKey($restaurant)), 403, 'Masuk PIN restoran dulu untuk kelola menu.');

        $mysteryBoxes = MysteryBox::where('restaurant_id', $restaurant->id)
            ->latest()
            ->get();

        return view('seller.mystery-boxes.by-restaurant', [
            'restaurant' => $restaurant,
            'mysteryBoxes' => $mysteryBoxes,
        ]);
    }

    public function showUnlockMenu(Request $request, Restaurant $restaurant): View
    {
        abort_unless($restaurant->user_id === $request->user()->id, 403);

        return view('seller.restaurants.unlock-menu', [
            'restaurant' => $restaurant,
        ]);
    }

    public function unlockMenu(Request $request, Restaurant $restaurant)
    {
        abort_unless($restaurant->user_id === $request->user()->id, 403);

        if (empty($restaurant->menu_access_pin)) {
            return redirect()
                ->route('seller.restaurants.edit', $restaurant)
                ->with('status', 'PIN belum disetel. Silakan atur PIN di halaman ubah restoran.');
        }

        $validated = $request->validate([
            'menu_access_pin' => 'required|digits_between:4,8',
        ]);

        if (! Hash::check($validated['menu_access_pin'], (string) $restaurant->menu_access_pin)) {
            return back()
                ->withErrors(['menu_access_pin' => 'PIN restoran tidak sesuai.'])
                ->withInput();
        }

        $request->session()->put($this->restaurantSessionKey($restaurant), true);

        return redirect()
            ->route('seller.restaurants.menus', $restaurant)
            ->with('status', 'PIN benar. Akses kelola menu dibuka.');
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
