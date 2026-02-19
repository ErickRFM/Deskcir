<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Appointment;   // 👈 IMPORTANTE

class TechnicianController extends Controller
{

    public function dashboard()
    {
        $asignados = Ticket::where('status','pendiente')->count();

        $proceso = Ticket::where('status','en_proceso')->count();

        $cerrados = Ticket::where('status','cerrado')->count();

        $hoy = Ticket::whereDate('updated_at',now())->count();

        $recientes = Ticket::latest()->take(5)->get();

        return view('technician.dashboard',compact(
            'asignados',
            'proceso',
            'cerrados',
            'hoy',
            'recientes'
        ));
    }

    // =============== TU CALENDAR COMO LO PEDISTE ===============

    public function calendar()
    {
        $user = auth()->user();

        $citas = Appointment::with('ticket.user')
            ->where('technician_id',$user->id)
            ->orderBy('date')
            ->get();

        $hoy = Appointment::where('technician_id',$user->id)
            ->whereDate('date',now())
            ->count();

        $semana = Appointment::where('technician_id',$user->id)
            ->whereBetween('date',[now(), now()->addDays(7)])
            ->count();

        $pendientes = Appointment::where('technician_id',$user->id)
            ->where('status','pendiente')
            ->count();

        $completadas = Appointment::where('technician_id',$user->id)
            ->where('status','completado')
            ->count();

        return view('technician.calendar',compact(
        'citas',
        'hoy',
        'semana',
        'pendientes',
        'completadas'  
       ));
    }

}