<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistPhoto extends Model
{

    protected $fillable=[
        'ticket_checklist_id',
        'path'
    ];

    public function checklist()
    {
        return $this->belongsTo(TicketChecklist::class);
    }

}