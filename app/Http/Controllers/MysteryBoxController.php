<?php

namespace App\Http\Controllers;

use App\Models\MysteryBox;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MysteryBoxController extends Controller
{
    private function ensureSellerRestaurantUnlocked(Request $request, Restaurant $restaurant): void
    {
        $sessionKey = 'seller.menu_access.'.$request->user()->id.'.'.$restaurant->id;
        abort_unless($request->session()->has($sessionKey), 403, 'Masuk PIN restoran dulu untuk kelola menu.');
    }

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
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,png|max:2048',
        ]);
        $validated['discount_percentage'] = $validated['discount_percentage'] ?? 0;

        $restaurant = Restaurant::findOrFail($validated['restaurant_id']);
        abort_unless($restaurant->user_id === $request->user()->id, 403, 'You can only add boxes to your own restaurant.');
        $this->ensureSellerRestaurantUnlocked($request, $restaurant);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('mystery-boxes', 'public');
        }

        $mysteryBox = MysteryBox::create($validated);

        if ($request->expectsJson()) {
            return response()->json($mysteryBox, 201);
        }

        return redirect()
            ->route('seller.mystery-boxes')
            ->with('status', 'Menu (mystery box) berhasil ditambahkan.');
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
        $this->ensureSellerRestaurantUnlocked($request, $mysteryBox->restaurant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,png|max:2048',
        ]);
        $validated['discount_percentage'] = $validated['discount_percentage'] ?? $mysteryBox->discount_percentage;

        if ($request->hasFile('image')) {
            if ($mysteryBox->image) {
                Storage::disk('public')->delete($mysteryBox->image);
            }
            $validated['image'] = $request->file('image')->store('mystery-boxes', 'public');
        }

        $mysteryBox->update($validated);

        if ($request->expectsJson()) {
            return response()->json($mysteryBox);
        }

        return redirect()
            ->route('seller.mystery-boxes')
            ->with('status', 'Menu (mystery box) berhasil diperbarui.');
    }

    public function destroy(MysteryBox $mysteryBox)
    {
        abort_unless(request()->user()?->role === 'seller', 403, 'Only sellers can manage mystery boxes.');
        abort_unless($mysteryBox->restaurant->user_id === request()->user()->id, 403, 'You can only delete your own mystery box.');
        $this->ensureSellerRestaurantUnlocked(request(), $mysteryBox->restaurant);

        if ($mysteryBox->image) {
            Storage::disk('public')->delete($mysteryBox->image);
        }

        $mysteryBox->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Mystery box deleted successfully.']);
        }

        return redirect()
            ->route('seller.mystery-boxes')
            ->with('status', 'Menu (mystery box) berhasil dihapus.');
    }
}
