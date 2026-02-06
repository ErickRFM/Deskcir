@extends('layouts.app')

@section('content')
<div class="container py-4">

<h4>{{ $ticket->subject }}</h4>

<p class="text-muted">
{{ $ticket->description }}
</p>

<hr>

<h5>💬 Conversación</h5>

@foreach($ticket->messages as $m)

<div class="card mb-2">
<div class="card-body">

<div class="d-flex justify-content-between">
<strong>{{ $m->user->name }}</strong>

<small class="text-muted">
{{ $m->created_at->diffForHumans() }}
</small>
</div>

<p>{{ $m->message }}</p>

@if($m->file)
<a class="btn btn-sm btn-outline-primary"
href="{{ asset('storage/'.$m->file) }}">
📎 Descargar archivo
</a>
@endif

</div>
</div>

@endforeach

<hr>

<h5>✏ Responder</h5>

<form method="POST"
action="/support/{{ $ticket->id }}/message"
enctype="multipart/form-data">

@csrf

<textarea name="message"
class="form-control mb-2"
rows="3"
placeholder="Escribe tu mensaje..."></textarea>

<input type="file" name="file"
class="form-control mb-2">

<button class="btn btn-primary">
Enviar respuesta
</button>

</form>

</div>
@endsection