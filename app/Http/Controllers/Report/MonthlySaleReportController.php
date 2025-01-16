<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Carbon;
use League\Csv\Writer;

class MonthlySaleReportController extends Controller
{
    public function __invoke()
    {
        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        $startOfMonth = "$currentYear-$currentMonth-01 00:00:00";
        $endOfMonth = Carbon::parse($startOfMonth)->endOfMonth()->toDateTimeString();

        $orders = Order::select('created_at', 'tracking_number', 'total_amount')
            ->where('status', 'completed')
            ->where('campus', request()->user()->campus)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'asc')
            ->get();

        if (request()->user()->hasRole('super-admin')) {
            $orders = Order::select('created_at', 'tracking_number', 'total_amount')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $csv = Writer::createFromString('');

        $csv->insertOne(['Date', 'Order ID', 'Total Amount']);

        foreach ($orders as $order) {
            $csv->insertOne([
                Carbon::parse($order->created_at)->toDateTimeString(),
                $order->tracking_number,
                number_format($order->total_amount, 2),
            ]);
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"MonthySaleReport_{$currentMonth}_{$currentYear}.csv\"",
        ]);
    }
}
