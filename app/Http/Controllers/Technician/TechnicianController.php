<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function dashboard()
    {
        $technicianId = auth()->id();

        $ticketBaseQuery = Ticket::where('technician_id', $technicianId);

        $asignados = (clone $ticketBaseQuery)
            ->where('status', 'abierto')
            ->count();

        $proceso = (clone $ticketBaseQuery)
            ->where('status', 'en_proceso')
            ->count();

        $cerrados = (clone $ticketBaseQuery)
            ->where('status', 'cerrado')
            ->count();

        $hoy = (clone $ticketBaseQuery)
            ->whereDate('updated_at', today())
            ->count();

        $tickets = (clone $ticketBaseQuery)
            ->with('checklist')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $recientes = (clone $ticketBaseQuery)
            ->with('user')
            ->latest('updated_at')
            ->take(8)
            ->get();

        return view('technician.dashboard', compact(
            'asignados',
            'proceso',
            'cerrados',
            'hoy',
            'tickets',
            'recientes'
        ));
    }

    public function index(Request $request)
    {
        $tickets = Ticket::with('user')
            ->where('technician_id', auth()->id());

        if ($request->priority) {
            $tickets->where('priority', $request->priority);
        }

        if ($request->status) {
            $tickets->where('status', $request->status);
        }

        if ($request->search) {
            $tickets->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($u) use ($request) {
                        $u->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $tickets = $tickets->latest()->get();

        return view('technician.tickets.index', compact('tickets'));
    }

    public function calendar()
    {
        $user = auth()->user();

        $citas = Appointment::with('ticket.user')
            ->where('technician_id', $user->id)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $hoy = Appointment::where('technician_id', $user->id)
            ->whereDate('date', now())
            ->count();

        $semana = Appointment::where('technician_id', $user->id)
            ->whereBetween('date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->count();

        $pendientes = Appointment::where('technician_id', $user->id)
            ->whereIn('status', ['programada', 'en_proceso'])
            ->count();

        $completadas = Appointment::where('technician_id', $user->id)
            ->where('status', 'completada')
            ->count();

        return view('technician.calendar', compact(
            'citas',
            'hoy',
            'semana',
            'pendientes',
            'completadas'
        ));
    }

    public function profile()
    {
        $user = auth()->user();

        $ticketBaseQuery = Ticket::query()->where('technician_id', $user->id);

        $assignedTickets = (clone $ticketBaseQuery)->count();
        $activeTickets = (clone $ticketBaseQuery)
            ->whereIn('status', ['abierto', 'en_proceso'])
            ->count();
        $closedTickets = (clone $ticketBaseQuery)
            ->where('status', 'cerrado')
            ->count();
        $upcomingAppointments = Appointment::query()
            ->where('technician_id', $user->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->count();

        return view('technician.profile', compact(
            'user',
            'assignedTickets',
            'activeTickets',
            'closedTickets',
            'upcomingAppointments'
        ));
    }
}
