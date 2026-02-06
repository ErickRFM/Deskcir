@extends('layouts.app')

@section('content')

<h3>Nuevo Ticket</h3>

<form method="POST" action="/tickets">
@csrf

<input class="form-control mb-2"
       name="subject"
       placeholder="Asunto">

<textarea class="form-control mb-2"
          name="description"
          placeholder="Describe tu problema">
</textarea>

<button class="btn btn-warning">
    Crear ticket
</button>

</form>

@endsection