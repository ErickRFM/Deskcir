@extends('layouts.app')

@section('content')

<h3>Mis tickets</h3>

<a href="/tickets/create" class="btn btn-warning mb-3">
Nuevo
</a>

<table class="table">
<tr>
<th>Asunto</th>
<th>Estado</th>
<th></th>
</tr>

@foreach($tickets as $t)
<tr>
<td>{{ $t->subject }}</td>

<td>{{ $t->status }}</td>

<td>
<a href="/tickets/{{ $t->id }}"
   class="btn btn-sm btn-dark">
Ver
</a>
</td>

</tr>
@endforeach

</table>

@endsection