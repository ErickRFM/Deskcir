@extends('layouts.app')

@section('title','Mis Tickets')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Mis Tickets de Soporte</h2>
        <a href="/support" class="btn btn-primary">Solicitar soporte</a>
    </div>

    @if($tickets->isEmpty())
        <div class="alert alert-info">
            No tienes tickets registrados.
        </div>
    @else
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Asunto</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ $ticket->subject }}</td>
                    <td>{{ $ticket->priority }}</td>
                    <td>
                        <span class="badge bg-{{ 
                            $ticket->status == 'Nuevo' ? 'secondary' :
                            ($ticket->status == 'En proceso' ? 'warning' :
                            ($ticket->status == 'Finalizado' ? 'success' : 'danger'))
                        }}">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="/tickets/{{ $ticket->id }}" class="btn btn-sm btn-outline-primary">
                            Ver
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection
