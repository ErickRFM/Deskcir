@extends('layouts.app')

@section('title', 'Tecnico | Deskcir')

@section('content')
<div class="container py-5">

    <div class="mb-5">
        <h3 class="fw-bold mb-1">Panel del Tecnico</h3>
        <p class="text-muted">Control de tickets, agenda y seguimiento de servicios</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">Asignados</h6>
                    <h2 class="fw-bold mb-0">{{ $asignados ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">En proceso</h6>
                    <h2 class="fw-bold mb-0">{{ $proceso ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">Cerrados</h6>
                    <h2 class="fw-bold mb-0">{{ $cerrados ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">Hoy</h6>
                    <h2 class="fw-bold mb-0">{{ $hoy ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Tickets asignados</h5>
                    <p class="text-muted mb-4">Gestiona incidencias, responde al cliente y sube evidencias</p>
                    <a href="{{ route('technician.tickets') }}" class="btn btn-deskcir w-100 py-2">Ir a tickets</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Agenda</h5>
                    <p class="text-muted mb-4">Citas y servicios programados</p>
                    <a href="{{ route('technician.calendar') }}" class="btn btn-deskcir w-100 py-2">Ver agenda</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Actividad reciente</h5>

            @if($recientes->isEmpty())
                <div class="text-center py-4 text-muted">Sin actividad reciente</div>
            @else
                @foreach($recientes as $r)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <strong>#{{ $r->id }} {{ $r->subject }}</strong>
                                <div class="small text-muted">Cliente: {{ $r->user->name ?? 'N/A' }}</div>
                            </div>
                            <small class="text-muted">{{ $r->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Ultimos Tickets</h5>

            @if($tickets->count())
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Estado</th>
                        <th>Checklist</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tickets as $ticket)
                        @php
                            $checklistStatus = optional($ticket->checklist)->status;
                            $checklistDone = $checklistStatus === 'finalizado';
                        @endphp
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $ticket->status }}</span>
                            </td>
                            <td>
                                @if($checklistDone)
                                    <span class="badge bg-success">Completado</span>
                                @elseif($ticket->checklist)
                                    <span class="badge bg-warning text-dark">En curso</span>
                                @else
                                    <span class="badge bg-secondary">Sin iniciar</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('technician.tickets.show', $ticket->id) }}" class="btn btn-sm btn-deskcir">Ver</a>
                                    <a href="{{ route('technician.checklist', $ticket->id) }}" class="btn btn-sm btn-outline-deskcir">Checklist</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted mb-0">No hay tickets recientes</p>
            @endif
        </div>
    </div>

</div>
@endsection
