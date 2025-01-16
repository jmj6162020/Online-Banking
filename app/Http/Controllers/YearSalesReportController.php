<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use League\Csv\Writer;

class YearSalesReportController extends Controller
{
    public function __invoke()
    {
        $currentYear = Carbon::now()->format('Y'); // Get the current year (e.g., '2024')

        $startOfYear = "$currentYear-01-01 00:00:00";
        $endOfYear = Carbon::parse($startOfYear)->endOfYear()->toDateTimeString();

        $orders = Order::select('created_at', 'tracking_number', 'total_amount')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
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
            'Content-Disposition' => "attachment; filename=\"YearlySaleReport_{$currentYear}.csv\"",
        ]);
    }
}
