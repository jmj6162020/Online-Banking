<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RefundRequestCollection;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RefundRequestController extends Controller
{
    public function index(Request $request)
    {
        $refundRequests = RefundRequest::search(request('query'))->get();

        if ($request->filled('status')) {
            $refundRequests = $refundRequests->where('status', request('status'));
        } else {
            $refundRequests = $refundRequests->where('status', 'processing');
        }

        return Inertia::render('Admin/RefundRequests/Index', [
            'requests' => new RefundRequestCollection($refundRequests),
            'query' => request('query'),
            'status' => request('status'),
        ]);
    }

    public function update(Request $request, RefundRequest $refundRequest)
    {
        $refundRequest->update($request->only('status'));
        $refundRequest->item->update(['quantity' => 0]);

        if ($refundRequest->item->order->items->count() === 1) {
            $refundRequest->item->order->update(['total_amount' => 0]);
        }

        return redirect(route('admin.refund-requests.index'))
            ->with(['message' => 'Refund applied']);
    }
}
