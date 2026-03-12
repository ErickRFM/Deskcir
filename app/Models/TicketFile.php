<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Model;

class TicketFile extends Model
{
    use ResolvesMediaUrls;

    protected $fillable = [
        'ticket_id',
        'path',
        'type',
        'disk',
    ];

    protected $appends = ['url'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->path, $this->disk);
    }
}
