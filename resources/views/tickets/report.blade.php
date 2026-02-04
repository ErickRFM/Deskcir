<h2>Informe de Servicio</h2>

<p><strong>Diagnóstico:</strong></p>
<p>{{ $ticket->report->diagnosis }}</p>

<p><strong>Acciones realizadas:</strong></p>
<p>{{ $ticket->report->actions_taken }}</p>

<p><strong>Recomendaciones:</strong></p>
<p>{{ $ticket->report->recommendations }}</p>

<p><em>Servicio finalizado el {{ $ticket->report->closed_at }}</em></p>
