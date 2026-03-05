@extends('layouts.app')

@section('title', 'Solicitar soporte | Deskcir')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Solicitar soporte</h3>
            <p class="text-muted mb-0">Crea un ticket para que el equipo tecnico atienda tu caso.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="/support" class="btn btn-outline-deskcir py-2">
                <- Regresar
            </a>
            <a href="/support" class="btn btn-outline-secondary py-2">
                Ver mis tickets
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong class="d-block mb-2">No se pudo crear el ticket:</strong>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="/support" enctype="multipart/form-data" class="card shadow-sm border-0">
                @csrf

                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Detalle del problema</h5>

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">Asunto</label>
                        <input id="subject"
                               name="subject"
                               class="form-control"
                               value="{{ old('subject') }}"
                               placeholder="Ej: Mi laptop no enciende"
                               maxlength="120"
                               required>
                        <small class="text-muted">Escribe un titulo breve para identificar tu ticket.</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Describe tu problema</label>
                        <textarea id="description"
                                  class="form-control"
                                  name="description"
                                  rows="6"
                                  placeholder="Cuentanos con detalle que sucede, desde cuando pasa y que intentaste."
                                  required>{{ old('description') }}</textarea>
                        <small class="text-muted">Entre mas contexto des, mas rapido podemos ayudarte.</small>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label fw-semibold">Prioridad</label>
                        <select id="priority" name="priority" class="form-select" required>
                            <option value="baja" {{ old('priority')=='baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('priority','media')=='media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('priority')=='alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <label for="attachments" class="form-label fw-semibold mb-0">Fotos o videos (opcional)</label>
                            <small class="text-muted">Maximo 5 archivos, 20MB c/u</small>
                        </div>

                        <input id="attachments"
                               type="file"
                               name="attachments[]"
                               class="form-control"
                               accept="image/*,video/*"
                               multiple>

                        <div id="attachmentPreview" class="ticket-media-preview mt-3"></div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-deskcir py-2">
                            Crear ticket
                        </button>
                        <a href="/support" class="btn btn-outline-secondary py-2">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Guia rapida</h6>

                    <div class="mb-3">
                        <span class="badge bg-success">Baja</span>
                        <p class="text-muted small mb-0 mt-2">Dudas o ajustes menores sin bloqueo de trabajo.</p>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-warning text-dark">Media</span>
                        <p class="text-muted small mb-0 mt-2">Falla parcial que afecta uso normal del equipo.</p>
                    </div>

                    <div>
                        <span class="badge bg-danger">Alta</span>
                        <p class="text-muted small mb-0 mt-2">No puedes trabajar o el equipo no responde.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('attachments');
    const preview = document.getElementById('attachmentPreview');

    if (!input || !preview) return;

    input.addEventListener('change', function () {
        preview.innerHTML = '';

        Array.from(input.files || []).forEach((file) => {
            const item = document.createElement('div');
            item.className = 'ticket-media-item';

            const isImage = file.type.startsWith('image/');
            const isVideo = file.type.startsWith('video/');

            if (isImage || isVideo) {
                const media = document.createElement(isVideo ? 'video' : 'img');
                media.src = URL.createObjectURL(file);
                media.className = 'ticket-media-thumb';

                if (isVideo) {
                    media.controls = true;
                }

                item.appendChild(media);
            }

            const meta = document.createElement('div');
            meta.className = 'ticket-media-meta';
            meta.textContent = file.name;
            item.appendChild(meta);

            preview.appendChild(item);
        });
    });
})();
</script>

<style>
.ticket-media-preview{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(140px,1fr));
    gap:10px;
}

.ticket-media-item{
    border:1px solid #dbe1ea;
    border-radius:12px;
    padding:8px;
    background:#f8fafc;
}

.ticket-media-thumb{
    width:100%;
    height:100px;
    object-fit:cover;
    border-radius:8px;
    display:block;
    background:#0b1220;
}

.ticket-media-meta{
    margin-top:6px;
    font-size:12px;
    color:#475569;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.dark .ticket-media-item{
    border-color:#253049;
    background:#0e1526;
}

.dark .ticket-media-meta{
    color:#cbd5e1;
}
</style>
@endpush
