<h2>Informe del Servicio</h2>

<p><strong>Ticket:</strong> #{{ $ticket->id }}</p>
<p><strong>Estado:</strong> {{ $ticket->status }}</p>

<hr>

<h4>Bitácora</h4>
<ul>
@foreach($logs as $log)
    <li>{{ $log->created_at }} - {{ $log->description }}</li>
@endforeach
</ul>

<a href="/reports/{{ $ticket->id }}/pdf" class="btn btn-danger">
Descargar PDF
</a>
