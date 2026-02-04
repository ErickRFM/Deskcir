@extends('layouts.app')

@section('content')
<h2>Solicitar Soporte Técnico</h2>

<form method="POST" action="/support">
@csrf

<input class="form-control mb-2" name="subject" placeholder="Asunto">

<textarea class="form-control mb-2" name="description"
placeholder="Describe el problema"></textarea>

<select name="priority" class="form-control mb-2">
<option>Baja</option>
<option>Media</option>
<option>Alta</option>
</select>

<button class="btn btn-primary">Crear ticket</button>
</form>
@endsection
