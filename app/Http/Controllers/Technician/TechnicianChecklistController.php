<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketChecklist;
use Illuminate\Http\Request;

class TechnicianChecklistController extends Controller
{
    public function show($ticketId)
    {
        $ticket = Ticket::where('technician_id', auth()->id())->findOrFail($ticketId);

        // No persistir checklist vacío al abrir pantalla.
        $checklist = TicketChecklist::firstOrNew(
            ['ticket_id' => $ticket->id],
            ['technician_id' => auth()->id()]
        );

        if (!$checklist->exists) {
            $checklist->setRelation('photos', collect());
        } else {
            $checklist->load('photos');
        }

        return view('technician.checklist', compact('ticket', 'checklist'));
    }

    public function update(Request $r, $ticketId)
    {
        $ticket = Ticket::where('technician_id', auth()->id())->findOrFail($ticketId);

        $checklist = TicketChecklist::where('ticket_id', $ticket->id)->firstOrFail();

        $checklist->update([
            'diagnostico' => $r->diagnostico ? 1 : 0,
            'reparacion' => $r->reparacion ? 1 : 0,
            'pruebas' => $r->pruebas ? 1 : 0,
            'diagnostico_notes' => $r->diagnostico_notes,
            'reparacion_notes' => $r->reparacion_notes,
            'pruebas_notes' => $r->pruebas_notes,
            'progress' => $this->calcProgress($r),
        ]);

        return back()->with('ok', 'Checklist actualizado');
    }

    private function calcProgress($r)
    {
        if ($r->pruebas) {
            return 'finalizado';
        }

        if ($r->reparacion) {
            return 'pruebas';
        }

        if ($r->diagnostico) {
            return 'reparacion';
        }

        return 'pendiente';
    }
}
