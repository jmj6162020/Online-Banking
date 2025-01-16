<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Carbon;
use League\Csv\Writer;

class TodaySalesReportController extends Controller
{
    public function __invoke()
    {
        $today = Carbon::now()->toDateString();

        $startOfDay = "$today 00:00:00";
        $endOfDay = "$today 23:59:59";

        $orders = Order::select('created_at', 'tracking_number', 'total_amount')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'asc')
            ->get();

        $csv = Writer::createFromString('');

        $csv->insertOne(['Date', 'Order ID', 'Total Amount']);

        foreach ($orders as $order) {
            $csv->insertOne([
                Carbon::parse($order->created_at)->toDateString(),
                $order->tracking_number,
                number_format($order->total_amount, 2),
            ]);
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"DailySaleReport_{$today}.csv\"",
        ]);
    }
}
