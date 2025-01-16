<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundRequestController extends Controller
{
    public function store(Request $request, OrderItem $item)
    {
        $request->validate([
            'category' => 'required|string',
            'reason' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'file|mimetypes:image/*',
        ]);

        $refundRequest = $item->refundRequest()->create([
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'campus' => $item->order->campus,
            'quantity' => $item->quantity,
            'total' => $item->quantity * $item->variant->price,
            'category' => $request->category,
        ]);

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $refundRequest->images()->create([
                    'path' => $image->store('refund-requests', 'public'),
                ]);
            }
        }

        return redirect(route('orders.show', $item->order));
    }
}
