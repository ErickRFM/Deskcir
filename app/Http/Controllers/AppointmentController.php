<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['ticket', 'technician'])
            ->where('user_id', auth()->id())
            ->latest('date')
            ->latest('time')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request)
    {
        $ticketId = $request->integer('ticket_id');

        if (!$ticketId) {
            return redirect('/support')->withErrors([
                'ticket_id' => 'Selecciona un ticket para agendar un servicio.',
            ]);
        }

        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($ticketId);

        return view('appointments.create', compact('ticket'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'type' => ['required', 'in:visita_presencial,recepcion_equipo,entrega_equipo,diagnostico_en_sitio,soporte_remoto'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($validated['ticket_id']);
        $technicianId = $ticket->technician_id ?: $ticket->assigned_to;

        if (!$technicianId) {
            return back()->withErrors([
                'ticket_id' => 'Este ticket todavia no tiene tecnico asignado.',
            ])->withInput();
        }

        Appointment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'technician_id' => $technicianId,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'type' => $validated['type'],
            'status' => 'programada',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect('/support/' . $ticket->id . '#agenda-tecnica')->with('success', 'Servicio agendado correctamente.');
    }

    public function show($id)
    {
        $appointment = Appointment::with(['ticket', 'technician'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('appointments.show', compact('appointment'));
    }
}


