<?php

namespace App\Http\Controllers;

use App\Models\RtcSignal;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebRTCController extends Controller
{
    public function offer(Request $r): JsonResponse
    {
        $data = $r->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'offer' => ['required', 'array'],
            'request_mode' => ['nullable', 'in:call,screen'],
        ]);

        $this->authorizeTicket((int) $data['ticket_id']);

        $signal = $this->storeSignal(
            (int) $data['ticket_id'],
            'offer',
            $data['offer'],
            $data['request_mode'] ?? 'call'
        );

        return response()->json(['ok' => true, 'id' => $signal->id]);
    }

    public function answer(Request $r): JsonResponse
    {
        $data = $r->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'answer' => ['required', 'array'],
        ]);

        $this->authorizeTicket((int) $data['ticket_id']);

        $signal = $this->storeSignal(
            (int) $data['ticket_id'],
            'answer',
            $data['answer']
        );

        return response()->json(['ok' => true, 'id' => $signal->id]);
    }

    public function ice(Request $r): JsonResponse
    {
        $data = $r->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'candidate' => ['required', 'array'],
        ]);

        $this->authorizeTicket((int) $data['ticket_id']);

        $signal = $this->storeSignal(
            (int) $data['ticket_id'],
            'ice',
            $data['candidate']
        );

        return response()->json(['ok' => true, 'id' => $signal->id]);
    }

    public function poll(Request $r): JsonResponse
    {
        $data = $r->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'after_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $ticketId = (int) $data['ticket_id'];
        $afterId = (int) ($data['after_id'] ?? 0);

        $this->authorizeTicket($ticketId);

        $signals = RtcSignal::query()
            ->where('ticket_id', $ticketId)
            ->where('id', '>', $afterId)
            ->where('sender_id', '!=', auth()->id())
            ->orderBy('id')
            ->limit(60)
            ->get(['id', 'type', 'payload', 'sender_id', 'request_mode', 'created_at']);

        return response()->json([
            'ok' => true,
            'signals' => $signals->map(fn (RtcSignal $signal) => [
                'id' => $signal->id,
                'type' => $signal->type,
                'data' => $signal->payload,
                'user_id' => $signal->sender_id,
                'request_mode' => $signal->request_mode,
                'created_at' => $signal->created_at?->toISOString(),
            ])->values(),
        ]);
    }

    private function storeSignal(int $ticketId, string $type, array $payload, ?string $requestMode = null): RtcSignal
    {
        return RtcSignal::create([
            'ticket_id' => $ticketId,
            'sender_id' => auth()->id(),
            'type' => $type,
            'payload' => $payload,
            'request_mode' => $requestMode,
        ]);
    }

    private function authorizeTicket(int $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        $user = auth()->user();

        $isOwner = (int) $ticket->user_id === (int) $user->id;
        $isTechnician = (int) $ticket->technician_id === (int) $user->id;
        $isAdmin = $user->role?->name === 'admin';

        abort_unless($isOwner || $isTechnician || $isAdmin, 403);
    }
}
