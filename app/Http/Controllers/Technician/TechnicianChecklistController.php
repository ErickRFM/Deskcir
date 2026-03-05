<?php


namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketChecklist;

class TechnicianChecklistController extends Controller
{
    public function show($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        $checklist = TicketChecklist::firstOrCreate([
            'ticket_id' => $ticketId
        ],[
            'technician_id' => auth()->id()
        ]);

        return view('technician.checklist',compact('ticket','checklist'));
    }

    public function update(Request $r,$ticketId)
    {
        $checklist = TicketChecklist::where('ticket_id',$ticketId)->firstOrFail();

        $checklist->update([
            'diagnostico' => $r->diagnostico ? 1 : 0,
            'reparacion'  => $r->reparacion ? 1 : 0,
            'pruebas'     => $r->pruebas ? 1 : 0,
            'diagnostico_notes' => $r->diagnostico_notes,
            'reparacion_notes'  => $r->reparacion_notes,
            'pruebas_notes'     => $r->pruebas_notes,
            'progress' => $this->calcProgress($r)
        ]);

        return back()->with('ok','Checklist actualizado');
    }

    private function calcProgress($r)
    {
        if($r->pruebas) return 'finalizado';
        if($r->reparacion) return 'pruebas';
        if($r->diagnostico) return 'reparacion';
        return 'pendiente';
    }
}