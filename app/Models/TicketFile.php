<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketFile extends Model
{
    protected $fillable = [
        'ticket_id',
        'path',
        'type',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}