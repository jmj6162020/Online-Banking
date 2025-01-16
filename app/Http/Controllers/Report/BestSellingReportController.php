<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;

class BestSellingReportController extends Controller
{
    public function __invoke()
    {
        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        $startOfMonth = "$currentYear-$currentMonth-01 00:00:00";
        $endOfMonth = Carbon::parse($startOfMonth)->endOfMonth()->toDateTimeString();

        $bestSellingProducts = OrderItem::select(
            'variants.id as variant_id',
            'products.name as product_name',
            'variants.name as variant_name',
            DB::raw('SUM(order_items.quantity) as quantity_sold'),
            DB::raw('variants.price as price'),
            DB::raw('SUM(order_items.quantity * variants.price) as total_sales')
        )
            ->join('variants', 'order_items.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->where('orders.campus', request()->user()->campus)
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('variants.id')
            ->orderByDesc('total_sales')
            ->take(10)
            ->get();

        if (request()->user()->hasRole('super-admin')) {
            $bestSellingProducts = OrderItem::select(
                'variants.id as variant_id',
                'products.name as product_name',
                'variants.name as variant_name',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('variants.price as price'),
                DB::raw('SUM(order_items.quantity * variants.price) as total_sales')
            )
                ->join('variants', 'order_items.variant_id', '=', 'variants.id')
                ->join('products', 'variants.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
                ->groupBy('variants.id')
                ->orderByDesc('total_sales')
                ->take(10)
                ->get();
        }

        $csv = Writer::createFromString('');
        $csv->insertOne(['Product', 'Variant', 'Quantity Sold', 'Price', 'Total Sales']); // Add CSV header

        foreach ($bestSellingProducts as $item) {
            $csv->insertOne([
                $item->product_name,
                $item->variant_name,
                $item->quantity_sold,
                number_format($item->price, 2),
                number_format($item->total_sales, 2),
            ]);
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"BestSellingProducts_{$currentMonth}_{$currentYear}.csv\"",
        ]);
    }
}
