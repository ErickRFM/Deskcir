<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TechnicianTicketController extends Controller
{
    // ==========================================
    // LISTADO + FILTROS (SOLO TICKETS DEL TÉCNICO)
    // ==========================================
    public function index(Request $request)
    {
        $query = Ticket::with('user')
            ->where('technician_id', auth()->id());

        // 🔎 FILTRO PRIORIDAD
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // 🔎 FILTRO ESTADO
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔎 BUSCADOR
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('subject','like',"%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name','like',"%{$search}%");
                  });
            });
        }

        $tickets = $query->latest()->get();

        return view('technician.tickets.index', compact('tickets'));
    }

    // ==========================================
    // VER TICKET (PROTEGIDO)
    // ==========================================
    public function show($id)
    {
        $ticket = Ticket::with(['messages.user','user'])
            ->where('technician_id', auth()->id()) // 🔐 evita acceso a tickets ajenos
            ->findOrFail($id);

        return view('technician.tickets.show', compact('ticket'));
    }

    // ==========================================
    // RESPONDER TICKET
    // ==========================================
    public function reply(Request $r, $id)
    {
        $r->validate([
            'message' => 'required'
        ]);

        // 🔐 validar que el ticket pertenece al técnico
        $ticket = Ticket::where('technician_id', auth()->id())
            ->findOrFail($id);

        $file = null;

        if ($r->hasFile('file')) {
            $file = $r->file('file')->store('tickets','public');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $r->message,
            'file'      => $file,
            'from_role' => 'tecnico'
        ]);

        return back()->with('success','Respuesta enviada');
    }
}