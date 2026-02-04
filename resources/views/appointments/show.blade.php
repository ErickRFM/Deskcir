<h2>Cita de Soporte</h2>

<p><strong>Tipo:</strong> {{ $appointment->type }}</p>
<p><strong>Fecha:</strong> {{ $appointment->date }}</p>
<p><strong>Hora:</strong> {{ $appointment->time }}</p>
<p><strong>Estado:</strong> {{ $appointment->status }}</p>

@if($appointment->status == 'Programada')
<div class="alert alert-info">
El técnico se conectará por soporte remoto.
</div>
@endif

