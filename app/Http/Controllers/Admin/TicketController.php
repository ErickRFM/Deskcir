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
    public function index()
    {
        $tickets = Ticket::with('user')
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::with('messages.user','user')
            ->findOrFail($id);

        $tecnicos = User::whereHas('role', function($q){
            $q->where('name','technician');
        })->get();

        return view('admin.tickets.show', compact('ticket','tecnicos'));
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status'      => $request->status,
            'assigned_to' => $request->assigned_to,
            'priority'    => $request->priority
        ]);

        return back()->with('success','Ticket actualizado');
    }

    // ✅ TU MÉTODO NUEVO INTEGRADO
    public function reply(Request $r, $id)
    {
        $r->validate([
            'message'=>'required'
        ]);

        $file = null;

        if($r->hasFile('file')){
            $file = $r->file('file')
                ->store('tickets','public');
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id'   => auth()->id(),
            'message'   => $r->message,
            'file'      => $file,
            'from_role' => 'admin'
        ]);

        return back()->with('success','Respuesta enviada');
    }

    public function assign(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'assigned_to' => $request->user_id,
            'status'      => 'en_proceso'
        ]);

        return back()->with('success','Técnico asignado');
    }
}