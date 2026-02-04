<h3>{{ $ticket->subject }}</h3>
<p>Estado: {{ $ticket->status }}</p>

<hr>

@foreach($ticket->messages as $m)
<p><strong>{{ $m->user->name }}:</strong> {{ $m->message }}</p>
@endforeach

<form method="POST" action="/tickets/{{ $ticket->id }}/message">
@csrf
<textarea name="message" class="form-control"></textarea>
<button class="btn btn-secondary mt-2">Enviar</button>
</form>

@if($ticket->report)
<a href="/tickets/{{ $ticket->id }}/report" class="btn btn-success mt-3">
Ver informe final
</a>
@endif
