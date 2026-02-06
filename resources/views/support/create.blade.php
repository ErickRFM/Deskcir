@extends('layouts.app')

@section('content')
<div class="container py-4">

<h3>📝 Solicitar soporte</h3>

<div class="card">
<div class="card-body">

<form method="POST" action="/support">
@csrf

<label>Asunto</label>
<input class="form-control mb-2"
name="subject"
placeholder="Ej: Mi laptop no enciende">

<label>Describe tu problema</label>
<textarea class="form-control mb-2"
name="description"
rows="4"
placeholder="Cuéntanos con detalle qué sucede..."></textarea>

<label>Prioridad</label>
<select name="priority" class="form-control mb-2">
<option>Baja</option>
<option>Media</option>
<option>Alta</option>
</select>

<button class="btn btn-warning w-100">
📨 Crear ticket
</button>

</form>

</div>
</div>

</div>
@endsection