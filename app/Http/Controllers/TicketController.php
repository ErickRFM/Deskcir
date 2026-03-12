<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Ticket;
use App\Models\TicketFile;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private function ticketDisk(): string
    {
        return config('filesystems.default', 'public');
    }

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

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:alta,media,baja',
            'support_mode' => 'nullable|in:general,presencial',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm|max:20480',
        ]);

        $supportMode = $r->input('support_mode', 'general');
        $subject = trim((string) $r->subject);
        $description = trim((string) $r->description);

        if ($supportMode === 'presencial') {
            if (! str_starts_with(mb_strtolower($subject), '[soporte presencial]')) {
                $subject = '[Soporte presencial] '.$subject;
            }

            $description = "Modalidad solicitada: soporte presencial.\n\n".$description;
        }

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $subject,
            'description' => $description,
            'priority' => strtolower($r->priority) ?? 'media',
        ]);

        if ($r->hasFile('attachments')) {
            $disk = $this->ticketDisk();

            foreach ($r->file('attachments') as $file) {
                $path = $file->store('tickets/evidence', $disk);

                TicketFile::create([
                    'ticket_id' => $ticket->id,
                    'path' => $path,
                    'type' => $file->getMimeType() ?? 'application/octet-stream',
                    'disk' => $disk,
                ]);
            }
        }

        return redirect('/support/'.$ticket->id)->with('success', 'Ticket creado');
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ((int) $ticket->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        $ticket->load(['messages.user', 'files', 'checklist', 'technician', 'appointments.technician']);

        $appointments = Appointment::where('ticket_id', $ticket->id)
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        return view('support.show', compact('ticket', 'appointments'));
    }

    public function addMessage(Request $r, $id)
    {
        $r->validate([
            'message' => 'required|string',
            'file' => 'nullable|file|max:4096',
        ]);

        $ticket = Ticket::findOrFail($id);

        if ((int) $ticket->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $path = null;
        $disk = null;

        if ($r->hasFile('file')) {
            $disk = $this->ticketDisk();
            $path = $r->file('file')->store('tickets/chat', $disk);
        }

        TicketMessage::create([
            'ticket_id' => $id,
            'user_id' => auth()->id(),
            'message' => $r->message,
            'file' => $path,
            'disk' => $disk,
            'seen_at' => null,
        ]);

        return back()->with('success', 'Mensaje enviado');
    }
}
