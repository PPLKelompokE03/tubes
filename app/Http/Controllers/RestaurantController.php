<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
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
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $validated['user_id'] = $request->user()->id;
        $restaurant = Restaurant::create($validated);

        return response()->json($restaurant, 201);
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
        ]);

        if ($request->hasFile('image')) {
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($validated);

        return response()->json($restaurant);
    }

    public function destroy(Restaurant $restaurant)
    {
        abort_unless(request()->user()?->role === 'seller', 403, 'Only sellers can manage restaurants.');
        abort_unless($restaurant->user_id === request()->user()->id, 403, 'You can only delete your own restaurant.');

        if ($restaurant->image) {
            Storage::disk('public')->delete($restaurant->image);
        }

        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully.']);
    }
}
