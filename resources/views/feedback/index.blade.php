@extends('layouts.app')

@section('title', 'Historial de comentarios | Deskcir')

@section('content')
<div class="container py-4" style="max-width: 1100px;">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Mis comentarios</h2>
            <p class="text-muted mb-0">Consulta el estado de tus quejas y sugerencias.</p>
        </div>
        <a href="{{ route('feedback.create') }}" class="btn btn-deskcir">Nuevo comentario</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($feedback as $item)
                    <tr>
                        <td class="text-capitalize">{{ $item->type }}</td>
                        <td>
                            <strong>{{ $item->subject }}</strong>
                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->message, 110) }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $item->status) }}</span></td>
                        <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Aun no has enviado comentarios.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $feedback->links() }}</div>
    </div>
</div>
@endsection