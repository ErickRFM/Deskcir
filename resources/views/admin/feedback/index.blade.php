@extends('layouts.app')

@section('title', 'Feedback | Admin Deskcir')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Quejas y sugerencias</h2>
            <p class="text-muted mb-0">Seguimiento administrativo de comentarios del sistema.</p>
        </div>
        <a href="/admin/dashboard" class="btn btn-outline-deskcir" data-smart-back data-fallback="/admin/dashboard">Regresar</a>
    </div>

    <form class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end p-4">
            <div class="col-md-4">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select">
                    <option value="">Todos</option>
                    <option value="queja" @selected(request('type') === 'queja')>Quejas</option>
                    <option value="sugerencia" @selected(request('type') === 'sugerencia')>Sugerencias</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="nuevo" @selected(request('status') === 'nuevo')>Nuevo</option>
                    <option value="en_revision" @selected(request('status') === 'en_revision')>En revision</option>
                    <option value="resuelto" @selected(request('status') === 'resuelto')>Resuelto</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-deskcir flex-fill">Filtrar</button>
                <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-deskcir flex-fill">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Tipo</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th>Actualizar</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($feedback as $item)
                    <tr>
                        <td>{{ $item->user->name ?? 'Sin usuario' }}</td>
                        <td class="text-capitalize">{{ $item->type }}</td>
                        <td>
                            <strong>{{ $item->subject }}</strong>
                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->message, 120) }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $item->status) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.feedback.update', $item) }}" class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm">
                                    <option value="nuevo" @selected($item->status === 'nuevo')>Nuevo</option>
                                    <option value="en_revision" @selected($item->status === 'en_revision')>En revision</option>
                                    <option value="resuelto" @selected($item->status === 'resuelto')>Resuelto</option>
                                </select>
                                <button class="btn btn-sm btn-deskcir">Guardar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay comentarios registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $feedback->links() }}</div>
    </div>
</div>
@endsection