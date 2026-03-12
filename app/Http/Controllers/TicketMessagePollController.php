<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketMessagePollController extends Controller
{
    public function __invoke(Request $request, int $ticketId): JsonResponse
    {
        $afterId = (int) $request->query('after_id', 0);

        $ticket = Ticket::findOrFail($ticketId);
        $user = auth()->user();

        $isOwner = (int) $ticket->user_id === (int) $user->id;
        $isTechnician = (int) $ticket->technician_id === (int) $user->id;
        $isAdmin = $user->role?->name === 'admin';

        abort_unless($isOwner || $isTechnician || $isAdmin, 403);

        $ticket->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        $messages = $ticket->messages()
            ->with('user:id,name')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get();

        return response()->json([
            'ok' => true,
            'messages' => $messages->map(function ($m) use ($user) {
                return [
                    'id' => $m->id,
                    'user_id' => $m->user_id,
                    'user_name' => $m->user?->name ?? 'Usuario',
                    'message' => (string) $m->message,
                    'file_url' => $m->file_url,
                    'time' => $m->created_at?->format('H:i'),
                    'is_me' => (int) $m->user_id === (int) $user->id,
                    'seen' => !is_null($m->seen_at),
                ];
            })->values(),
        ]);
    }
}
