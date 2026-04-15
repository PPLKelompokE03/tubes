<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $user = request()->user();

        $reviews = Review::with('order.mysteryBox')
            ->when($user->role === 'customer', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->role === 'customer', 403, 'Only customers can create reviews.');

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        abort_unless($order->user_id === $request->user()->id, 403, 'You can only review your own order.');
        abort_unless($order->status === 'completed', 422, 'Review is allowed only after order is completed.');
        abort_if($order->review()->exists(), 422, 'This order has already been reviewed.');

        $review = Review::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json($review, 201);
    }

    public function show(Review $review)
    {
        $user = request()->user();
        $canView = $user->role === 'admin' || $review->user_id === $user->id;

        abort_unless($canView, 403);

        return response()->json($review->load('order.mysteryBox'));
    }

    public function edit(Review $review)
    {
        abort(404);
    }

    public function update(Request $request, Review $review)
    {
        $user = $request->user();
        abort_unless($user->role === 'admin' || $review->user_id === $user->id, 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);

        return response()->json($review);
    }

    public function destroy(Review $review)
    {
        $user = request()->user();
        abort_unless($user->role === 'admin' || $review->user_id === $user->id, 403);

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }
}
