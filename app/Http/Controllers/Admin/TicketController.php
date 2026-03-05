<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User; // 👈 IMPORTANTE (para traer técnicos)
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // ===============================
    // LISTADO
    // ===============================
    public function index()
    {
        $tickets = Ticket::with(['user','technician'])
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    // ===============================
    // VER TICKET
    // ===============================
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        $ticket->load(['messages.user','user','technician','files','checklist']);

        // 👇 traer usuarios con rol técnico
        $technicians = User::whereHas('role', function($q){
            $q->where('name','technician');
        })->get();

        return view('admin.tickets.show', compact('ticket','technicians'));
    }

    // ===============================
    // ACTUALIZAR STATUS / PRIORIDAD
    // ===============================
    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status'   => $request->status,
            'priority' => $request->priority
        ]);

        // si también mandan técnico desde update
        if($request->filled('technician_id')){
            $ticket->update([
                'technician_id' => $request->technician_id
            ]);
        }

        return back()->with('success','Ticket actualizado');
    }

    // ===============================
    // RESPONDER COMO ADMIN
    // ===============================
    public function reply(Request $r, $id)
    {
        $r->validate([
            'message'=>'required'
        ]);

        $file = null;

        if($r->hasFile('file')){
            $file = $r->file('file')->store('tickets','public');
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id'   => auth()->id(),
            'message'   => $r->message,
            'file'      => $file,
            'from_role' => 'admin',
            'seen_at'   => null,
        ]);

        return back()->with('success','Respuesta enviada');
    }

    // ===============================
    // ASIGNAR TÉCNICO (VERSIÓN FINAL)
    // ===============================
    public function assign(Request $request, $id)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id'
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket->technician_id = $request->technician_id;
        $ticket->status = 'pendiente'; // queda pendiente hasta que el técnico lo tome
        $ticket->save();

        return back()->with('success','Ticket asignado correctamente');
    }
}
