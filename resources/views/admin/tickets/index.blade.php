@extends('layouts.app')

@section('content')

<div class="container py-4">

<h3>🎫 Tickets de Soporte</h3>

<table class="table table-hover align-middle mt-3">

<tr class="table-dark">
    <th>Usuario</th>
    <th>Asunto</th>
    <th>Prioridad</th>
    <th>Estado</th>
    <th></th>
</tr>

@foreach($tickets as $t)
<tr>

<td>{{ $t->user->name }}</td>

<td>{{ $t->subject }}</td>

<td>
<span class="badge bg-{{ 
    $t->priority=='alta'?'danger':
    ($t->priority=='media'?'warning':'secondary')
}}">
{{ $t->priority }}
</span>
</td>

<td>
<span class="badge bg-{{
    $t->status=='cerrado'?'success':
    ($t->status=='en_proceso'?'warning':'secondary')
}}">
{{ $t->status }}
</span>
</td>

<td>
<a href="{{ route('admin.tickets.show', $t->id) }}"
   class="btn btn-sm btn-dark">
Gestionar
</a>
</td>

</tr>
@endforeach

</table>

</div>

@endsection