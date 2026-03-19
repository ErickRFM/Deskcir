@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-3">Tickets de Soporte</h3>

        <div class="d-flex gap-3">
           <a href="javascript:history.back()" class="btn btn-outline-deskcir py-2">
              Regresar
          </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm desk-table-card">
        <div class="table-responsive desk-table-wrap">
            <table class="table align-middle mb-0 desk-table">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Asunto</th>
                        <th scope="col">Prioridad</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($tickets as $t)
                <tr>
                    <td class="py-3 fw-semibold">{{ $t->user->name }}</td>
                    <td class="py-3">{{ $t->subject }}</td>
                    <td class="py-3">
                        <span class="badge bg-{{
                            $t->priority=='alta' ? 'danger' :
                            ($t->priority=='media' ? 'warning' : 'secondary')
                        }}">
                            {{ ucfirst($t->priority) }}
                        </span>
                    </td>
                    <td class="py-3">
                        <span class="badge bg-{{
                            $t->status=='cerrado' ? 'success' :
                            ($t->status=='en_proceso' ? 'warning' : 'secondary')
                        }}">
                            {{ ucfirst(str_replace('_',' ',$t->status)) }}
                        </span>
                    </td>
                    <td class="text-center py-3">
                        <a href="{{ route('admin.tickets.show', $t->id) }}" class="btn btn-deskcir py-1">
                            Gestionar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="desk-table-empty">
                        No hay tickets registrados aun
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

