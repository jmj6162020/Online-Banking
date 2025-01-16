<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'selected' => 'required|boolean',
        ]);

        $item->update($request->only('selected'));

        return redirect(route('cart.index'));
    }

    public function destroy(CartItem $item)
    {
        $item->delete();

        return redirect(route('cart.index'))->with([
            'message' => 'The item has been removed from your cart.',
        ]);
    }
}
