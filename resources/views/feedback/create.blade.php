@extends('layouts.app')

@section('title', 'Quejas y sugerencias | Deskcir')

@section('content')
<div class="container py-4" style="max-width: 980px;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Quejas y sugerencias</h2>
            <p class="text-muted mb-0">Queremos mejorar el servicio, la tienda y la experiencia completa.</p>
        </div>
        <a href="{{ route('feedback.index') }}" class="btn btn-outline-deskcir">Ver historial</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('feedback.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="sugerencia" @selected(old('type') === 'sugerencia')>Sugerencia</option>
                                <option value="queja" @selected(old('type') === 'queja')>Queja</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Asunto</label>
                            <input name="subject" class="form-control" value="{{ old('subject') }}" maxlength="140" placeholder="Ej: Mejorar tiempos de respuesta en soporte" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mensaje</label>
                            <textarea name="message" rows="7" class="form-control" placeholder="Cuentanos tu comentario con el mayor detalle posible." required>{{ old('message') }}</textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="/client" class="btn btn-outline-deskcir" data-smart-back data-fallback="/client">Regresar</a>
                            <button class="btn btn-deskcir">Enviar comentario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Lo que puedes reportar</h5>
                    <ul class="mb-4 feedback-guide">
                        <li>Problemas en tienda, pagos o billetera.</li>
                        <li>Detalles visuales, textos o errores del sistema.</li>
                        <li>Ideas para mejorar soporte y seguimiento.</li>
                    </ul>
                    <div class="feedback-tip-card">
                        <strong class="d-block mb-2">Consejo</strong>
                        <p class="mb-0">Si tu comentario depende de una pantalla especifica, menciona la vista y el paso exacto donde ocurre.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection