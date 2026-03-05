<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RtcSignal extends Model
{
    protected $fillable = [
        'ticket_id',
        'sender_id',
        'type',
        'payload',
        'request_mode',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
