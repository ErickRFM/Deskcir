@extends('layouts.app')

@section('content')
<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h3 class="fw-bold mt-2">
Checklist Tecnico - Ticket #{{ $ticket->id }}
</h3>

<div class="d-flex gap-3">

<a href="{{ route('technician.tickets') }}" class="btn btn-outline-deskcir py-2">
Regresar
</a>

<a href="{{ route('technician.checklist.pdf',$ticket->id) }}"
class="btn btn-deskcir py-2">
Exportar PDF
</a>

</div>

</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm border-0">

<div class="card-body p-4">

<form method="POST"
action="{{ route('technician.checklist.save',$ticket->id) }}"
enctype="multipart/form-data">

@csrf

<div class="row g-4">

<div class="col-12">

<label class="form-check fw-semibold">

<input type="checkbox"
class="form-check-input me-2"
name="diagnostico"
{{ isset($checklist) && $checklist->diagnostico ? 'checked':'' }}>

Diagnostico realizado

</label>

<textarea
name="diagnostico_notes"
class="form-control mt-2"
rows="3"
placeholder="Describe hallazgos tecnicos...">{{ old('diagnostico_notes', $checklist->diagnostico_notes ?? '') }}</textarea>

</div>

<div class="col-12">

<label class="form-check fw-semibold">

<input type="checkbox"
class="form-check-input me-2"
name="reparacion"
{{ isset($checklist) && $checklist->reparacion ? 'checked':'' }}>

Reparacion aplicada

</label>

<textarea
name="reparacion_notes"
class="form-control mt-2"
rows="3"
placeholder="Acciones realizadas...">{{ old('reparacion_notes', $checklist->reparacion_notes ?? '') }}</textarea>

</div>

<div class="col-12">

<label class="form-check fw-semibold">

<input type="checkbox"
class="form-check-input me-2"
name="pruebas"
{{ isset($checklist) && $checklist->pruebas ? 'checked':'' }}>

Pruebas finales realizadas

</label>

<textarea
name="pruebas_notes"
class="form-control mt-2"
rows="3"
placeholder="Resultados de las pruebas...">{{ old('pruebas_notes', $checklist->pruebas_notes ?? '') }}</textarea>

</div>

<div class="col-md-6">

<label class="form-label fw-semibold">
Errores detectados
</label>

<textarea
name="errores"
class="form-control"
rows="4">{{ old('errores', $checklist->errores ?? '') }}</textarea>

</div>

<div class="col-md-6">

<label class="form-label fw-semibold">
Observaciones tecnicas
</label>

<textarea
name="observaciones"
class="form-control"
rows="4">{{ old('observaciones', $checklist->observaciones ?? '') }}</textarea>

</div>

<div class="col-12">

<label class="form-label fw-semibold">
Fotos del servicio
</label>

<input type="file"
name="fotos[]"
id="fotosInput"
multiple
accept="image/*"
class="form-control">

<small class="text-muted d-block mt-1">Al guardar, las fotos se almacenan en este ticket y quedan visibles abajo.</small>

<div id="newPhotoPreview" class="row g-2 mt-2"></div>

@if(isset($checklist) && $checklist->photos && $checklist->photos->count())
    <div class="mt-3">
        <h6 class="fw-semibold mb-2">Fotos guardadas ({{ $checklist->photos->count() }})</h6>
        <div class="row g-2">
            @foreach($checklist->photos as $photo)
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="checklist-photo-card">
                        <a href="{{ $photo->url }}" target="_blank" rel="noopener" class="d-block checklist-photo-thumb">
                            <img src="{{ $photo->url }}" alt="Foto servicio" class="img-fluid rounded">
                        </a>

                        <button type="button"
                            class="btn btn-sm btn-danger checklist-photo-delete js-confirm-action"
                            data-action="{{ route('technician.checklist.photo.delete', ['ticket' => $ticket->id, 'photo' => $photo->id]) }}"
                            data-confirm-title="Eliminar foto"
                            data-confirm-message="Esta accion no se puede deshacer."
                            data-confirm-button="Eliminar"
                            data-confirm-variant="danger">
                            Eliminar
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

</div>

<div class="col-md-6">

<label class="form-label fw-semibold">
Estado del ticket
</label>

<select name="status" class="form-select">

<option value="diagnostico"
{{ old('status', $ticket->status) == 'diagnostico' ? 'selected':'' }}>
Diagnostico
</option>

<option value="reparacion"
{{ old('status', $ticket->status) == 'reparacion' ? 'selected':'' }}>
En reparacion
</option>

<option value="finalizado"
{{ old('status', $ticket->status) == 'finalizado' ? 'selected':'' }}>
Finalizado
</option>

</select>

</div>


</div>


<div class="mt-4 d-flex gap-3">

<button type="submit" class="btn btn-deskcir px-4 py-2">
Guardar / Actualizar
</button>

<button type="button"
id="clearChecklistBtn"
class="btn btn-outline-danger px-4 py-2">
Limpiar todo
</button>

</div>

</form>

<form id="deletePhotoForm" method="POST" class="d-none">
    @csrf
</form>

</div>

</div>

</div>

<div id="deskcirActionModal" class="deskcir-modal" aria-hidden="true">
    <div class="deskcir-modal__backdrop" data-close-modal="true"></div>
    <div class="deskcir-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="deskcirModalTitle">
        <div class="deskcir-modal__icon deskcir-modal__icon--info" id="deskcirModalIcon">!</div>
        <h5 id="deskcirModalTitle" class="deskcir-modal__title">Confirmar accion</h5>
        <p id="deskcirModalMessage" class="deskcir-modal__text">Deseas continuar?</p>
        <div class="deskcir-modal__actions">
            <button type="button" class="btn btn-outline-deskcir btn-sm" id="deskcirModalCancel">Cancelar</button>
            <button type="button" class="btn btn-deskcir btn-sm" id="deskcirModalConfirm">Confirmar</button>
        </div>
    </div>
