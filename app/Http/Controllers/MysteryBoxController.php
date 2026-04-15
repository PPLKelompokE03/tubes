<?php

namespace App\Http\Controllers;

use App\Models\MysteryBox;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MysteryBoxController extends Controller
{
    public function index()
    {
        $mysteryBoxes = MysteryBox::with('restaurant')
            ->latest()
            ->get();

        return response()->json($mysteryBoxes);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->role === 'seller', 403, 'Only sellers can manage mystery boxes.');

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,png|max:2048',
        ]);

        $restaurant = Restaurant::findOrFail($validated['restaurant_id']);
        abort_unless($restaurant->user_id === $request->user()->id, 403, 'You can only add boxes to your own restaurant.');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('mystery-boxes', 'public');
        }

        $mysteryBox = MysteryBox::create($validated);

        return response()->json($mysteryBox, 201);
    }

    public function show(MysteryBox $mysteryBox)
    {
        return response()->json($mysteryBox->load('restaurant'));
    }

    public function edit(MysteryBox $mysteryBox)
    {
        abort(404);
    }

    public function update(Request $request, MysteryBox $mysteryBox)
    {
        abort_unless($request->user()?->role === 'seller', 403, 'Only sellers can manage mystery boxes.');
        abort_unless($mysteryBox->restaurant->user_id === $request->user()->id, 403, 'You can only update your own mystery box.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($mysteryBox->image) {
                Storage::disk('public')->delete($mysteryBox->image);
            }
            $validated['image'] = $request->file('image')->store('mystery-boxes', 'public');
        }

        $mysteryBox->update($validated);

        return response()->json($mysteryBox);
    }

    public function destroy(MysteryBox $mysteryBox)
    {
        abort_unless(request()->user()?->role === 'seller', 403, 'Only sellers can manage mystery boxes.');
        abort_unless($mysteryBox->restaurant->user_id === request()->user()->id, 403, 'You can only delete your own mystery box.');

        if ($mysteryBox->image) {
            Storage::disk('public')->delete($mysteryBox->image);
        }

        $mysteryBox->delete();

        return response()->json(['message' => 'Mystery box deleted successfully.']);
    }
}
