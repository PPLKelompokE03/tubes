<?php

namespace App\Http\Controllers;

use App\Models\MysteryBox;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $user = request()->user();

        $orders = Order::with(['user', 'mysteryBox'])
            ->when($user->role === 'customer', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->role === 'customer', 403, 'Only customers can create orders.');

        $validated = $request->validate([
            'mystery_box_id' => 'required|exists:mystery_boxes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            $mysteryBox = MysteryBox::lockForUpdate()->findOrFail($validated['mystery_box_id']);

            abort_if($mysteryBox->stock < $validated['quantity'], 422, 'Insufficient stock.');

            $mysteryBox->decrement('stock', $validated['quantity']);

            return Order::create([
                'user_id' => $request->user()->id,
                'mystery_box_id' => $mysteryBox->id,
                'quantity' => $validated['quantity'],
                'total_price' => $mysteryBox->price * $validated['quantity'],
                'status' => 'pending',
            ]);
        });

        return response()->json($order->load('mysteryBox'), 201);
    }

    public function show(Order $order)
    {
        $user = request()->user();
        $isOrderOwner = $order->user_id === $user->id;
        $isSellerOwner = $order->mysteryBox->restaurant->user_id === $user->id;

        abort_unless($user->role === 'admin' || $isOrderOwner || $isSellerOwner, 403);

        return response()->json($order->load(['user', 'mysteryBox']));
    }

    public function edit(Order $order)
    {
        abort(404);
    }

    public function update(Request $request, Order $order)
    {
        $user = $request->user();

        if ($request->hasFile('payment_proof')) {
            abort_unless($user->role === 'customer', 403, 'Only customers can upload payment proof.');
            abort_unless($order->user_id === $user->id, 403, 'You can only update your own order.');

            $validated = $request->validate([
                'payment_proof' => 'required|image|mimes:jpg,png|max:2048',
            ]);

            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            $order->update([
                'payment_proof' => $request->file('payment_proof')->store('payment-proofs', 'public'),
                'status' => 'paid',
            ]);

            return response()->json($order);
        }

        abort_unless($user->role === 'seller' || $user->role === 'admin', 403, 'Only seller/admin can update order status.');
        abort_unless(
            $user->role === 'admin' || $order->mysteryBox->restaurant->user_id === $user->id,
            403,
            'You can only manage orders from your own restaurant.'
        );

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,completed',
        ]);

        $order->update($validated);

        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $order->user_id === $user->id, 403);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.']);
    }
}
