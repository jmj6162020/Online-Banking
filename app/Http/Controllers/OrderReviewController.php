<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReviewController extends Controller
{
    public function store(Request $request, OrderItem $item)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'rating' => 'required|integer',
        ]);

        $item->review()->upsert([
            'user_id' => Auth::id(),
            'product_id' => $item->product->id,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ], ['user_id', 'order_item_id'], ['comment', 'rating']);

        return redirect(route('orders.show', $item->order));
    }
}
