@extends('layouts.app')

@section('content')
<div class="container py-4">

<h3>📝 Solicitar soporte</h3>

{{-- MOSTRAR ERRORES --}}
@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
<div class="card-body">

<form method="POST" action="/support">
@csrf

<label>Asunto</label>
<input class="form-control mb-2"
name="subject"
value="{{ old('subject') }}"
placeholder="Ej: Mi laptop no enciende"
required>

<label>Describe tu problema</label>
<textarea class="form-control mb-2"
name="description"
rows="4"
placeholder="Cuéntanos con detalle qué sucede..."
required>{{ old('description') }}</textarea>

<label>Prioridad</label>
<select name="priority" class="form-control mb-2" required>
<option value="baja" {{ old('priority')=='baja'?'selected':'' }}>Baja</option>
<option value="media" {{ old('priority')=='media'?'selected':'' }}>Media</option>
<option value="alta" {{ old('priority')=='alta'?'selected':'' }}>Alta</option>
</select>

<button class="btn btn-warning w-100">
📨 Crear ticket
</button>

</form>

</div>
</div>

</div>
@endsection