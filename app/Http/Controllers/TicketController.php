<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // CLIENTE → lista SUS tickets (SOPORTE)
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('support.index', compact('tickets'));
    }

    // FORM crear ticket
    public function create()
    {
        return view('support.create');
    }

    // GUARDAR ticket
    public function store(Request $r)
    {
        $r->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|string'
        ]);

       $ticket = Ticket::create([
         'user_id' => auth()->id(),
         'subject' => $r->subject,
         'description' => $r->description,
         'priority' => $r->priority,
         'status' => 'abierto'
        ]);

        return redirect('/support')
            ->with('success','Ticket creado 🔥');
    }

    // VER ticket con mensajes
    public function show($id)
    {
        $ticket = Ticket::with('messages.user')
                    ->findOrFail($id);

        // SEGURIDAD: solo su ticket
        if ($ticket->user_id != auth()->id()) {
            abort(403);
        }

        return view('support.show', compact('ticket'));
    }

    // RESPONDER ticket
    public function addMessage(Request $r, $id)
    {
        $r->validate([
            'message' => 'required|string',
            'file' => 'nullable|file|max:4096'
        ]);

        $ticket = Ticket::findOrFail($id);

        // SEGURIDAD
        if ($ticket->user_id != auth()->id()) {
            abort(403);
        }

        $path = null;

        if ($r->hasFile('file')) {
            $path = $r->file('file')->store('tickets','public');
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id' => auth()->id(),
            'message' => $r->message,
            'file' => $path
        ]);

        return back()->with('success','Mensaje enviado');
    }
}