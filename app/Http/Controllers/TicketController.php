<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Ticket;
use App\Models\TicketFile;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('technician')
            ->withCount(['appointments as citas_programadas_count' => function ($q) {
                $q->where('status', '!=', 'cancelada');
            }])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:alta,media,baja',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm|max:20480',
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $r->subject,
            'description' => $r->description,
            'priority' => strtolower($r->priority) ?? 'media',
        ]);

        if ($r->hasFile('attachments')) {
            foreach ($r->file('attachments') as $file) {
                $path = $file->store('tickets/evidence', 'public');

                TicketFile::create([
                    'ticket_id' => $ticket->id,
                    'path' => $path,
                    'type' => $file->getMimeType() ?? 'application/octet-stream',
                ]);
            }
        }

        return redirect('/support')->with('success', 'Ticket creado');
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ((int) $ticket->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        $ticket->load(['messages.user', 'files', 'checklist', 'technician', 'appointments.technician']);

        $appointments = Appointment::where('ticket_id', $ticket->id)
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        return view('support.show', compact('ticket', 'appointments'));
    }

    public function addMessage(Request $r, $id)
    {
        $r->validate([
            'message' => 'required|string',
            'file' => 'nullable|file|max:4096',
        ]);

        $ticket = Ticket::findOrFail($id);

        if ((int) $ticket->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $path = null;

        if ($r->hasFile('file')) {
            $path = $r->file('file')->store('tickets', 'public');
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id' => auth()->id(),
            'message' => $r->message,
            'file' => $path,
            'seen_at' => null,
        ]);

        return back()->with('success', 'Mensaje enviado');
    }
}
