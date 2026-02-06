@extends('layouts.app')

@section('content')
<div class="container py-4">

<h2 class="mb-3">🎧 Mis tickets de soporte</h2>

<a href="/support/create" class="btn btn-warning mb-3">
➕ Nuevo ticket
</a>

@if($tickets->isEmpty())
<div class="alert alert-info">
Aún no tienes tickets creados.
</div>
@else

<table class="table table-bordered align-middle">
<thead class="table-dark">
<tr>
<th>Asunto</th>
<th>Estado</th>
<th>Prioridad</th>
<th></th>
</tr>
</thead>

@foreach($tickets as $t)
<tr>
<td>
<strong>{{ $t->subject }}</strong>
</td>

<td>
<span class="badge bg-{{ 
$t->status == 'Abierto' ? 'success' :
($t->status == 'En proceso' ? 'warning' : 'secondary')
}}">
{{ $t->status }}
</span>
</td>

<td>
<span class="badge bg-{{ 
$t->priority == 'Alta' ? 'danger' :
($t->priority == 'Media' ? 'warning' : 'info')
}}">
{{ $t->priority }}
</span>
</td>

<td>
<a href="/support/{{ $t->id }}"
class="btn btn-sm btn-dark">
👁 Ver
</a>
</td>

</tr>
@endforeach

</table>

@endif

</div>
@endsection