@extends('layouts.app')

@section('title','Tickets | Técnico')

@section('content')

<div class="container py-4">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">🎫 Mis Tickets</h3>
        <p class="text-muted mb-0">
            Gestiona incidencias asignadas
        </p>
    </div>

    <a href="/technician" class="btn btn-outline-secondary">
        ← Volver al panel
    </a>
</div>

{{-- FILTROS --}}
<div class="card p-3 mb-4">
    <div class="row g-2">

        <div class="col-md-3">
            <select class="form-select">
                <option>Todos</option>
                <option>Alta</option>
                <option>Media</option>
                <option>Baja</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select">
                <option>Todos</option>
                <option>Pendiente</option>
                <option>En proceso</option>
                <option>Cerrado</option>
            </select>
        </div>

        <div class="col-md-6">
            <input class="form-control"
                   placeholder="Buscar por cliente o asunto">
        </div>

    </div>
</div>

{{-- LISTA DE TICKETS --}}
<div class="card">

<table class="table align-middle mb-0">

<thead>
<tr>
    <th>Cliente</th>
    <th>Asunto</th>
    <th>Prioridad</th>
    <th>Estado</th>
    <th>Actualizado</th>
    <th></th>
</tr>
</thead>

<tbody>

@forelse($tickets as $t)

<tr>

<td>
<strong>{{ $t->user->name }}</strong>
</td>

<td>
{{ $t->subject }}
</td>

<td>
<span class="badge bg-{{
$t->priority=='alta'?'danger':
($t->priority=='media'?'warning':'secondary')
}}">
{{ ucfirst($t->priority) }}
</span>
</td>

<td>
<span class="badge bg-{{
$t->status=='cerrado'?'success':
($t->status=='en_proceso'?'warning':'secondary')
}}">
{{ str_replace('_',' ',$t->status) }}
</span>
</td>

<td>
{{ $t->updated_at->diffForHumans() }}
</td>

<td class="text-end">
<a href="/technician/tickets/{{ $t->id }}"
   class="btn btn-sm btn-primary">
    Atender
</a>
</td>

</tr>

@empty

<tr>
<td colspan="6" class="text-center text-muted py-4">
No tienes tickets asignados
</td>
</tr>

@endforelse

</tbody>
</table>

</div>

</div>

@endsection