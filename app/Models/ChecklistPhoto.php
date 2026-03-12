<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistPhoto extends Model
{
    use HasFactory;
    use ResolvesMediaUrls;

    protected $fillable = [
        'ticket_checklist_id',
        'path',
        'disk',
    ];

    protected $appends = ['url'];

    public function checklist()
    {
        return $this->belongsTo(TicketChecklist::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->path, $this->disk);
    }
}
