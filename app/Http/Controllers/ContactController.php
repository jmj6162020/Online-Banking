<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campus' => ['required', Rule::in(['Main', 'Morelos'])],
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'contact_number' => 'required|string',
            'email' => 'required|email|string',
            'message' => 'required|string',
        ]);

        Message::create($validated);

        return redirect(route('contact-us'));
    }
}
