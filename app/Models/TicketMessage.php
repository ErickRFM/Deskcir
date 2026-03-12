<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use ResolvesMediaUrls;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'file',
        'disk',
        'seen_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
    ];

    protected $appends = ['file_url'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->file, $this->disk);
    }
}
