@extends('layouts.app')

@section('content')
<h2>Mis Citas</h2>

<table class="table table-bordered">
<thead>
<tr>
<th>#</th>
<th>Tipo</th>
<th>Fecha</th>
<th>Hora</th>
<th>Estado</th>
<th></th>
</tr>
</thead>

<tbody>
@foreach($appointments as $a)
<tr>
<td>{{ $a->id }}</td>
<td>{{ $a->type }}</td>
<td>{{ $a->date }}</td>
<td>{{ $a->time }}</td>
<td>{{ $a->status }}</td>
<td>
<a href="/appointments/{{ $a->id }}" class="btn btn-sm btn-primary">Ver</a>
</td>
</tr>
@endforeach
</tbody>
</table>
@endsection
