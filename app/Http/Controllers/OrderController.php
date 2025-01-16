<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemCollection;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SalaryDeduction;
use App\Models\Variant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = QueryBuilder::for(
            $request->user()->orders()
                ->whereHas('items.variant.product', function ($query) {
                    $query->whereNull('deleted_at');
                }))
            ->allowedFilters(['status', 'campus'])
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => new OrderCollection($orders),
            'campus' => request('campus'),
        ]);
    }

    public function create()
    {
        $cartItemQuery = CartItem::currentSession()->where('selected', true)->get();

        return Inertia::render('Orders/Create', [
            'cartItems' => new CartItemCollection($cartItemQuery),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => Rule::in(['cash', 'salary_deduction']),
        ]);

        $cartItems = CartItem::currentSession()->where('selected', true)->get();

        DB::beginTransaction();

        try {
            $order = $request->user()->orders()->create([
                'tracking_number' => strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
                'total_amount' => $this->calculateTotal($cartItems->toArray()),
                'status' => 'created',
                'campus' => 'Main',
                'payment_type' => $request->payment_method,
            ]);

            foreach ($cartItems as $item) {
                $variant = Variant::find($item['variant_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product->id,
                    'variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'price' => $variant->price,
                ]);
            }

            
            if ($request->payment_method === 'salary_deduction') {
                $salaryDeduction = SalaryDeduction::latest()->first();
                $salaryDeduction->update(['order_id' => $order->id]);
            } else {
                SalaryDeduction::whereNull('order_id')->delete();
            }

            CartItem::currentSession()->where('selected', true)->delete();

            DB::commit();

            return redirect(route('orders.show', $order))->with([
                'message' => 'Your order has been placed!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function show(Order $order): Response
    {
        return Inertia::render('Orders/Show', [
            'order' => new OrderResource($order),
        ]);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->update(['status' => 'cancelled']);

        $items = $order->items;

        foreach ($items as $item) {
            $item->variant->increment('quantity', $item->quantity);
        }

        return redirect(route('orders.index'))->with([
            'message' => 'Your order has been canceled.',
        ]);
    }

    private function calculateTotal(array $cartItems)
    {
        return collect($cartItems)->sum(function ($item) {
            return $item['quantity'] * Variant::find($item['variant_id'])->price;
        });
    }
}
