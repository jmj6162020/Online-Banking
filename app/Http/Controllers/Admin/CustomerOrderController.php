<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\User;
use Inertia\Inertia;

class CustomerOrderController extends Controller
{
    public function show(User $customer)
    {
        return Inertia::render('Admin/Orders/Index', [
            'orders' => new OrderCollection($customer->orders),
            'title' => "{$customer->name} Orders",
        ]);
    }
}
