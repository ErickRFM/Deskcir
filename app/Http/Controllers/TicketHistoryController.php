<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Schema;

class TicketHistoryController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('technician')
            ->withCount(['appointments as citas_programadas_count' => function ($q) {
                $q->where('status', '!=', 'cancelada');
            }])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('support.index', compact('tickets'));
    }

    public function archiveClosed()
    {
        if (!Schema::hasColumn('tickets', 'archived_at')) {
            return back()->with('error', 'No se puede archivar: columna archived_at no disponible.');
        }

        Ticket::query()
            ->where('user_id', auth()->id())
            ->where('status', 'cerrado')
            ->whereNull('archived_at')
            ->update(['archived_at' => now()]);

        return back()->with('success', 'Tickets cerrados archivados.');
    }
}
