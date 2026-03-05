<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketChecklist;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\ChecklistPhoto;

class ChecklistController extends Controller
{

    // GUARDAR CHECKLIST
    public function save(Request $request, Ticket $ticket)
    {

        $checklist = TicketChecklist::updateOrCreate(
            ['ticket_id'=>$ticket->id],
            [

                'diagnostico' => $request->has('diagnostico'),
                'diagnostico_notes' => $request->diagnostico_notes,

                'reparacion' => $request->has('reparacion'),
                'reparacion_notes' => $request->reparacion_notes,

                'pruebas' => $request->has('pruebas'),
                'pruebas_notes' => $request->pruebas_notes,

                'errores' => $request->errores,
                'observaciones' => $request->observaciones,

                'status' => $request->status
            ]
        );


        /*
        ============================
        GUARDAR FOTOS
        ============================
        */

        if($request->hasFile('fotos')){

            foreach($request->file('fotos') as $foto){

                $path = $foto->store('checklists','public');

                ChecklistPhoto::create([
                    'ticket_checklist_id'=>$checklist->id,
                    'path'=>$path
                ]);

            }

        }


        return redirect()
            ->back()
            ->with('success','Checklist guardado correctamente');

    }



    // EXPORTAR PDF
    public function pdf(Ticket $ticket)
    {

        $checklist = $ticket->checklist()->with('photos')->first();

        $pdf = Pdf::loadView('pdf.checklist',[
            'ticket'=>$ticket,
            'checklist'=>$checklist
        ]);

        return $pdf->download('checklist_ticket_'.$ticket->id.'.pdf');

    }

}