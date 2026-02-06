@extends('layouts.app')

@section('content')

<h4>{{ $ticket->subject }}</h4>

<p>{{ $ticket->description }}</p>

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

<form method="POST"
      action="/tickets/{{ $ticket->id }}/message"
      enctype="multipart/form-data">
@csrf

<textarea name="message"
          class="form-control mb-2">
</textarea>

<input type="file" name="file"
       class="form-control mb-2">

<button class="btn btn-primary">
Responder
</button>

</form>

@endsection