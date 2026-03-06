<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'technician'])
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        $ticket->load(['messages.user', 'user', 'technician', 'files', 'checklist']);

        $technicians = User::whereHas('role', function ($q) {
            $q->where('name', 'technician');
        })->get();

        return view('admin.tickets.show', compact('ticket', 'technicians'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:abierto,en_proceso,cerrado',
            'priority' => 'nullable|in:alta,media,baja',
            'technician_id' => 'nullable|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? $ticket->priority,
        ]);

        if (!empty($validated['technician_id'])) {
            $ticket->update([
                'technician_id' => $validated['technician_id'],
            ]);
        }

        return back()->with('success', 'Ticket actualizado');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $file = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file')->store('tickets', 'public');
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'file' => $file,
            'from_role' => 'admin',
            'seen_at' => null,
        ]);

        return back()->with('success', 'Respuesta enviada');
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket->technician_id = $request->technician_id;
        $ticket->status = 'abierto';
        $ticket->save();

        return back()->with('success', 'Ticket asignado correctamente');
    }
}
