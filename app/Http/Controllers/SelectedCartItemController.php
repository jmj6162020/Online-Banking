<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class SelectedCartItemController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'selected' => 'required|boolean',
        ]);

        CartItem::currentSession()->update($request->only('selected'));

        return redirect(route('cart.index'));
    }
}
