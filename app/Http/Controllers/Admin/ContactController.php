<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Contacts/Index', [
            'messages' => Message::where('campus', $request->user()->campus)
                ->get(),
        ]);
    }

    public function update(Request $request, Message $message)
    {
        $request->validate([
            'read' => 'required|boolean',
        ]);

        $message->update($request->only('read'));

        return redirect(route('admin.contacts.index'));
    }
}
