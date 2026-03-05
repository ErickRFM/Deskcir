<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketChecklist extends Model
{
    protected $fillable = [
        'ticket_id',
        'technician_id',
        'diagnostico',
        'reparacion',
        'pruebas',
        'diagnostico_notes',
        'reparacion_notes',
        'pruebas_notes',
        'errores',
        'observaciones',
        'status',
        'progress',
    ];

    protected $casts = [
        'diagnostico' => 'boolean',
        'reparacion' => 'boolean',
        'pruebas' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function photos()
    {
        return $this->hasMany(ChecklistPhoto::class, 'ticket_checklist_id');
    }
}
