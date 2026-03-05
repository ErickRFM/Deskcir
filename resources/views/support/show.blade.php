@extends('layouts.app')

@section('content')
<div class="container py-4 ticket-page">

    <a href="/support" class="btn btn-outline-secondary btn-sm mb-3 d-inline-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="m14 6-6 6 6 6 1.4-1.4L10.8 12l4.6-4.6L14 6Z"/>
        </svg>
        Regresar
    </a>

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-2">{{ $ticket->subject }}</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill bg-warning text-dark text-uppercase">{{ $ticket->status }}</span>
                        <span class="badge rounded-pill bg-info text-dark text-uppercase">{{ $ticket->priority ?? 'media' }}</span>
                        <span class="badge rounded-pill bg-light text-dark border">Ticket #{{ $ticket->id }}</span>
                    </div>
                </div>

                @php $peerName = optional($ticket->technician)->name ?? 'Tecnico'; @endphp
                <x-ticket-call-tools :ticket="$ticket" screen-label="Compartir mi pantalla" call-label="Llamar tecnico" :peer-user-id="optional($ticket->technician)->id" :peer-label="$peerName" />
            </div>

            <p class="text-muted mb-0 mt-3 small">
                Tecnico asignado: {{ optional($ticket->technician)->name ?? 'Sin asignar' }}
            </p>
        </div>
    </section>

    @if($ticket->files->count())
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Evidencia adjunta</h6>
                <div class="row g-3">
                    @foreach($ticket->files as $file)
                        <div class="col-sm-6 col-lg-4">
                            <div class="attachment-box h-100">
                                @if(str_starts_with($file->type, 'image/'))
                                    <img src="{{ asset('storage/'.$file->path) }}" class="w-100 rounded" style="height:180px;object-fit:cover;" alt="Adjunto">
                                @elseif(str_starts_with($file->type, 'video/'))
                                    <video controls class="w-100 rounded" style="height:180px;object-fit:cover;">
                                        <source src="{{ asset('storage/'.$file->path) }}" type="{{ $file->type }}">
                                    </video>
                                @else
                                    <a href="{{ asset('storage/'.$file->path) }}" target="_blank" class="btn btn-outline-deskcir w-100">Ver archivo</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="card border-0 shadow-sm mb-4" id="agenda-tecnica">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h6 class="fw-bold mb-0">Agenda de servicio</h6>
                @php
                    $appointmentTechnicianId = $ticket->technician_id ?: $ticket->assigned_to;
                @endphp
                @if($appointmentTechnicianId)
                    <a href="{{ route('appointments.create', ['ticket_id' => $ticket->id]) }}" class="btn btn-deskcir btn-sm">Agendar visita o recepcion</a>
                @else
                    <span class="badge rounded-pill bg-secondary">Sin tecnico asignado</span>
                @endif
            </div>

            <p class="text-muted small mb-3">La agenda se gestiona en una pantalla aparte para visitas presenciales, recepcion y entrega de equipos.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(isset($appointments) && $appointments->count())
                <div class="table-responsive mt-2">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Tecnico</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                @php
                                    $typeMap = [
                                        'visita_presencial' => 'Visita presencial',
                                        'recepcion_equipo' => 'Recepcion de equipo',
                                        'entrega_equipo' => 'Entrega de equipo',
                                        'diagnostico_en_sitio' => 'Diagnostico en sitio',
                                        'soporte_remoto' => 'Soporte remoto',
                                    ];
                                @endphp
                                <tr>
                                    <td>{{ \Illuminate\Support\Carbon::parse($appointment->date)->format('d/m/Y') }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($appointment->time)->format('H:i') }}</td>
                                    <td>{{ $typeMap[$appointment->type] ?? ucfirst(str_replace('_', ' ', $appointment->type)) }}</td>
                                    <td class="text-capitalize">{{ str_replace('_', ' ', $appointment->status) }}</td>
                                    <td>{{ optional($appointment->technician)->name ?? 'Sin asignar' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted small">No hay servicios agendados para este ticket.</div>
            @endif
        </div>
    </section>

    <section class="mb-4">
        <x-chat :ticket="$ticket" action="/support/{{ $ticket->id }}/message" />
    </section>

    <section class="card border-0 shadow-sm checklist-summary">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h6 class="fw-bold mb-0">Checklist tecnico - Ticket #{{ $ticket->id }}</h6>
                <span class="badge rounded-pill bg-light text-dark border text-uppercase">{{ optional($ticket->checklist)->progress ?? 'pendiente' }}</span>
            </div>

            @php $check = $ticket->checklist; @endphp

            <div class="row g-2">
                <div class="col-md-4"><div class="check-item {{ $check && $check->diagnostico ? 'is-done' : '' }}">Diagnostico realizado</div></div>
                <div class="col-md-4"><div class="check-item {{ $check && $check->reparacion ? 'is-done' : '' }}">Reparacion aplicada</div></div>
                <div class="col-md-4"><div class="check-item {{ $check && $check->pruebas ? 'is-done' : '' }}">Pruebas finales</div></div>
            </div>

            @if(!$check)
                <p class="text-muted small mb-0 mt-3">El tecnico aun no ha llenado el checklist.</p>
            @endif
        </div>
    </section>

</div>

<style>
.attachment-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 8px; }
.checklist-summary .check-item { border: 1px solid #d1d5db; border-radius: 10px; padding: 10px 12px; background: #f9fafb; font-size: 14px; }
.checklist-summary .check-item.is-done { border-color: #0ea5a4; background: rgba(14, 165, 164, 0.1); color: #0f766e; font-weight: 600; }
.dark .attachment-box { background: #0f172a; border-color: #253049; }
.dark .checklist-summary .check-item { background: #0f172a; border-color: #253049; color: #d1d5db; }
</style>
@endsection
