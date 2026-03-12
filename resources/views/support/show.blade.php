@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="ticket-workspace">
    <a href="/support" class="btn btn-outline-secondary btn-sm mb-3 d-inline-flex align-items-center gap-2">
        <span class="material-symbols-outlined">arrow_back</span>
        Regresar
    </a>

    <section class="ticket-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="ticket-hero__grid">
                <div>
                    <p class="ticket-hero__eyebrow">Mesa de ayuda</p>
                    <h1 class="ticket-hero__title">{{ $ticket->subject }}</h1>
                    <p class="ticket-hero__subtitle mb-3">Tecnico asignado: {{ optional($ticket->technician)->name ?? 'Sin asignar' }}</p>
                    <div class="ticket-hero__badges">
                        <span class="badge text-bg-warning text-uppercase">{{ $ticket->status }}</span>
                        <span class="badge text-bg-info text-uppercase">{{ $ticket->priority ?? 'media' }}</span>
                        <span class="badge text-bg-light border text-dark">Ticket #{{ $ticket->id }}</span>
                    </div>
                </div>

                @php $peerName = optional($ticket->technician)->name ?? 'Tecnico'; @endphp
                <x-ticket-call-tools :ticket="$ticket" screen-label="Compartir mi pantalla" call-label="Llamar tecnico" :peer-user-id="optional($ticket->technician)->id" :peer-label="$peerName" />
            </div>
        </div>
    </section>

    @if($ticket->files->count())
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-deskcir">photo_library</span>
                    <h5 class="mb-0 fw-bold">Evidencia adjunta</h5>
                </div>
                <div class="row g-3">
                    @foreach($ticket->files as $file)
                        <div class="col-sm-6 col-lg-4">
                            <div class="ticket-media-card h-100">
                                @if(str_starts_with($file->type, 'image/'))
                                    <a href="{{ $file->url }}" target="_blank" rel="noopener">
                                        <img src="{{ $file->url }}" class="ticket-media-card__image" alt="Adjunto del ticket">
                                    </a>
                                @elseif(str_starts_with($file->type, 'video/'))
                                    <video controls class="ticket-media-card__image">
                                        <source src="{{ $file->url }}" type="{{ $file->type }}">
                                    </video>
                                @else
                                    <a href="{{ $file->url }}" target="_blank" class="ticket-media-card__file">
                                        <span class="material-symbols-outlined">draft</span>
                                        Ver archivo
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <p class="ticket-section__eyebrow mb-1">Agenda</p>
                    <h5 class="fw-bold mb-0">Servicios programados</h5>
                </div>
                @php $appointmentTechnicianId = $ticket->technician_id ?: $ticket->assigned_to; @endphp
                @if($appointmentTechnicianId)
                    <a href="{{ route('appointments.create', ['ticket_id' => $ticket->id]) }}" class="btn btn-deskcir btn-sm d-inline-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">event_available</span>
                        Agendar visita o recepcion
                    </a>
                @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border">Sin tecnico asignado</span>
                @endif
            </div>

            <p class="text-muted small mb-3">La agenda vive en una pantalla separada para visitas, recepcion y entrega de equipos.</p>

            @if(isset($appointments) && $appointments->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
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
                <div class="ticket-empty-box">No hay servicios agendados para este ticket.</div>
            @endif
        </div>
    </section>

    <section class="mb-4">
        <x-chat :ticket="$ticket" action="/support/{{ $ticket->id }}/message" />
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <p class="ticket-section__eyebrow mb-1">Checklist tecnico</p>
                    <h5 class="fw-bold mb-0">Estado de servicio</h5>
                </div>
                <span class="badge bg-light text-dark border text-uppercase">{{ optional($ticket->checklist)->progress ?? 'pendiente' }}</span>
            </div>

            @php $check = $ticket->checklist; @endphp
            <div class="row g-3">
                <div class="col-md-4"><div class="ticket-check-item {{ $check && $check->diagnostico ? 'is-done' : '' }}">Diagnostico realizado</div></div>
                <div class="col-md-4"><div class="ticket-check-item {{ $check && $check->reparacion ? 'is-done' : '' }}">Reparacion aplicada</div></div>
                <div class="col-md-4"><div class="ticket-check-item {{ $check && $check->pruebas ? 'is-done' : '' }}">Pruebas finales</div></div>
            </div>

            @if(!$check)
                <p class="text-muted small mb-0 mt-3">El tecnico aun no ha llenado el checklist.</p>
            @endif
        </div>
    </section>
</div>

<style>
.ticket-workspace {
    --ticket-surface: #ffffff;
    --ticket-border: #dce7ef;
}
.ticket-hero {
    background: linear-gradient(135deg, #071827 0%, #0d2438 55%, #12344a 100%);
    color: #edf9ff;
    border: 1px solid rgba(80, 187, 212, 0.22) !important;
}
.ticket-hero__grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(320px, 420px);
    gap: 1.5rem;
    align-items: start;
}
.ticket-hero__eyebrow,
.ticket-section__eyebrow {
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .12em;
    font-weight: 800;
    color: #74d8ee;
}
.ticket-hero__title {
    font-size: clamp(1.7rem, 2.8vw, 2.5rem);
    font-weight: 800;
    margin-bottom: .45rem;
}
.ticket-hero__subtitle { color: #c2e6f0; }
.ticket-hero__badges { display: flex; gap: .55rem; flex-wrap: wrap; }
.ticket-media-card {
    border: 1px solid var(--ticket-border);
    border-radius: 18px;
    overflow: hidden;
    background: #f7fbfd;
    min-height: 220px;
    display: grid;
    place-items: center;
}
.ticket-media-card__image {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
}
.ticket-media-card__file {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    color: #0f5d6f;
    font-weight: 700;
    text-decoration: none;
}
.ticket-empty-box {
    border: 1px dashed #cbd9e3;
    border-radius: 14px;
    padding: 1rem;
    color: #62798d;
    background: #f8fbfd;
}
.ticket-check-item {
    border: 1px solid #d1dce4;
    border-radius: 14px;
    padding: .9rem 1rem;
    background: #f8fbfd;
    font-weight: 600;
}
.ticket-check-item.is-done {
    border-color: #62c6d7;
    background: #eafcff;
    color: #0a6f81;
}
.dark .ticket-media-card,
.dark .ticket-empty-box,
.dark .ticket-check-item {
    background: #0f172a;
    border-color: #24364c;
    color: #dce7f5;
}
@media (max-width: 991.98px) {
    .ticket-hero__grid { grid-template-columns: 1fr; }
}
</style>
@endsection
