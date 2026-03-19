@extends('layouts.app')

@section('title', 'Mis citas')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-3">Mis citas de soporte</h3>

    <div class="card shadow-sm border-0 desk-table-card">
        <div class="card-body p-0">
            <div class="table-responsive desk-table-wrap">
                <table class="table align-middle mb-0 desk-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Ticket</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($appointments as $a)
                        @php
                            $typeMap = [
                                'visita_presencial' => 'Visita presencial',
                                'recepcion_equipo' => 'Recepcion de equipo',
                                'entrega_equipo' => 'Entrega de equipo',
                                'diagnostico_en_sitio' => 'Diagnostico en sitio',
                                'soporte_remoto' => 'Soporte remoto',
                            ];
                        @endphp
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>#{{ $a->ticket_id }}</td>
                            <td>{{ $typeMap[$a->type] ?? ucfirst(str_replace('_', ' ', $a->type)) }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($a->date)->format('d/m/Y') }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($a->time)->format('H:i') }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $a->status) }}</td>
                            <td>
                                <a href="/appointments/{{ $a->id }}" class="btn btn-sm btn-outline-primary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="desk-table-empty">No tienes citas registradas</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
