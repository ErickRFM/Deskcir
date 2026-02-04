@extends('layouts.app')

@section('content')
<h2>Agendar Cita de Soporte</h2>

<form method="POST" action="/appointments">
@csrf

<input type="hidden" name="ticket_id" value="{{ $ticketId }}">

<label>Fecha</label>
<input type="date" name="date" class="form-control mb-2" required>

<label>Hora</label>
<input type="time" name="time" class="form-control mb-2" required>

<label>Tipo de soporte</label>
<select name="type" class="form-control mb-2">
    <option>Soporte remoto</option>
    <option>Consulta técnica</option>
</select>

<button class="btn btn-success">Agendar</button>
</form>
@endsection
