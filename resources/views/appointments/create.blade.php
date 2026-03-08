@extends('layouts.app')

@section('title', 'Agendar servicio')

@section('content')
<div class="container py-4 schedule-create" style="max-width: 920px;">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Agendar servicio tecnico</h4>
            <p class="text-muted mb-0">Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</p>
        </div>
        <a href="/support/{{ $ticket->id }}#agenda-tecnica" class="btn btn-outline-secondary btn-sm">Volver al ticket</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card border-0 schedule-card">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-lg-7">
                    <form method="POST" action="{{ route('appointments.store') }}" class="row g-3">
                        @csrf
                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                        <div class="col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="date" class="form-control form-control-dark" min="{{ now()->toDateString() }}" value="{{ old('date') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Hora</label>
                            <input type="time" name="time" class="form-control form-control-dark" value="{{ old('time') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Tipo de servicio</label>
                            <select name="type" class="form-select form-control-dark" required>
                                <option value="visita_presencial" @selected(old('type') === 'visita_presencial')>Visita presencial</option>
                                <option value="recepcion_equipo" @selected(old('type') === 'recepcion_equipo')>Recepcion de equipo</option>
                                <option value="entrega_equipo" @selected(old('type') === 'entrega_equipo')>Entrega de equipo</option>
                                <option value="diagnostico_en_sitio" @selected(old('type') === 'diagnostico_en_sitio')>Diagnostico en sitio</option>
                                <option value="soporte_remoto" @selected(old('type') === 'soporte_remoto')>Soporte remoto</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notas (opcional)</label>
                            <textarea name="notes" rows="3" class="form-control form-control-dark" placeholder="Ejemplo: recibir laptop en mostrador a nombre de cliente Demo">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="/support/{{ $ticket->id }}#agenda-tecnica" class="btn btn-outline-secondary">Cancelar</a>
                            <button class="btn btn-deskcir">Guardar agenda</button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-5">
                    <div class="schedule-info h-100">
                        <h6 class="fw-bold mb-3">Guia rapida</h6>
                        <ul class="mb-0">
                            <li><strong>Visita presencial:</strong> atencion en domicilio o empresa.</li>
                            <li><strong>Recepcion de equipo:</strong> entrega fisica de equipo para revision.</li>
                            <li><strong>Entrega de equipo:</strong> devolucion programada al cliente.</li>
                            <li><strong>Diagnostico en sitio:</strong> evaluacion tecnica sin retiro de equipo.</li>
                            <li><strong>Soporte remoto:</strong> asistencia a distancia.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.schedule-create {
    --schedule-bg: #ffffff;
    --schedule-border: #dbe3ee;
    --schedule-text: #0f172a;
    --schedule-muted: #64748b;
    --schedule-input-bg: #ffffff;
    --schedule-input-text: #0f172a;
    --schedule-input-border: #c9d5e5;
    --schedule-focus: #0e9ab8;
    --schedule-panel-bg: #f8fbff;
}

.schedule-create .schedule-card {
    background: var(--schedule-bg);
    border: 1px solid var(--schedule-border) !important;
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
}

.schedule-create .form-label,
.schedule-create h4,
.schedule-create h6,
.schedule-create li,
.schedule-create p {
    color: var(--schedule-text);
}

.schedule-create .text-muted {
    color: var(--schedule-muted) !important;
}

.schedule-create .form-control-dark {
    background: var(--schedule-input-bg);
    border: 1px solid var(--schedule-input-border);
    color: var(--schedule-input-text);
    border-radius: 10px;
}

.schedule-create .form-control-dark:focus {
    background: var(--schedule-input-bg);
    color: var(--schedule-input-text);
    border-color: var(--schedule-focus);
    box-shadow: 0 0 0 0.2rem rgba(14, 154, 184, 0.18);
}

.schedule-create .form-control-dark::placeholder {
    color: #7c8ba1;
}

.schedule-info {
    background: var(--schedule-panel-bg);
    border: 1px solid var(--schedule-border);
    border-radius: 12px;
    padding: 16px;
}

.schedule-info ul {
    padding-left: 1rem;
    display: grid;
    gap: .45rem;
}

@media (max-width: 575.98px) {
    .schedule-create .card-body {
        padding: 1rem;
    }

    .schedule-create .btn {
        width: 100%;
    }

    .schedule-create .col-12.d-flex {
        flex-direction: column-reverse;
        align-items: stretch !important;
    }
}

.dark .schedule-create {
    --schedule-bg: #0f1b34;
    --schedule-border: #233454;
    --schedule-text: #dbe7ff;
    --schedule-muted: #9fb2d4;
    --schedule-input-bg: #081227;
    --schedule-input-text: #ecf3ff;
    --schedule-input-border: #2c436a;
    --schedule-focus: #2bb8d6;
    --schedule-panel-bg: #081227;
}

.dark .schedule-create .form-control-dark::placeholder {
    color: #6f89b0;
}
</style>
@endsection

