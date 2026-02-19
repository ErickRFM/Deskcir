<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TechnicianTicketController extends Controller
{

    public function index()
    {
        $tickets = Ticket::latest()->get();

        return view('technician.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['messages.user','user'])
            ->findOrFail($id);

        return view('technician.tickets.show', compact('ticket'));
    }

    public function reply(Request $r, $id)
    {
        $r->validate([
            'message' => 'required'
        ]);

        $file = null;

        if ($r->hasFile('file')) {
            $file = $r->file('file')
                ->store('tickets','public');
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id'   => auth()->id(),
            'message'   => $r->message,
            'file'      => $file,
            'from_role' => 'tecnico'
        ]);

        return back()->with('success','Respuesta enviada');
    }

}