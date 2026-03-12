@extends('layouts.app')

@section('title', 'Historial | Deskcir')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="fw-bold mb-1">Mi historial</h2>
            <p class="text-muted mb-0">Servicios, tickets y compras recientes</p>
        </div>

        <a href="/client" class="btn btn-outline-deskcir" data-smart-back data-fallback="/client">Regresar a mi cuenta</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Tickets</p>
                    <h4 class="mb-0">{{ $ticketCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Citas</p>
                    <h4 class="mb-0">{{ $appointmentCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Compras</p>
                    <h4 class="mb-0">{{ $orderCount }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-bold">Tickets de soporte</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
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
                    @forelse($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ ucfirst($ticket->priority ?? 'media') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td>
                        <td>{{ $ticket->created_at?->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="/support/{{ $ticket->id }}" class="btn btn-sm btn-outline-deskcir">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No tienes tickets registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-bold">Citas</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>Ticket</th>
                        <th>Tecnico</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>{{ ucfirst($appointment->type) }}</td>
                        <td>{{ $appointment->ticket?->subject ?? 'Sin ticket' }}</td>
                        <td>{{ $appointment->technician?->name ?? 'Sin asignar' }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</td>
                        <td>{{ $appointment->time }}</td>
                        <td>{{ ucfirst($appointment->status) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No tienes citas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Compras</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th># Orden</th>
                        <th>Metodo</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ ucfirst($order->payment_method) }}</td>
                        <td>${{ number_format((float) $order->total, 2) }}</td>
                        <td>{{ ucfirst($order->status ?? 'pendiente') }}</td>
                        <td>{{ $order->created_at?->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Aun no tienes compras registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection