<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TechnicianTicketController extends Controller
{
    private function ticketDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    public function index(Request $request)
    {
        $query = Ticket::with('user')
            ->where('technician_id', auth()->id());

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest()->get();

        return view('technician.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::where('technician_id', auth()->id())
            ->findOrFail($id);

        $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        $ticket->load(['messages.user', 'user', 'files', 'checklist']);

        return view('technician.tickets.show', compact('ticket'));
    }

    public function reply(Request $r, $id)
    {
        $r->validate([
            'message' => 'required',
        ]);

        $ticket = Ticket::where('technician_id', auth()->id())
            ->findOrFail($id);

        $file = null;
        $disk = null;

        if ($r->hasFile('file')) {
            $disk = $this->ticketDisk();
            $file = $r->file('file')->store('tickets/chat', $disk);
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $r->message,
            'file' => $file,
            'disk' => $disk,
            'from_role' => 'tecnico',
            'seen_at' => null,
        ]);

        return back()->with('success', 'Respuesta enviada');
    }
}