</div>

<script>
(function(){
    const fileInput = document.getElementById('fotosInput');
    const previewBox = document.getElementById('newPhotoPreview');
    const clearBtn = document.getElementById('clearChecklistBtn');

    const modal = document.getElementById('deskcirActionModal');
    const modalTitle = document.getElementById('deskcirModalTitle');
    const modalMessage = document.getElementById('deskcirModalMessage');
    const modalConfirm = document.getElementById('deskcirModalConfirm');
    const modalCancel = document.getElementById('deskcirModalCancel');
    const modalIcon = document.getElementById('deskcirModalIcon');
    const deleteButtons = Array.from(document.querySelectorAll('.js-confirm-action'));
    const deletePhotoForm = document.getElementById('deletePhotoForm');

    let onConfirmAction = null;

    function openModal(config) {
        if (!modal) return;

        modalTitle.textContent = config.title || 'Confirmar accion';
        modalMessage.textContent = config.message || 'Deseas continuar?';
        modalConfirm.textContent = config.confirmText || 'Confirmar';

        const isDanger = config.variant === 'danger';
        modalConfirm.classList.toggle('btn-danger', isDanger);
        modalConfirm.classList.toggle('btn-deskcir', !isDanger);
        modalIcon.classList.toggle('deskcir-modal__icon--danger', isDanger);
        modalIcon.classList.toggle('deskcir-modal__icon--info', !isDanger);

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        onConfirmAction = null;
    }

    if (modal) {
        modal.addEventListener('click', function(event){
            if (event.target.dataset.closeModal === 'true') {
                closeModal();
            }
        });
    }

    if (modalCancel) {
        modalCancel.addEventListener('click', closeModal);
    }

    if (modalConfirm) {
        modalConfirm.addEventListener('click', function(){
            if (typeof onConfirmAction === 'function') {
                onConfirmAction();
            }
            closeModal();
        });
    }

    document.addEventListener('keydown', function(event){
        if (event.key === 'Escape' && modal && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function(){
            openModal({
                title: 'Limpiar checklist',
                message: 'Se vaciaran textos, checks y fotos nuevas sin guardar.',
                confirmText: 'Limpiar',
                variant: 'danger',
            });

            onConfirmAction = function() {
                document.querySelectorAll("textarea").forEach(function(t){ t.value = ''; });
                document.querySelectorAll("input[type='checkbox']").forEach(function(c){ c.checked = false; });
                document.querySelectorAll("input[type='file']").forEach(function(f){ f.value = ''; });
                if (previewBox) previewBox.innerHTML = '';
            };
        });
    }

    deleteButtons.forEach(function(button){
        button.addEventListener('click', function(){
            if (!deletePhotoForm) return;

            openModal({
                title: button.dataset.confirmTitle || 'Eliminar foto',
                message: button.dataset.confirmMessage || 'Esta accion no se puede deshacer.',
                confirmText: button.dataset.confirmButton || 'Eliminar',
                variant: button.dataset.confirmVariant || 'danger',
            });

            onConfirmAction = function() {
                deletePhotoForm.setAttribute('action', button.dataset.action || '#');
                deletePhotoForm.submit();
            };
        });
    });

    if (!fileInput || !previewBox) return;

    fileInput.addEventListener('change', function(){
        previewBox.innerHTML = '';
        const files = Array.from(this.files || []);

        files.forEach(function(file){
            if (!file.type.startsWith('image/')) return;

            const col = document.createElement('div');
            col.className = 'col-6 col-md-3 col-lg-2';

            const img = document.createElement('img');
            img.className = 'img-fluid rounded checklist-photo-preview';
            img.alt = file.name;
            img.src = URL.createObjectURL(file);

            col.appendChild(img);
            previewBox.appendChild(col);
        });
    });
})();
</script>

<style>
.checklist-photo-thumb,
.checklist-photo-preview {
    border: 1px solid #2a3a5f;
    background: #0b1220;
}

.checklist-photo-thumb img,
.checklist-photo-preview {
    width: 100%;
    height: 110px;
    object-fit: cover;
}

.checklist-photo-card {
    position: relative;
}

.checklist-photo-delete {
    position: absolute;
    top: 6px;
    right: 6px;
}

.deskcir-modal {
    position: fixed;
    inset: 0;
    z-index: 1080;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease;
}

.deskcir-modal.is-open {
    opacity: 1;
    visibility: visible;
}

.deskcir-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(2, 8, 23, 0.55);
    backdrop-filter: blur(4px);
}

.deskcir-modal__dialog {
    position: relative;
    width: min(92vw, 420px);
    margin: 12vh auto 0;
    background: linear-gradient(165deg, #101c33 0%, #0f172a 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 14px;
    box-shadow: 0 20px 45px rgba(2, 8, 23, 0.55);
    color: #e2e8f0;
    padding: 20px 20px 16px;
}

.deskcir-modal__icon {
    width: 38px;
    height: 38px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    margin-bottom: 10px;
}

.deskcir-modal__icon--danger {
    background: rgba(239, 68, 68, 0.18);
    color: #fca5a5;
}

.deskcir-modal__icon--info {
    background: rgba(14, 165, 233, 0.18);
    color: #7dd3fc;
}

.deskcir-modal__title {
    margin-bottom: 4px;
    font-weight: 700;
}

.deskcir-modal__text {
    color: #cbd5e1;
    margin-bottom: 14px;
}

.deskcir-modal__actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
</style>
@endsection





