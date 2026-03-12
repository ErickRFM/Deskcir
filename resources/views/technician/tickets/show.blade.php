@extends('layouts.app')

@section('title', 'Tecnico Ticket #' . $ticket->id)

@section('content')
<div class="ticket-workspace">
    <a href="{{ route('technician.tickets') }}" class="btn btn-outline-secondary btn-sm mb-3 d-inline-flex align-items-center gap-2">
        <span class="material-symbols-outlined">arrow_back</span>
        Regresar
    </a>

    <section class="ticket-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="ticket-hero__grid">
                <div>
                    <p class="ticket-hero__eyebrow">Panel tecnico</p>
                    <h1 class="ticket-hero__title">{{ $ticket->subject }}</h1>
                    <p class="ticket-hero__subtitle mb-3">Cliente: {{ $ticket->user->name }}</p>
                    <div class="ticket-hero__badges">
                        <span class="badge text-bg-warning text-uppercase">{{ $ticket->status }}</span>
                        <span class="badge text-bg-info text-uppercase">{{ $ticket->priority ?? 'media' }}</span>
                        <span class="badge text-bg-light border text-dark">Ticket #{{ $ticket->id }}</span>
                    </div>
                </div>

                <x-ticket-call-tools :ticket="$ticket" screen-label="Compartir pantalla" call-label="Videollamada" :peer-user-id="$ticket->user->id" :peer-label="$ticket->user->name" />
            </div>
        </div>
    </section>

    @if($ticket->files->count())
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-deskcir">photo_library</span>
                    <h5 class="mb-0 fw-bold">Evidencia del cliente</h5>
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

    <section class="mb-4">
        <x-chat :ticket="$ticket" action="/technician/tickets/{{ $ticket->id }}/reply" />
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <p class="ticket-section__eyebrow mb-1">Checklist tecnico</p>
                    <h5 class="fw-bold mb-0">Avance del servicio</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border text-uppercase">{{ optional($ticket->checklist)->progress ?? 'pendiente' }}</span>
                    <a href="{{ route('technician.checklist',$ticket->id) }}" class="btn btn-sm btn-deskcir d-inline-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">checklist</span>
                        Abrir checklist completo
                    </a>
                </div>
            </div>

            @php $check = $ticket->checklist; @endphp
            <div class="row g-3">
                <div class="col-md-4"><div class="ticket-check-item {{ $check && $check->diagnostico ? 'is-done' : '' }}">Diagnostico realizado</div></div>
                <div class="col-md-4"><div class="ticket-check-item {{ $check && $check->reparacion ? 'is-done' : '' }}">Reparacion aplicada</div></div>
                <div class="col-md-4"><div class="ticket-check-item {{ $check && $check->pruebas ? 'is-done' : '' }}">Pruebas finales</div></div>
            </div>
        </div>
    </section>
</div>

<style>
.ticket-workspace { --ticket-border: #dce7ef; }
.ticket-hero {
    background: linear-gradient(135deg, #071827 0%, #0d2438 55%, #12344a 100%);
    color: #edf9ff;
    border: 1px solid rgba(80, 187, 212, 0.22) !important;
}
.ticket-hero__grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(320px, 430px); gap: 1.5rem; align-items: start; }
.ticket-hero__eyebrow, .ticket-section__eyebrow { font-size: .76rem; text-transform: uppercase; letter-spacing: .12em; font-weight: 800; color: #74d8ee; }
.ticket-hero__title { font-size: clamp(1.7rem, 2.8vw, 2.5rem); font-weight: 800; margin-bottom: .45rem; }
.ticket-hero__subtitle { color: #c2e6f0; }
.ticket-hero__badges { display: flex; gap: .55rem; flex-wrap: wrap; }
.ticket-media-card { border: 1px solid var(--ticket-border); border-radius: 18px; overflow: hidden; background: #f7fbfd; min-height: 220px; display: grid; place-items: center; }
.ticket-media-card__image { width: 100%; height: 220px; object-fit: cover; display: block; }
.ticket-media-card__file { display: inline-flex; align-items: center; gap: .45rem; color: #0f5d6f; font-weight: 700; text-decoration: none; }
.ticket-check-item { border: 1px solid #d1dce4; border-radius: 14px; padding: .9rem 1rem; background: #f8fbfd; font-weight: 600; }
.ticket-check-item.is-done { border-color: #62c6d7; background: #eafcff; color: #0a6f81; }
.dark .ticket-media-card, .dark .ticket-check-item { background: #0f172a; border-color: #24364c; color: #dce7f5; }
@media (max-width: 991.98px) { .ticket-hero__grid { grid-template-columns: 1fr; } }
</style>
@endsection
