@extends('layouts.app')

@section('title', 'Checklist Ticket #' . $ticket->id)

@section('content')
@php
    $checklist = $ticket->checklist;
    $progress = $checklist?->progress ?? 'pendiente';
    $status = $checklist?->status;
    $photos = $checklist?->photos ?? collect();
@endphp

<div class="container py-4 admin-checklist-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <p class="ticket-section__eyebrow mb-1">Checklist tecnico</p>
            <h2 class="fw-bold mb-1">Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</h2>
            <p class="text-muted mb-0">Vista completa del seguimiento tecnico para administracion.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-outline-deskcir d-inline-flex align-items-center gap-2">
                <span class="material-symbols-outlined">arrow_back</span>
                Volver al ticket
            </a>
            <a href="{{ route('admin.tickets.checklist.pdf', $ticket->id) }}" class="btn btn-deskcir d-inline-flex align-items-center gap-2">
                <span class="material-symbols-outlined">download</span>
                Exportar PDF
            </a>
        </div>
    </div>

    <section class="ticket-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="ticket-hero__grid ticket-hero__grid--single">
                <div>
                    <p class="ticket-hero__eyebrow">Administracion de servicio</p>
                    <h1 class="ticket-hero__title">{{ $ticket->subject }}</h1>
                    <p class="ticket-hero__subtitle mb-3">
                        Cliente: {{ $ticket->user->name }} |
                        Tecnico: {{ $ticket->technician?->name ?? 'Sin asignar' }}
                    </p>
                    <div class="ticket-hero__badges">
                        <span class="badge text-bg-warning text-uppercase">{{ $ticket->status }}</span>
                        <span class="badge text-bg-info text-uppercase">{{ $ticket->priority ?? 'media' }}</span>
                        <span class="badge bg-light text-dark border text-uppercase">{{ $progress }}</span>
                        @if($status)
                            <span class="badge bg-success-subtle text-success-emphasis border text-uppercase">{{ $status }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="ticket-check-card {{ $checklist && $checklist->diagnostico ? 'is-done' : '' }}">
                <div class="ticket-check-card__head">
                    <span class="material-symbols-outlined">troubleshoot</span>
                    Diagnostico
                </div>
                <p class="ticket-check-card__state">{{ $checklist && $checklist->diagnostico ? 'Completado' : 'Pendiente' }}</p>
                <div class="ticket-check-card__body">
                    {{ $checklist?->diagnostico_notes ?: 'Sin notas registradas en esta etapa.' }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="ticket-check-card {{ $checklist && $checklist->reparacion ? 'is-done' : '' }}">
                <div class="ticket-check-card__head">
                    <span class="material-symbols-outlined">build</span>
                    Reparacion
                </div>
                <p class="ticket-check-card__state">{{ $checklist && $checklist->reparacion ? 'Completado' : 'Pendiente' }}</p>
                <div class="ticket-check-card__body">
                    {{ $checklist?->reparacion_notes ?: 'Sin notas registradas en esta etapa.' }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="ticket-check-card {{ $checklist && $checklist->pruebas ? 'is-done' : '' }}">
                <div class="ticket-check-card__head">
                    <span class="material-symbols-outlined">task_alt</span>
                    Pruebas finales
                </div>
                <p class="ticket-check-card__state">{{ $checklist && $checklist->pruebas ? 'Completado' : 'Pendiente' }}</p>
                <div class="ticket-check-card__body">
                    {{ $checklist?->pruebas_notes ?: 'Sin notas registradas en esta etapa.' }}
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-deskcir">warning</span>
                        <h5 class="fw-bold mb-0">Errores detectados</h5>
                    </div>
                    <div class="checklist-note-box">
                        {{ $checklist?->errores ?: 'El tecnico no reporto errores adicionales.' }}
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-deskcir">notes</span>
                        <h5 class="fw-bold mb-0">Observaciones tecnicas</h5>
                    </div>
                    <div class="checklist-note-box">
                        {{ $checklist?->observaciones ?: 'No hay observaciones adicionales registradas.' }}
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12">
            <section class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-deskcir">photo_library</span>
                        <h5 class="fw-bold mb-0">Fotos del servicio</h5>
                    </div>

                    @if($photos->isEmpty())
                        <div class="checklist-empty-box">Todavia no hay fotos registradas para este checklist.</div>
                    @else
                        <div class="row g-3">
                            @foreach($photos as $photo)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <a href="{{ $photo->url }}" target="_blank" rel="noopener" class="checklist-photo-link">
                                        <img src="{{ $photo->url }}" alt="Foto del checklist" class="checklist-photo-image">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>

<style>
.ticket-section__eyebrow { font-size: .76rem; text-transform: uppercase; letter-spacing: .12em; font-weight: 800; color: #74d8ee; }
.ticket-hero {
    background: linear-gradient(135deg, #071827 0%, #0d2438 55%, #12344a 100%);
    color: #edf9ff;
    border: 1px solid rgba(80, 187, 212, 0.22) !important;
}
.ticket-hero__grid--single { display: block; }
.ticket-hero__eyebrow { font-size: .76rem; text-transform: uppercase; letter-spacing: .12em; font-weight: 800; color: #74d8ee; }
.ticket-hero__title { font-size: clamp(1.7rem, 2.8vw, 2.5rem); font-weight: 800; margin-bottom: .45rem; }
.ticket-hero__subtitle { color: #c2e6f0; }
.ticket-hero__badges { display: flex; gap: .55rem; flex-wrap: wrap; }
.ticket-check-card {
    height: 100%;
    border: 1px solid #d7e4eb;
    border-radius: 18px;
    background: #f8fbfd;
    padding: 1rem 1rem 1.1rem;
}
.ticket-check-card.is-done {
    border-color: #62c6d7;
    background: #ebfcff;
}
.ticket-check-card__head {
    display: flex;
    align-items: center;
    gap: .55rem;
    font-weight: 800;
    color: #12354b;
    margin-bottom: .5rem;
}
.ticket-check-card__state {
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 700;
    color: #5c7489;
    margin-bottom: .8rem;
}
.ticket-check-card__body,
.checklist-note-box {
    white-space: pre-wrap;
    color: #274559;
    line-height: 1.65;
}
.checklist-note-box {
    min-height: 160px;
    border: 1px solid #dce8ef;
    border-radius: 16px;
    background: #f8fbfd;
    padding: 1rem;
}
.checklist-empty-box {
    border: 1px dashed #cbd9e2;
    border-radius: 16px;
    background: #f8fbfd;
    color: #5f768a;
    text-align: center;
    padding: 1.4rem;
}
.checklist-photo-link {
    display: block;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #d7e4eb;
    background: #0d1728;
}
.checklist-photo-image {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
}
.dark .ticket-check-card,
.dark .checklist-note-box,
.dark .checklist-empty-box,
.dark .checklist-photo-link {
    background: #0f172a;
    border-color: #24364c;
    color: #dce7f5;
}
.dark .ticket-check-card__head,
.dark .ticket-check-card__body,
.dark .checklist-note-box {
    color: #dce7f5;
}
.dark .ticket-check-card__state,
.dark .checklist-empty-box {
    color: #9fb7cd;
}
</style>
@endsection
