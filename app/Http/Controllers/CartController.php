<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemCollection;
use App\Models\CartItem;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        if (request('campus') === null) {
            session(['campus' => 'Main']);
        } else {
            session(['campus' => request('campus')]);
        }

        CartItem::currentSession()
            ->whereHas('product', function ($query) {
                $query->where('campus', '!=', session('campus', 'Main'));
            })
            ->update(['selected' => false]);

        $cartItems = CartItem::currentSession()
            ->whereHas('product', function ($query) {
                $query->where('campus', session('campus', 'Main'));
            })
            ->get();

        return Inertia::render('Cart/Index', [
            'cartItems' => new CartItemCollection($cartItems),
            'campus' => session('campus', 'Main'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $guestId = Session::get('guest_id', uniqid());

        $variant = Variant::find($request->variant_id);

        if ($validated['quantity'] > $variant->quantity) {
            return back()->withErrors([
                'quantity' => 'Quantity exceeds available stock.',
            ]);
        }

        $cartItem = CartItem::where(function ($query) use ($userId, $guestId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_id', $guestId);
            }
        })
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'])
            ->first();

        if ($cartItem) {
            if ($cartItem->quantity + $validated['quantity'] > $variant->quantity) {
                return back()->withErrors([
                    'quantity' => 'Quantity exceeds available stock.',
                ]);
            }

            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => $userId,
                'guest_id' => $userId ? null : $guestId,
                'product_id' => $validated['product_id'],
                'variant_id' => $validated['variant_id'],
                'quantity' => $validated['quantity'],
            ]);
        }

        Session::put('guest_id', $guestId);

        return redirect(route('products.show', $validated['product_id']));
    }
}
