@extends('layouts.app')

@section('title','Agenda Técnico')

@section('content')

<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">📅 Agenda del Técnico</h3>
        <p class="text-muted">
            Gestión de visitas, horas y servicios programados
        </p>
    </div>

    <a href="/technician" class="btn btn-outline-light">
        ← Volver al panel
    </a>
</div>


{{-- RESUMEN --}}
<div class="row g-3 mb-4">

<div class="col-md-3">
<div class="card">
<div class="card-body">
<h6 class="text-muted">Hoy</h6>
<h3>{{ $hoy }}</h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card">
<div class="card-body">
<h6 class="text-muted">Esta semana</h6>
<h3>{{ $semana }}</h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card">
<div class="card-body">
<h6 class="text-muted">Pendientes</h6>
<h3>{{ $pendientes }}</h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card">
<div class="card-body">
<h6 class="text-muted">Completadas</h6>
<h3>{{ $completadas }}</h3>
</div>
</div>
</div>

</div>


{{-- TABLA AGENDA --}}
<div class="card">
<div class="card-body">

<h5 class="mb-3">Servicios programados</h5>

<table class="table align-middle">

<tr class="table-dark">
<th>Hora</th>
<th>Cliente</th>
<th>Dirección</th>
<th>Ticket</th>
<th>Estado</th>
<th></th>
</tr>

@forelse($citas as $c)

<tr>

<td>
<strong>{{ $c->date->format('H:i') }}</strong>
<br>
<small>{{ $c->date->format('d M Y') }}</small>
</td>

<td>
{{ $c->ticket->user->name }}
</td>

<td>
{{ $c->ticket->address ?? '—' }}
</td>

<td>
#{{ $c->ticket->id }}
</td>

<td>
<span class="badge bg-{{
$c->status=='completado'?'success':
($c->status=='en_proceso'?'warning':'secondary')
}}">
{{ $c->status }}
</span>
</td>

<td>
<a href="/technician/tickets/{{ $c->ticket->id }}"
class="btn btn-sm btn-primary">
Abrir
</a>
</td>

</tr>

@empty

<tr>
<td colspan="6" class="text-center py-4">
Sin citas programadas
</td>
</tr>

@endforelse

</table>

</div>
</div>

</div>

@endsection