@extends('layouts.app')

@section('content')

<div class="container py-4">

<h4>{{ $ticket->subject }}</h4>

<form method="POST"
action="{{ route('admin.tickets.status',$ticket->id) }}">
@csrf

<div class="row">

<div class="col">
<select name="status" class="form-control input-pro">
<option>abierto</option>
<option>en_proceso</option>
<option>cerrado</option>
</select>
</div>

<div class="col">
<select name="priority" class="form-control input-pro">
<option>baja</option>
<option>media</option>
<option>alta</option>
</select>
</div>

</div>

<button class="btn btn-warning mt-2">
Actualizar
</button>

</form>

<x-chat
:ticket="$ticket"
action="{{ route('admin.tickets.reply',$ticket->id) }}" />

</div>

@endsection