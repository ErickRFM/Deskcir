<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Appointment;
use Illuminate\Http\Request; // 👈 IMPORTANTE (para filtros)

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

    // ================= LISTADO DE TICKETS DEL TÉCNICO =================

    public function index(Request $request)
    {
        $tickets = Ticket::with('user')
            ->where('technician_id', auth()->id()); // solo los suyos

        // 🔎 filtro por prioridad
        if($request->priority){
            $tickets->where('priority', $request->priority);
        }

        // 🔎 filtro por estado
        if($request->status){
            $tickets->where('status', $request->status);
        }

        // 🔎 búsqueda por asunto o cliente
        if($request->search){
            $tickets->where(function($q) use ($request){
                $q->where('subject','like','%'.$request->search.'%')
                  ->orWhereHas('user', function($u) use ($request){
                        $u->where('name','like','%'.$request->search.'%');
                  });
            });
        }

        $tickets = $tickets->latest()->get();

        return view('technician.tickets.index', compact('tickets'));
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