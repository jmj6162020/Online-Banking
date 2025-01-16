<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerCollection;
use App\Models\User;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = new CustomerCollection(
            User::search(request('query'))
                ->get()
                ->filter(function ($user) {
                    return $user->roles->contains('name', 'customer');
                })
        );

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
        ]);
    }
}
