<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order->update($request->only('status'));

        if ($order->status === 'cancelled') {
            foreach ($order->items as $item) {
                $item->variant->increment('quantity', $item->quantity);
            }
        }

        if ($order->status === 'completed') {
            foreach ($order->items as $item) {
                $item->variant->decrement('quantity', $item->quantity);
            }
        }

        return back();
    }
}
