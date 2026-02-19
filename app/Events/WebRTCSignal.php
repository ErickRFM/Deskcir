<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebRTCSignal implements ShouldBroadcast
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->payload['ticket_id']);
    }

    public function broadcastAs()
    {
        return 'WebRTCSignal';
    }

    public function broadcastWith()
    {
        return $this->payload;
    }
}