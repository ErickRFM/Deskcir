<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::query()->with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $feedback = $query->paginate(15)->withQueryString();

        if (auth()->user()?->role?->name === 'admin') {
            return view('admin.feedback.index', compact('feedback'));
        }

        $feedback = Feedback::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('feedback.index', compact('feedback'));
    }

    public function create()
    {
        return view('feedback.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:queja,sugerencia'],
            'subject' => ['required', 'string', 'max:140'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'nuevo',
        ]);

        return redirect()->route('feedback.index')->with('success', 'Tu mensaje se envio correctamente.');
    }

    public function update(Request $request, Feedback $feedback)
    {
        abort_unless(auth()->user()?->role?->name === 'admin', 403);

        $data = $request->validate([
            'status' => ['required', 'in:nuevo,en_revision,resuelto'],
        ]);

        $feedback->update($data);

        return back()->with('success', 'Estado actualizado.');
    }
}