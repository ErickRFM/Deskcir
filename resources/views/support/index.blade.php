@extends('layouts.app')

@section('content')

<div class="container py-4">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Centro de Soporte</h3>
        <p class="text-muted mb-0">
            Gestiona tus conversaciones con el equipo
        </p>
    </div>

    <a href="/support/create" class="btn btn-client">
        + Nuevo ticket
    </a>
</div>

{{-- BUSCADOR (visual por ahora) --}}
<div class="card p-3 mb-3">
    <input class="form-control input-pro"
    placeholder="Buscar ticket por asunto...">
</div>

{{-- TABLA PRO --}}
<div class="card">
<div class="table-responsive">

<table class="table align-middle mb-0">

<thead class="table-light">
<tr>
<th>Asunto</th>
<th>Prioridad</th>
<th>Estado</th>
<th>Último mensaje</th>
<th></th>
</tr>
</thead>

<tbody>

@forelse($tickets as $t)

<tr class="ticket-row">

<td>
<div class="fw-bold">{{ $t->subject }}</div>
<small class="text-muted">
#{{ $t->id }} • {{ $t->created_at->format('d M Y') }}
</small>
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
{{ ucfirst(str_replace('_',' ',$t->status)) }}
</span>
</td>

<td>
{{ optional($t->messages->last())->message ?? 'Sin mensajes' }}
</td>

<td class="text-end">
<a href="/support/{{ $t->id }}"
class="btn btn-sm btn-outline-dark">
Abrir
</a>
</td>

</tr>

@empty

<tr>
<td colspan="5" class="text-center py-5 text-muted">
No tienes tickets aún
</td>
</tr>

@endforelse

</tbody>

</table>

</div>
</div>
</div>

<style>
.ticket-row:hover{
    background:#f9fafb;
    cursor:pointer;
}
.dark .ticket-row:hover{
    background:#0e1424;
}
</style>

@endsection