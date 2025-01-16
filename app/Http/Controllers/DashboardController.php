<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $recentOrders = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('variants', 'order_items.variant_id', '=', 'variants.id')
                ->join('products', 'variants.product_id', '=', 'products.id')
                ->where('orders.campus', request()->user()->campus)
                ->whereNull('products.deleted_at')
                ->select('orders.*')
                ->latest()
                ->get();

        if ($request->user()->hasRole('super-admin')) {
            $recentOrders = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('variants', 'order_items.variant_id', '=', 'variants.id')
                ->join('products', 'variants.product_id', '=', 'products.id')
                ->whereNull('products.deleted_at')
                ->select('orders.*')
                ->latest()
                ->get();
        }

        return Inertia::render('Dashboard', [
            'currentMonthSales' => $this->getSalesDataForCurrentMonth(),
            'todaySales' => $this->getSalesDataForToday(),
            'yearSales' => $this->getSalesDataForYear(),
            'currentMonthBestSelling' => $this->getBestSellingForCurrentMonth(),
            'recentOrders' => new OrderCollection($recentOrders),
        ]);
    }

    public function getSalesDataForCurrentMonth()
    {
        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        $startOfMonth = "$currentYear-$currentMonth-01 00:00:00";
        $endOfMonth = Carbon::parse($startOfMonth)->endOfMonth()->toDateTimeString();

        $salesData = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('variants', 'order_items.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->where('orders.campus', request()->user()->campus)
            ->whereNull('products.deleted_at')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw("strftime('%d', orders.created_at) as day, SUM(orders.total_amount) as total_sales")
            ->groupBy(DB::raw("strftime('%d', orders.created_at)"))
            ->orderBy(DB::raw("strftime('%d', orders.created_at)"), 'asc')
            ->get();

        if (request()->user()->hasRole('super-admin')) {
            $salesData = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('variants', 'order_items.variant_id', '=', 'variants.id')
                ->join('products', 'variants.product_id', '=', 'products.id')
                ->where('orders.status', 'completed')
                ->whereNull('products.deleted_at')
                ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
                ->selectRaw("strftime('%d', orders.created_at) as day, SUM(orders.total_amount) as total_sales")
                ->groupBy(DB::raw("strftime('%d', orders.created_at)"))
                ->orderBy(DB::raw("strftime('%d', orders.created_at)"), 'asc')
                ->get();
        }

        $labels = [];
        $data = [];

        for ($day = 1; $day <= Carbon::now()->daysInMonth; $day++) {
            $labels[] = (string) $day;
            $salesForDay = $salesData->firstWhere('day', str_pad($day, 2, '0', STR_PAD_LEFT));
            $data[] = $salesForDay ? (float) $salesForDay->total_sales : 0;
        }

        $data = [
            'labels' => $labels,
            'data' => $data,
        ];

        return $data;
    }

    public function getSalesDataForToday()
    {
        $today = Carbon::now()->toDateString();

        $startOfDay = "$today 00:00:00";
        $endOfDay = "$today 23:59:59";

        $salesData = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('variants', 'order_items.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->where('orders.campus', request()->user()->campus)
            ->whereNull('products.deleted_at')
            ->whereBetween('orders.created_at', [$startOfDay, $endOfDay])
            ->selectRaw("strftime('%H', orders.created_at) as hour, SUM(orders.total_amount) as total_sales")
            ->groupBy(DB::raw("strftime('%H', orders.created_at)"))
            ->orderBy(DB::raw("strftime('%H', orders.created_at)"), 'asc')
            ->get();

        if (request()->user()->hasRole('super-admin')) {
            $salesData = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('variants', 'order_items.variant_id', '=', 'variants.id')
                ->join('products', 'variants.product_id', '=', 'products.id')
                ->where('orders.status', 'completed')
                ->whereNull('products.deleted_at')
                ->whereBetween('orders.created_at', [$startOfDay, $endOfDay])
                ->selectRaw("strftime('%H', orders.created_at) as hour, SUM(orders.total_amount) as total_sales")
                ->groupBy(DB::raw("strftime('%H', orders.created_at)"))
                ->orderBy(DB::raw("strftime('%H', orders.created_at)"), 'asc')
                ->get();
        }

        $labels = [];
        $data = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $hourLabel24 = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $hourLabel12 = (new DateTime("$hour:00"))->format('h A');
            $labels[] = $hourLabel12;
            $salesForHour = $salesData->firstWhere('hour', $hourLabel24);
            $data[] = $salesForHour ? (float) $salesForHour->total_sales : 0;
        }

        $data = [
            'labels' => $labels,
            'data' => $data,
        ];

        return $data;
    }


    public function getSalesDataForYear()
    {
        $year = Carbon::now()->year;

        $salesData = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('variants', 'order_items.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->where('orders.campus', request()->user()->campus)
            ->whereYear('orders.created_at', $year)
            ->whereNull('products.deleted_at')
            ->selectRaw("strftime('%m', orders.created_at) as month, SUM(orders.total_amount) as total_sales")
            ->groupBy(DB::raw("strftime('%m', orders.created_at)"))
            ->orderBy(DB::raw("strftime('%m', orders.created_at)"), 'asc')
            ->get();

        if (request()->user()->hasRole('super-admin')) {
            $salesData = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('variants', 'order_items.variant_id', '=', 'variants.id')
                ->join('products', 'variants.product_id', '=', 'products.id')
                ->where('orders.status', 'completed')
                ->whereYear('orders.created_at', $year)
                ->whereNull('products.deleted_at')
                ->selectRaw("strftime('%m', orders.created_at) as month, SUM(orders.total_amount) as total_sales")
                ->groupBy(DB::raw("strftime('%m', orders.created_at)"))
                ->orderBy(DB::raw("strftime('%m', orders.created_at)"), 'asc')
                ->get();
        }

        $monthLabels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];
        $labels = $monthLabels;
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT); // Format month as "01", "02", etc.
            $salesForMonth = $salesData->firstWhere('month', $monthKey);
            $data[] = $salesForMonth ? (float) $salesForMonth->total_sales : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }


    public function getBestSellingForCurrentMonth()
    {
        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        $startOfMonth = "$currentYear-$currentMonth-01 00:00:00";
        $endOfMonth = Carbon::parse($startOfMonth)->endOfMonth()->toDateTimeString();

        $bestSellingProducts = OrderItem::select('variant_id', DB::raw('SUM(order_items.quantity * price) as total_sales'))
            ->join('variants', 'order_items.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->where('orders.campus', request()->user()->campus)
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->whereNull('products.deleted_at')
            ->groupBy('variant_id')
            ->orderByDesc('total_sales')
            ->take(10)
            ->get();


        if (request()->user()->hasRole('super-admin')) {
            $bestSellingProducts = OrderItem::select('variant_id', DB::raw('SUM(order_items.quantity * price) as total_sales'))
                ->join('variants', 'order_items.variant_id', '=', 'variants.id')
                ->join('products', 'variants.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
                ->whereNull('products.deleted_at')
                ->groupBy('variant_id')
                ->orderByDesc('total_sales')
                ->take(10)
                ->get();
        }

        $bestSellingProducts = $bestSellingProducts->map(function ($item) {
            $variant = Variant::find($item->variant_id);
            $item->product_name = $variant ? $variant->product->name : 'Unknown Product';

            return $item;
        });

        return $bestSellingProducts;
    }
}
