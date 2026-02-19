<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class WebRTCController extends Controller
{
    public function offer(Request $r)
    {
        broadcast(new \App\Events\WebRTCSignal([
            'ticket_id' => $r->ticket_id,
            'type'      => 'offer',
            'data'      => $r->offer,
            'user_id'   => auth()->id()
        ]));

        return response()->json(['ok'=>true]);
    }

    public function answer(Request $r)
    {
        broadcast(new \App\Events\WebRTCSignal([
            'ticket_id' => $r->ticket_id,
            'type'      => 'answer',
            'data'      => $r->answer,
            'user_id'   => auth()->id()
        ]));

        return response()->json(['ok'=>true]);
    }

    public function ice(Request $r)
    {
        broadcast(new \App\Events\WebRTCSignal([
            'ticket_id' => $r->ticket_id,
            'type'      => 'ice',
            'data'      => $r->candidate,
            'user_id'   => auth()->id()
        ]));

        return response()->json(['ok'=>true]);
    }
}