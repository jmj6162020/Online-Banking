<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreatedOrderController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $nonDeletedProductOrderIds = OrderItem::join('variants', 'order_items.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->whereNull('products.deleted_at') // Filter out soft-deleted products
            ->pluck('order_items.order_id') // Get only the order IDs
            ->toArray();

        $orders = Order::search(request('query'))
            ->whereIn('id', $nonDeletedProductOrderIds) // Filter orders by the non-deleted product order IDs
            ->where('status', 'created')
            ->where('campus', $request->user()->campus)
            ->get();

        if ($request->user()->hasRole('super-admin')) {
            $orders = Order::search(request('tracking_number'))
                ->whereIn('id', $nonDeletedProductOrderIds) // Filter orders by the non-deleted product order IDs
                ->where('status', 'created')
                ->get();
        }

        return Inertia::render('Admin/Orders/Index', [
            'title' => 'Created Orders',
            'orders' => new OrderCollection($orders),
        ]);
    }
}
