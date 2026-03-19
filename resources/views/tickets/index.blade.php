@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Mis tickets</h3>
            <p class="text-muted mb-0">Consulta el seguimiento de tus solicitudes activas.</p>
        </div>

        <a href="/tickets/create" class="btn btn-deskcir">
            Nuevo
        </a>
    </div>

    <div class="card border-0 shadow-sm desk-table-card">
        <div class="table-responsive desk-table-wrap">
            <table class="table align-middle mb-0 desk-table">
                <thead>
                <tr>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td>{{ $t->subject }}</td>
                        <td>{{ $t->status }}</td>
                        <td class="text-end">
                            <a href="/tickets/{{ $t->id }}" class="btn btn-sm btn-deskcir">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="desk-table-empty">No tienes tickets registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
