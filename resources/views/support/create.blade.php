@extends('layouts.app')

@section('title', 'Solicitar soporte | Deskcir')

@section('content')
@php
    $mode = old('support_mode', request('mode', 'general'));
    $isPresencial = $mode === 'presencial';
    $priorityValue = old('priority', $isPresencial ? 'alta' : 'media');
    $isGuest = !auth()->check();
    $redirectBack = '/support/create?restore_draft=1'.($isPresencial ? '&mode=presencial' : '');
@endphp

<div class="support-entry-page container py-4" data-support-auth="{{ auth()->check() ? '1' : '0' }}" data-support-restore="{{ request()->boolean('restore_draft') ? '1' : '0' }}">
    <section class="support-entry-hero card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <p class="support-entry-kicker mb-2">Centro de ayuda</p>
                    <h1 class="support-entry-title mb-3">Solicita soporte como mejor te convenga.</h1>
                    <p class="support-entry-subtitle mb-0">Puedes consultar primero a Deskcir AI, abrir un ticket normal o registrar una solicitud presencial para visita o recepcion de equipo.</p>
                </div>
                <div class="col-lg-5">
                    <div class="support-entry-actions">
                        <a href="{{ route('deskcir.ai', ['prompt' => 'Necesito ayuda para evaluar un problema antes de abrir un ticket.']) }}" class="support-entry-action is-ai">
                            <span class="material-symbols-outlined">auto_awesome</span>
                            <div>
                                <strong>Preguntar a la IA</strong>
                                <span>Recibe orientacion inmediata.</span>
                            </div>
                        </a>
                        <a href="/support/create?mode=presencial" class="support-entry-action is-field">
                            <span class="material-symbols-outlined">home_repair_service</span>
                            <div>
                                <strong>Soporte presencial</strong>
                                <span>Registra una visita o recepcion.</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">{{ $isPresencial ? 'Registrar soporte presencial' : 'Crear ticket de soporte' }}</h3>
            <p class="text-muted mb-0">{{ $isPresencial ? 'Esta solicitud se registrara con prioridad para coordinacion presencial.' : 'Crea un ticket para que el equipo tecnico atienda tu caso.' }}</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ auth()->check() ? '/support' : '/store' }}" class="btn btn-outline-deskcir py-2">Regresar</a>
            <a href="{{ route('deskcir.ai') }}" class="btn btn-outline-secondary py-2">Abrir Deskcir AI</a>
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

    @if($isGuest)
    <div class="alert alert-info border-0 shadow-sm support-guest-banner">
        <strong class="d-block mb-1">Puedes preparar tu ticket sin iniciar sesion.</strong>
        <span>Cuando lo envias, te pediremos registrarte o entrar para guardar el borrador y mandarlo sin perder donde te quedaste.</span>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="/support" enctype="multipart/form-data" class="card shadow-sm border-0" id="supportCreateForm">
                @csrf
                <input type="hidden" name="support_mode" value="{{ $isPresencial ? 'presencial' : 'general' }}">

                <div class="card-body p-4">
                    <div class="support-entry-toggle mb-4">
                        <a href="/support/create" class="support-entry-tab {{ !$isPresencial ? 'is-active' : '' }}">Ticket normal</a>
                        <a href="/support/create?mode=presencial" class="support-entry-tab {{ $isPresencial ? 'is-active' : '' }}">Presencial</a>
                        <a href="{{ route('deskcir.ai', ['prompt' => 'Ayudame a redactar un ticket de soporte con la informacion correcta.']) }}" class="support-entry-tab">Con IA</a>
                    </div>

                    <h5 class="fw-semibold mb-3">Detalle del problema</h5>

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">Asunto</label>
                        <input id="subject" name="subject" class="form-control" value="{{ old('subject', $isPresencial ? 'Necesito revision presencial de mi equipo' : '') }}" placeholder="Ej: Mi laptop no enciende" maxlength="120" required>
                        <small class="text-muted">Escribe un titulo breve para identificar tu ticket.</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Describe tu problema</label>
                        <textarea id="description" class="form-control" name="description" rows="6" placeholder="Cuentanos con detalle que sucede, desde cuando pasa y que intentaste." required>{{ old('description', $isPresencial ? 'Necesito atencion presencial. Equipo o servicio: ' : '') }}</textarea>
                        <small class="text-muted">Entre mas contexto des, mas rapido podemos ayudarte.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="priority" class="form-label fw-semibold">Prioridad</label>
                            <select id="priority" name="priority" class="form-select" required>
                                <option value="baja" {{ $priorityValue == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ $priorityValue == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ $priorityValue == 'alta' ? 'selected' : '' }}>Alta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="support-entry-tip h-100">
                                <strong>{{ $isPresencial ? 'Soporte presencial' : 'Ticket remoto o general' }}</strong>
                                <span>{{ $isPresencial ? 'La solicitud quedara marcada dentro del ticket para seguimiento de visita.' : 'Ideal para dudas, incidencias y seguimiento normal.' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 mt-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <label for="attachments" class="form-label fw-semibold mb-0">Fotos o videos (opcional)</label>
                            <small class="text-muted">Maximo 5 archivos, 20MB c/u</small>
                        </div>

                        <input id="attachments" type="file" name="attachments[]" class="form-control" accept="image/*,video/*" multiple>
                        <div id="attachmentPreview" class="ticket-media-preview mt-3"></div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-deskcir py-2">{{ $isPresencial ? 'Registrar soporte presencial' : 'Crear ticket' }}</button>
                        <a href="{{ auth()->check() ? '/support' : '/store' }}" class="btn btn-outline-secondary py-2">Cancelar</a>
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

                    <div class="mb-4">
                        <span class="badge bg-danger">Alta</span>
                        <p class="text-muted small mb-0 mt-2">No puedes trabajar o el equipo no responde.</p>
                    </div>

                    <div class="support-entry-side-card">
                        <strong class="d-block mb-2">Necesitas ayuda para redactarlo?</strong>
                        <p class="small text-muted mb-3">Abre Deskcir AI y pide que te ayude a resumir el caso antes de crear el ticket.</p>
                        <a href="{{ route('deskcir.ai', ['prompt' => 'Ayudame a redactar un ticket de soporte claro y profesional.']) }}" class="btn btn-outline-deskcir w-100">Abrir Deskcir AI</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isGuest)
<div class="modal fade" id="supportAuthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content deskcir-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Necesitas una cuenta para enviar el ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-3">Tu borrador se guardara y al volver a esta pantalla seguira donde te quedaste.</p>
                <div class="d-grid gap-2">
                    <a href="/login?redirect_to={{ urlencode($redirectBack) }}" class="btn btn-outline-deskcir">Ya tengo cuenta</a>
                    <a href="/register?redirect_to={{ urlencode($redirectBack) }}" class="btn btn-deskcir">Crear cuenta y continuar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('attachments');
    const preview = document.getElementById('attachmentPreview');
    const form = document.getElementById('supportCreateForm');
    const wrapper = document.querySelector('.support-entry-page');
    const isAuthenticated = wrapper?.dataset.supportAuth === '1';
    const shouldRestore = wrapper?.dataset.supportRestore === '1';
    const draftKey = 'deskcir-support-draft';

    const renderFiles = () => {
        if (!input || !preview) return;
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
                if (isVideo) media.controls = true;
                item.appendChild(media);
            }

            const meta = document.createElement('div');
            meta.className = 'ticket-media-meta';
            meta.textContent = file.name;
            item.appendChild(meta);
            preview.appendChild(item);
        });
    };

    const saveDraft = () => {
        if (!form) return;
        const payload = {
            subject: form.querySelector('[name="subject"]')?.value || '',
            description: form.querySelector('[name="description"]')?.value || '',
            priority: form.querySelector('[name="priority"]')?.value || 'media',
            support_mode: form.querySelector('[name="support_mode"]')?.value || 'general',
        };
        sessionStorage.setItem(draftKey, JSON.stringify(payload));
    };

    const restoreDraft = () => {
        if (!shouldRestore || !form) return;
        const raw = sessionStorage.getItem(draftKey);
        if (!raw) return;

        try {
            const payload = JSON.parse(raw);
            if (payload.subject) form.querySelector('[name="subject"]').value = payload.subject;
            if (payload.description) form.querySelector('[name="description"]').value = payload.description;
            if (payload.priority) form.querySelector('[name="priority"]').value = payload.priority;
            if (payload.support_mode) form.querySelector('[name="support_mode"]').value = payload.support_mode;
        } catch (error) {
            sessionStorage.removeItem(draftKey);
        }
    };

    if (input && preview) {
        input.addEventListener('change', renderFiles);
    }

    restoreDraft();

    if (!isAuthenticated && form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            saveDraft();
            const modalElement = document.getElementById('supportAuthModal');
            if (modalElement && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        });
    }

    if (isAuthenticated && shouldRestore && form) {
        form.addEventListener('submit', () => {
            sessionStorage.removeItem(draftKey);
        });
    }
})();
</script>
@endpush
