<?php

namespace App\Http\Controllers;

use App\Http\Resources\RefundRequestCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RefundedItemsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('RefundedItems/Index', [
            'refundRequests' => new RefundRequestCollection($request->user()->refundRequests),
        ]);
    }
}
