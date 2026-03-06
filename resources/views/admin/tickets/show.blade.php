@extends('layouts.app')

@section('content')
<div class="container py-4 ticket-page">

    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm mb-3 d-inline-flex align-items-center gap-2">
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
                    <p class="text-muted mb-2">Cliente: {{ $ticket->user->name }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill bg-warning text-dark text-uppercase">{{ $ticket->status }}</span>
                        <span class="badge rounded-pill bg-info text-dark text-uppercase">{{ $ticket->priority ?? 'media' }}</span>
                        <span class="badge rounded-pill bg-light text-dark border">Ticket #{{ $ticket->id }}</span>
                    </div>
                </div>

                <div class="d-flex flex-column align-items-end gap-2">
                    @if($ticket->technician)
                        <span class="badge rounded-pill bg-success-subtle text-success-emphasis border px-3 py-2">
                            Tecnico: {{ $ticket->technician->name }}
                        </span>
                    @else
                        <span class="badge rounded-pill bg-secondary">Sin tecnico asignado</span>
                    @endif

                    <x-ticket-call-tools :ticket="$ticket" screen-label="Compartir pantalla" call-label="Videollamada" :peer-user-id="$ticket->user->id" :peer-label="$ticket->user->name" />
                </div>
            </div>
        </div>
    </section>

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Gestion del ticket</h6>

            @if(!$ticket->technician_id)
                <form method="POST" action="{{ route('admin.tickets.assign',$ticket->id) }}" class="mb-4">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Asignar a tecnico</label>
                            <select name="technician_id" class="form-select" required>
                                <option value="">Seleccionar tecnico</option>
                                @foreach($technicians as $tec)
                                    <option value="{{ $tec->id }}">{{ $tec->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-deskcir w-100">Asignar tecnico</button>
                        </div>
                    </div>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.tickets.status',$ticket->id) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="abierto" {{ $ticket->status=='abierto'?'selected':'' }}>Pendiente</option>
                            <option value="en_proceso" {{ $ticket->status=='en_proceso'?'selected':'' }}>En proceso</option>
                            <option value="cerrado" {{ $ticket->status=='cerrado'?'selected':'' }}>Cerrado</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Prioridad</label>
                        <select name="priority" class="form-select">
                            <option value="baja" {{ $ticket->priority=='baja'?'selected':'' }}>Baja</option>
                            <option value="media" {{ $ticket->priority=='media'?'selected':'' }}>Media</option>
                            <option value="alta" {{ $ticket->priority=='alta'?'selected':'' }}>Alta</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tecnico asignado</label>
                        <input type="text" class="form-control" value="{{ optional($ticket->technician)->name ?? 'Sin asignar' }}" disabled>
                    </div>
                </div>

                <button class="btn btn-warning mt-3 px-4">Actualizar ticket</button>
            </form>
        </div>
    </section>

    @if($ticket->files->count())
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Evidencia del cliente</h6>
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

    <section class="mb-4">
        <x-chat :ticket="$ticket" action="{{ route('admin.tickets.reply',$ticket->id) }}" />
    </section>

    <section class="card border-0 shadow-sm checklist-summary">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h6 class="fw-bold mb-0">Checklist Tecnico - Ticket #{{ $ticket->id }}</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-light text-dark border text-uppercase">
                        {{ optional($ticket->checklist)->progress ?? 'pendiente' }}
                    </span>
                    <a href="{{ route('technician.checklist',$ticket->id) }}" class="btn btn-sm btn-outline-deskcir">Ver checklist completo</a>
                </div>
            </div>

            @php
                $check = $ticket->checklist;
            @endphp

            <div class="row g-2">
                <div class="col-md-4">
                    <div class="check-item {{ $check && $check->diagnostico ? 'is-done' : '' }}">Diagnostico realizado</div>
                </div>
                <div class="col-md-4">
                    <div class="check-item {{ $check && $check->reparacion ? 'is-done' : '' }}">Reparacion aplicada</div>
                </div>
                <div class="col-md-4">
                    <div class="check-item {{ $check && $check->pruebas ? 'is-done' : '' }}">Pruebas finales</div>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
.attachment-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px;
}

.checklist-summary .check-item {
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 10px 12px;
    background: #f9fafb;
    font-size: 14px;
}

.checklist-summary .check-item.is-done {
    border-color: #0ea5a4;
    background: rgba(14, 165, 164, 0.1);
    color: #0f766e;
    font-weight: 600;
}

.dark .attachment-box {
    background: #0f172a;
    border-color: #253049;
}

.dark .checklist-summary .check-item {
    background: #0f172a;
    border-color: #253049;
    color: #d1d5db;
}
</style>
@endsection



