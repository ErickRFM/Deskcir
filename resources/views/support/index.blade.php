@extends('layouts.app')

@section('content')
<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Centro de Soporte</h3>
        <p class="text-muted mb-0">Gestiona tus tickets activos</p>
    </div>

    <div class="d-flex gap-3">
        <a href="/dashboard" class="btn btn-outline-deskcir py-2">Regresar</a>
        <a href="/support/create" class="btn btn-client">Nuevo ticket</a>
    </div>
</div>

<div class="card p-3 mb-3 d-flex flex-row justify-content-between align-items-center">
    <span class="text-muted">Puedes archivar los tickets cerrados para limpiar tu bandeja.</span>

    <form method="POST" action="{{ route('support.archiveClosed') }}">
        @csrf
        <button class="btn btn-deskcir py-2">Archivar cerrados</button>
    </form>
</div>

<div class="card">
<div class="table-responsive">
<table class="table align-middle mb-0">
<thead class="table-light">
<tr>
<th>Asunto</th>
<th>Prioridad</th>
<th>Estado</th>
<th>Agenda</th>
<th></th>
</tr>
</thead>
<tbody>
@forelse($tickets->whereNull('archived_at') as $t)
@php
    $canSchedule = (int) ($t->technician_id ?: $t->assigned_to ?: 0) > 0;
@endphp
<tr>
<td>
<strong>{{ $t->subject }}</strong><br>
<small class="text-muted">#{{ $t->id }}</small>
</td>

<td><span class="badge bg-secondary">{{ ucfirst($t->priority) }}</span></td>

<td>
<span class="badge bg-{{ $t->status=='cerrado'?'success':($t->status=='en_proceso'?'warning':'secondary') }}">
{{ ucfirst(str_replace('_',' ',$t->status)) }}
</span>
</td>

<td>
    @if($canSchedule)
        <a href="/appointments/create?ticket_id={{ $t->id }}" class="btn btn-outline-deskcir btn-sm">Agendar</a>
        @if(($t->citas_programadas_count ?? 0) > 0)
            <span class="badge bg-info text-dark ms-1">{{ $t->citas_programadas_count }} cita(s)</span>
        @endif
    @else
        <span class="text-muted small">Sin tecnico asignado</span>
    @endif
</td>

<td class="text-end">
<a href="/support/{{ $t->id }}" class="btn btn-outline-deskcir py-1">Abrir</a>
</td>
</tr>
@empty
<tr>
<td colspan="5" class="text-center text-muted py-4">Sin tickets activos</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>

</div>
@endsection

