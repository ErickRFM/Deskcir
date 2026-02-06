<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // 👉 VER TODOS LOS TICKETS
    public function index()
    {
        $tickets = Ticket::with('user')
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    // 👉 VER TICKET Y CONVERSACIÓN
    public function show($id)
    {
        $ticket = Ticket::with('messages.user','user')
            ->findOrFail($id);

        // 🔹 Técnicos (según tu nuevo enfoque)
       $tecnicos = User::whereHas('role', function($q){
         $q->where('name','technician');
        })->get();

        return view('admin.tickets.show', compact('ticket','tecnicos'));
    }

    // 👉 ACTUALIZAR ESTADO + PRIORIDAD + ASIGNADO
    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority
        ]);

        return back()->with('success','Ticket actualizado');
    }

    // 👉 RESPONDER COMO SOPORTE (NUEVA VERSIÓN)
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
            'file' => 'nullable|file'
        ]);

        $path = null;

        if ($request->file) {
            $path = $request->file('file')
                    ->store('tickets','public');
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'file' => $path
        ]);

        return back()->with('success','Respuesta enviada');
    }

    // 👉 ASIGNAR TÉCNICO RÁPIDO (opcional si lo usas aparte)
    public function assign(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'assigned_to' => $request->user_id,
            'status' => 'en_proceso'
        ]);

        return back()->with('success','Técnico asignado');
    }
}