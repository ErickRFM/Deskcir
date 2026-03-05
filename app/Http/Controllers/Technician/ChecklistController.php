<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketChecklist;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ChecklistPhoto;
use Illuminate\Support\Facades\Storage;

class ChecklistController extends Controller
{
    // GUARDAR CHECKLIST
    public function save(Request $request, Ticket $ticket)
    {
        abort_unless((int) $ticket->technician_id === (int) auth()->id(), 403);
        $validated = $request->validate([
            'diagnostico_notes' => ['nullable', 'string'],
            'reparacion_notes' => ['nullable', 'string'],
            'pruebas_notes' => ['nullable', 'string'],
            'errores' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'status' => ['required', 'in:diagnostico,reparacion,finalizado'],
            'fotos' => ['nullable', 'array', 'max:8'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $checklist = TicketChecklist::updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'technician_id' => $ticket->technician_id ?? auth()->id(),
                'diagnostico' => $request->boolean('diagnostico'),
                'diagnostico_notes' => $validated['diagnostico_notes'] ?? null,

                'reparacion' => $request->boolean('reparacion'),
                'reparacion_notes' => $validated['reparacion_notes'] ?? null,

                'pruebas' => $request->boolean('pruebas'),
                'pruebas_notes' => $validated['pruebas_notes'] ?? null,

                'errores' => $validated['errores'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'status' => $validated['status'],
            ]
        );

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('checklists', 'public');

                ChecklistPhoto::create([
                    'ticket_checklist_id' => $checklist->id,
                    'path' => $path,
                ]);
            }
        }

        // Mantener sincronizado el estado del ticket con el checklist
        // usando valores compatibles con el enum de tickets.
        $ticketStatusMap = [
            'diagnostico' => 'en_proceso',
            'reparacion' => 'en_proceso',
            'finalizado' => 'cerrado',
        ];

        $ticket->status = $ticketStatusMap[$validated['status']] ?? 'en_proceso';
        $ticket->save();

        return redirect()
            ->back()
            ->with('success', 'Checklist guardado correctamente');
    }

    // EXPORTAR PDF
    public function pdf(Ticket $ticket)
    {
        abort_unless((int) $ticket->technician_id === (int) auth()->id(), 403);

        $checklist = $ticket->checklist()->with('photos')->first();

        $pdf = Pdf::loadView('pdf.checklist', [
            'ticket' => $ticket,
            'checklist' => $checklist,
        ]);

        return $pdf->download('checklist_ticket_' . $ticket->id . '.pdf');
    }

    public function deletePhoto(Ticket $ticket, $photo)
    {
        $user = auth()->user();

        $isAssignedTech = (int) $ticket->technician_id === (int) $user->id;
        $isAdmin = $user->role?->name === 'admin';

        abort_unless($isAssignedTech || $isAdmin, 403);

        $checklist = $ticket->checklist;
        if (!$checklist) {
            return redirect()
                ->route('technician.checklist', $ticket->id)
                ->with('error', 'No hay checklist para este ticket.');
        }

        $photoModel = $checklist->photos()->whereKey($photo)->first();
        if (!$photoModel) {
            return redirect()
                ->route('technician.checklist', $ticket->id)
                ->with('error', 'La foto ya no existe o ya fue eliminada.');
        }

        if ($photoModel->path && Storage::disk('public')->exists($photoModel->path)) {
            Storage::disk('public')->delete($photoModel->path);
        }

        $photoModel->delete();

        return redirect()
            ->route('technician.checklist', $ticket->id)
            ->with('success', 'Foto eliminada correctamente');
    }
}
