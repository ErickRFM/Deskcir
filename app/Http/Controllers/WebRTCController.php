<?php

namespace App\Http\Controllers;

use App\Models\RtcSignal;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\WebRTCSignal;

class WebRTCController extends Controller
{
    public function offer(Request $r): JsonResponse
    {
        $data = $r->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'offer' => ['required', 'array'],
            'request_mode' => ['nullable', 'in:call,screen-share,screen-request'],
        ]);

        $this->authorizeTicket((int) $data['ticket_id']);

        $signal = $this->storeSignal(
            (int) $data['ticket_id'],
            'offer',
            $data['offer'],
            $data['request_mode'] ?? 'call'
        );

        // broadcast opcional (no rompe polling)
        broadcast(new WebRTCSignal([
            'id'=>$signal->id,
            'ticket_id'=>$data['ticket_id'],
            'type'=>'offer',
            'data'=>$data['offer'],
            'user_id'=>auth()->id(),
            'request_mode'=>$data['request_mode'] ?? 'call'
        ]))->toOthers();

        return response()->json(['ok'=>true,'id'=>$signal->id]);
    }

    public function answer(Request $r): JsonResponse
    {
        $data = $r->validate([
            'ticket_id'=>['required','integer','exists:tickets,id'],
            'answer'=>['required','array'],
        ]);

        $this->authorizeTicket((int)$data['ticket_id']);

        $signal = $this->storeSignal(
            (int)$data['ticket_id'],
            'answer',
            $data['answer']
        );

        broadcast(new WebRTCSignal([
            'id'=>$signal->id,
            'ticket_id'=>$data['ticket_id'],
            'type'=>'answer',
            'data'=>$data['answer'],
            'user_id'=>auth()->id()
        ]))->toOthers();

        return response()->json(['ok'=>true,'id'=>$signal->id]);
    }

    public function ice(Request $r): JsonResponse
    {
        $data=$r->validate([
            'ticket_id'=>['required','integer','exists:tickets,id'],
            'candidate'=>['required','array'],
        ]);

        $this->authorizeTicket((int)$data['ticket_id']);

        $signal=$this->storeSignal(
            (int)$data['ticket_id'],
            'ice',
            $data['candidate']
        );

        broadcast(new WebRTCSignal([
            'id'=>$signal->id,
            'ticket_id'=>$data['ticket_id'],
            'type'=>'ice',
            'data'=>$data['candidate'],
            'user_id'=>auth()->id()
        ]))->toOthers();

        return response()->json(['ok'=>true,'id'=>$signal->id]);
    }

    public function hangup(Request $r): JsonResponse
    {
        $data = $r->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'reason' => ['nullable', 'in:ended,declined,denied,busy,reload,disconnected'],
        ]);

        $this->authorizeTicket((int) $data['ticket_id']);

        $payload = ['reason' => $data['reason'] ?? 'ended'];

        $signal = $this->storeSignal(
            (int) $data['ticket_id'],
            'hangup',
            $payload
        );

        broadcast(new WebRTCSignal([
            'id' => $signal->id,
            'ticket_id' => $data['ticket_id'],
            'type' => 'hangup',
            'data' => $payload,
            'user_id' => auth()->id(),
        ]))->toOthers();

        return response()->json(['ok' => true, 'id' => $signal->id]);
    }

    public function poll(Request $r): JsonResponse
    {
        $data=$r->validate([
            'ticket_id'=>['required','integer','exists:tickets,id'],
            'after_id'=>['nullable','integer','min:0'],
        ]);

        $ticketId=(int)$data['ticket_id'];
        $afterId=(int)($data['after_id'] ?? 0);

        $this->authorizeTicket($ticketId);

        $signals=RtcSignal::query()
            ->where('ticket_id',$ticketId)
            ->where('id','>',$afterId)
            ->where('sender_id','!=',auth()->id())
            ->orderBy('id')
            ->limit(60)
            ->get(['id','type','payload','sender_id','request_mode','created_at']);

        return response()->json([
            'ok'=>true,
            'signals'=>$signals->map(fn($s)=>[
                'id'=>$s->id,
                'type'=>$s->type,
                'data'=>$s->payload,
                'user_id'=>$s->sender_id,
                'request_mode'=>$s->request_mode,
                'created_at'=>$s->created_at?->toISOString()
            ])
        ]);
    }

    private function storeSignal(int $ticketId,string $type,array $payload,?string $requestMode=null):RtcSignal
    {
        return RtcSignal::create([
            'ticket_id'=>$ticketId,
            'sender_id'=>auth()->id(),
            'type'=>$type,
            'payload'=>$payload,
            'request_mode'=>$requestMode
        ]);
    }

    private function authorizeTicket(int $ticketId):void
    {
        $ticket=Ticket::findOrFail($ticketId);
        $user=auth()->user();

        $isOwner=(int)$ticket->user_id === (int)$user->id;
        $isTechnician=(int)$ticket->technician_id === (int)$user->id;
        $isAdmin=$user->role?->name === 'admin';

        abort_unless($isOwner || $isTechnician || $isAdmin,403);
    }
}
