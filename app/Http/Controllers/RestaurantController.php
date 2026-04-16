<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with('mysteryBoxes')
            ->latest()
            ->get();

        return response()->json($restaurants);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->role === 'seller', 403, 'Only sellers can manage restaurants.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png|max:2048',
            'menu_access_pin' => 'required|digits_between:4,8',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }
        $validated['menu_access_pin'] = Hash::make($validated['menu_access_pin']);

        $validated['user_id'] = $request->user()->id;
        $restaurant = Restaurant::create($validated);

        if ($request->expectsJson()) {
            return response()->json($restaurant, 201);
        }

        return redirect()
            ->route('seller.restaurants')
            ->with('status', 'Restoran berhasil ditambahkan.');
    }

    public function show(Restaurant $restaurant)
    {
        return response()->json($restaurant->load('mysteryBoxes'));
    }

    public function edit(Restaurant $restaurant)
    {
        abort(404);
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        abort_unless($request->user()?->role === 'seller', 403, 'Only sellers can manage restaurants.');
        abort_unless($restaurant->user_id === $request->user()->id, 403, 'You can only update your own restaurant.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png|max:2048',
            'menu_access_pin' => 'nullable|digits_between:4,8',
        ]);

        if ($request->hasFile('image')) {
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        if (! empty($validated['menu_access_pin'])) {
            $validated['menu_access_pin'] = Hash::make($validated['menu_access_pin']);
        } else {
            unset($validated['menu_access_pin']);
        }

        $restaurant->update($validated);

        if ($request->expectsJson()) {
            return response()->json($restaurant);
        }

        return redirect()
            ->route('seller.restaurants')
            ->with('status', 'Restoran berhasil diperbarui.');
    }

    public function destroy(Restaurant $restaurant)
    {
        abort_unless(request()->user()?->role === 'seller', 403, 'Only sellers can manage restaurants.');
        abort_unless($restaurant->user_id === request()->user()->id, 403, 'You can only delete your own restaurant.');

        if ($restaurant->image) {
            Storage::disk('public')->delete($restaurant->image);
        }

        $restaurant->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Restaurant deleted successfully.']);
        }

        return redirect()
            ->route('seller.restaurants')
            ->with('status', 'Restoran berhasil dihapus.');
    }
}
