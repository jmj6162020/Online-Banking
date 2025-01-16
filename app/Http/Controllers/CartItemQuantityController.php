<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemQuantityController extends Controller
{
    public function __invoke(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer',
        ]);

        if ($request->quantity > $item->variant->quantity) {
            return back()->with([
                'message' => 'Quantity exceeds stock.',
            ]);
        }

        $item->update($request->only('quantity'));

        return redirect(route('cart.index'));
    }
}
