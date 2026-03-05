@extends('layouts.app')

@section('title', 'Agendar servicio')

@section('content')
<div class="container py-4 schedule-create" style="max-width: 920px;">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Agendar servicio tecnico</h4>
            <p class="text-muted mb-0">Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</p>
        </div>
        <a href="/support/{{ $ticket->id }}#agenda-tecnica" class="btn btn-outline-light btn-sm">Volver al ticket</a>
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
.schedule-create .schedule-card {
    background: #0f1b34;
    border: 1px solid #233454 !important;
    box-shadow: none;
}

.schedule-create .form-label,
.schedule-create h4,
.schedule-create h6,
.schedule-create li,
.schedule-create p {
    color: #dbe7ff;
}

.schedule-create .text-muted {
    color: #9fb2d4 !important;
}

.schedule-create .form-control-dark {
    background: #081227;
    border: 1px solid #2c436a;
    color: #ecf3ff;
}

.schedule-create .form-control-dark:focus {
    background: #081227;
    color: #ecf3ff;
    border-color: #2bb8d6;
    box-shadow: none;
}

.schedule-info {
    background: #081227;
    border: 1px solid #223453;
    border-radius: 12px;
    padding: 16px;
}

.schedule-info ul {
    padding-left: 1rem;
    display: grid;
    gap: .45rem;
}
</style>
@endsection
