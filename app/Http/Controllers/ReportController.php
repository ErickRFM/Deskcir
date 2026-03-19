<?php

namespace App\Http\Controllers;

use App\Models\ServiceLog;
use App\Models\Ticket;
use PDF;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function show($ticketId)
    {
        $ticket = Ticket::with(['user', 'technician', 'files', 'checklist.photos', 'appointments.technician'])->findOrFail($ticketId);
        $logs = Schema::hasTable('service_logs')
            ? ServiceLog::with('user')->where('ticket_id', $ticketId)->orderBy('created_at')->get()
            : collect();

        return view('reports.show', compact('ticket', 'logs'));
    }

    public function pdf($ticketId)
    {
        $ticket = Ticket::with(['user', 'technician', 'files', 'checklist.photos', 'appointments.technician'])->findOrFail($ticketId);
        $logs = Schema::hasTable('service_logs')
            ? ServiceLog::with('user')->where('ticket_id', $ticketId)->orderBy('created_at')->get()
            : collect();

        $pdf = PDF::loadView('reports.pdf', compact('ticket', 'logs'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('deskcir_reporte_servicio_ticket_' . $ticketId . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
