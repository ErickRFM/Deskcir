@extends('layouts.app')

@section('title', 'Detalle de cita')

@section('content')
<div class="container py-4" style="max-width: 760px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            @php
                $typeMap = [
                    'visita_presencial' => 'Visita presencial',
                    'recepcion_equipo' => 'Recepcion de equipo',
                    'entrega_equipo' => 'Entrega de equipo',
                    'diagnostico_en_sitio' => 'Diagnostico en sitio',
                    'soporte_remoto' => 'Soporte remoto',
                ];
            @endphp
            <h4 class="fw-bold mb-3">Cita de soporte #{{ $appointment->id }}</h4>

            <p><strong>Ticket:</strong> #{{ $appointment->ticket_id }}</p>
            <p><strong>Tipo:</strong> {{ $typeMap[$appointment->type] ?? ucfirst(str_replace('_', ' ', $appointment->type)) }}</p>
            <p><strong>Fecha:</strong> {{ \Illuminate\Support\Carbon::parse($appointment->date)->format('d/m/Y') }}</p>
            <p><strong>Hora:</strong> {{ \Illuminate\Support\Carbon::parse($appointment->time)->format('H:i') }}</p>
            <p><strong>Estado:</strong> {{ str_replace('_', ' ', $appointment->status) }}</p>
            <p><strong>Tecnico:</strong> {{ optional($appointment->technician)->name ?? 'Sin asignar' }}</p>

            @if(!empty($appointment->notes))
                <p><strong>Notas:</strong> {{ $appointment->notes }}</p>
            @endif

            <a href="/appointments" class="btn btn-outline-secondary mt-2">Regresar</a>
        </div>
    </div>
</div>
@endsection
