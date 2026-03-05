@extends('layouts.app')

@section('title', 'Agenda tecnico')

@section('content')
<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">Agenda del tecnico</h3>
        <p class="text-muted">Gestion de visitas, horas y servicios programados</p>
    </div>

    <a href="/technician" class="btn btn-outline-light">Volver al panel</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body"><h6 class="text-muted">Hoy</h6><h3>{{ $hoy }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><h6 class="text-muted">Esta semana</h6><h3>{{ $semana }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><h6 class="text-muted">Pendientes</h6><h3>{{ $pendientes }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><h6 class="text-muted">Completadas</h6><h3>{{ $completadas }}</h3></div></div></div>
</div>

<div class="card">
<div class="card-body">

<h5 class="mb-3">Servicios programados</h5>

<table class="table align-middle">
<thead class="table-dark">
<tr>
<th>Hora</th>
<th>Cliente</th>
<th>Tipo</th>
<th>Ticket</th>
<th>Estado</th>
<th></th>
</tr>
</thead>
<tbody>
@forelse($citas as $c)
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
<td>
    <strong>{{ \Illuminate\Support\Carbon::parse($c->time)->format('H:i') }}</strong><br>
    <small>{{ \Illuminate\Support\Carbon::parse($c->date)->format('d M Y') }}</small>
</td>
<td>{{ optional($c->ticket->user)->name ?? 'Sin cliente' }}</td>
<td>{{ $typeMap[$c->type] ?? ucfirst(str_replace('_', ' ', $c->type)) }}</td>
<td>#{{ optional($c->ticket)->id }}</td>
<td>
    <span class="badge bg-{{
        $c->status === 'completada' ? 'success' :
        ($c->status === 'en_proceso' ? 'warning text-dark' : 'secondary')
    }}">
        {{ str_replace('_', ' ', $c->status) }}
    </span>
</td>
<td>
    @if($c->ticket_id)
    <a href="/technician/tickets/{{ $c->ticket_id }}" class="btn btn-sm btn-primary">Abrir</a>
    @endif
</td>
</tr>
@empty
<tr><td colspan="6" class="text-center py-4">Sin citas programadas</td></tr>
@endforelse
</tbody>
</table>

</div>
</div>

</div>
@endsection
