<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\SalaryDeduction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalaryDeductionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_1' => 'required|string',
            'student_1_yrlvl' => 'required|string',

            'student_2' => 'nullable|string',
            'student_2_yrlvl' => 'nullable|string',

            'student_3' => 'nullable|string',
            'student_3_yrlvl' => 'nullable|string',

            'student_4' => 'nullable|string',
            'student_4_yrlvl' => 'nullable|string',

            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        SalaryDeduction::create($validated);

        return redirect(route('orders.create'));
    }

    public function show(Order $order)
    {
        return Inertia::render('SalaryDeductionForm', [
            'order' => new OrderResource($order),
        ]);
    }
}
