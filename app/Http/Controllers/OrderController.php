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
            ->when($user->role === 'seller', function ($query) use ($user) {
                $query->whereHas('mysteryBox.restaurant', fn ($q) => $q->where('user_id', $user->id));
            })
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

            $unitPrice = (float) $mysteryBox->final_price;

            return Order::create([
                'user_id' => $request->user()->id,
                'mystery_box_id' => $mysteryBox->id,
                'quantity' => $validated['quantity'],
                'total_price' => round($unitPrice * $validated['quantity'], 2),
                'status' => 'pending',
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json($order->load('mysteryBox'), 201);
        }

        return redirect()
            ->back()
            ->with('status', 'Pesanan berhasil dibuat. Silakan lanjut upload bukti pembayaran di riwayat pesanan.');
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
            abort_if($order->status === 'canceled', 422, 'Canceled orders cannot accept payment proof.');
            abort_unless(in_array($order->status, ['pending', 'paid'], true), 422, 'Payment proof is not allowed for this order state.');

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

            return response()->json($order->fresh()->load(['user', 'mysteryBox']));
        }

        if ($request->has('status')) {
            $request->validate([
                'status' => 'required|in:pending,paid,completed,canceled',
            ]);
            $newStatus = $request->input('status');

            if ($user->role === 'customer' && $order->user_id === $user->id) {
                abort_unless($newStatus === 'canceled', 403, 'Customers may only cancel their own orders.');
                $this->applyStatusChange($order, 'canceled');

                return response()->json($order->fresh()->load(['user', 'mysteryBox']));
            }

            abort_unless($user->role === 'seller' || $user->role === 'admin', 403, 'Only seller or admin can update order status.');

            if ($user->role === 'seller') {
                abort_unless($order->mysteryBox->restaurant->user_id === $user->id, 403, 'You can only manage orders from your own restaurant.');
            }

            $this->applyStatusChange($order, $newStatus);

            return response()->json($order->fresh()->load(['user', 'mysteryBox']));
        }

        abort(422, 'No recognized update fields were provided.');
    }

    public function destroy(Order $order)
    {
        $user = request()->user();
        $isOwner = $order->user_id === $user->id;
        $isSellerOwner = $order->mysteryBox->restaurant->user_id === $user->id;

        abort_unless(
            $user->role === 'admin'
            || $isOwner
            || ($user->role === 'seller' && $isSellerOwner),
            403
        );

        if (in_array($order->status, ['pending', 'paid'], true)) {
            $order->mysteryBox->increment('stock', $order->quantity);
        }

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.']);
    }

    private function applyStatusChange(Order $order, string $newStatus): void
    {
        $previous = $order->status;

        if ($previous === $newStatus) {
            return;
        }

        if ($newStatus === 'canceled' && in_array($previous, ['pending', 'paid'], true)) {
            $order->mysteryBox->increment('stock', $order->quantity);
        }

        $order->update(['status' => $newStatus]);
    }
}
