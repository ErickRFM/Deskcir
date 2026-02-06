@extends('layouts.app')

@section('content')

<div class="container py-4">

<h4>{{ $ticket->subject }}</h4>

<p>De: <strong>{{ $ticket->user->name }}</strong></p>

<p>
Estado actual:
<span class="badge bg-{{
    $ticket->status=='cerrado'?'success':
    ($ticket->status=='en_proceso'?'warning':'secondary')
}}">
{{ $ticket->status }}
</span>
</p>

<p>{{ $ticket->description }}</p>

<hr>

{{-- 👉 FORM ACTUALIZAR ESTADO / PRIORIDAD / TÉCNICO --}}
<form method="POST"
 action="{{ route('admin.tickets.status', $ticket->id) }}">
@csrf

<div class="row">

<div class="col">
<select name="status" class="form-control">
<option value="abierto"     {{ $ticket->status=='abierto'?'selected':'' }}>Abierto</option>
<option value="en_proceso" {{ $ticket->status=='en_proceso'?'selected':'' }}>En proceso</option>
<option value="cerrado"    {{ $ticket->status=='cerrado'?'selected':'' }}>Cerrado</option>
</select>
</div>

<div class="col">
<select name="priority" class="form-control">
<option value="baja"  {{ $ticket->priority=='baja'?'selected':'' }}>Baja</option>
<option value="media" {{ $ticket->priority=='media'?'selected':'' }}>Media</option>
<option value="alta"  {{ $ticket->priority=='alta'?'selected':'' }}>Alta</option>
</select>
</div>

<div class="col">
<select name="assigned_to" class="form-control">
<option value="">Asignar técnico</option>

@foreach($tecnicos as $tec)
<option value="{{ $tec->id }}"
    {{ $ticket->assigned_to==$tec->id?'selected':'' }}>
    {{ $tec->name }}
</option>
@endforeach

</select>
</div>

</div>

<button class="btn btn-warning mt-2">
Actualizar estado
</button>

</form>

<hr>

<h5>Conversación</h5>

@foreach($ticket->messages as $m)

<div class="card mb-2">
<div class="card-body">

<strong>{{ $m->user->name }}</strong>

<p>{{ $m->message }}</p>

@if($m->file)
<a href="{{ asset('storage/'.$m->file) }}">
Descargar archivo
</a>
@endif

</div>
</div>

@endforeach

<hr>

{{-- 👉 RESPONDER COMO SOPORTE --}}
<form method="POST"
 action="{{ route('admin.tickets.reply', $ticket->id) }}"
 enctype="multipart/form-data">
@csrf

<textarea name="message"
 class="form-control mb-2"></textarea>

<input type="file" name="file"
 class="form-control mb-2">

<button class="btn btn-primary">
Responder como soporte
</button>

</form>

</div>

@endsection