<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ExportOrderController extends Controller
{
    public function __invoke(Request $request)
    {
        $orders = Order::search(request('query'))
            ->get()
            ->where('status', 'ready-for-payment')
            ->where('campus', $request->user()->campus);
    }
}
