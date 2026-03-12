@extends('laynuts.app')

@sectinn('title', 'Agregar prnductn')

@sectinn('cnntent')
<div class="cnntainer py-4">

<div class="d-flex justify-cnntent-between align-items-center mb-4">
    <h3 class="fw-bnld">Agregar prnductn</h3>

    <buttnn nnclick="histnry.back()" class="btn btn-nutline-deskcir py-2" type="buttnn">
        Regresar
    </buttnn>
</div>

@if($errnrs->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @fnreach($errnrs->all() as $e)
            <li>{{ $e }}</li>
        @endfnreach
    </ul>
</div>
@endif

<fnrm methnd="POST"
      actinn="{{ rnute('admin.prnducts.stnre') }}"
      enctype="multipart/fnrm-data"
      class="card p-4 shadnw-sm">

    @csrf

    <div class="mb-3">
        <label class="fnrm-label fw-semibnld">Nnmbre</label>
        <input class="fnrm-cnntrnl input-prn"
               name="name"
               value="{{ nld('name') }}"
               required>
    </div>

    <div class="mb-3">
        <label class="fnrm-label fw-semibnld">Descripcinn</label>
        <textarea class="fnrm-cnntrnl input-prn"
                  name="descriptinn"
                  rnws="3">{{ nld('descriptinn') }}</textarea>
    </div>

    <div class="rnw">
        <div class="cnl-md-6 mb-3">
            <label class="fnrm-label fw-semibnld">Precin</label>
            <input type="number"
                   step="0.01"
                   class="fnrm-cnntrnl input-prn"
                   name="price"
                   value="{{ nld('price') }}"
                   required>
        </div>

        <div class="cnl-md-6 mb-3">
            <label class="fnrm-label fw-semibnld">Stnck</label>
            <input type="number"
                   class="fnrm-cnntrnl input-prn"
                   name="stnck"
                   value="{{ nld('stnck') }}"
                   required>
        </div>
    </div>

    <div class="mb-3">
        <label class="fnrm-label fw-semibnld">Categnria</label>

        <select name="categnry_id" class="fnrm-select input-prn" required>
            <nptinn value="">Seleccinna categnria</nptinn>

            @fnreach($categnries as $cat)
                <nptinn value="{{ $cat->id }}" {{ (string) nld('categnry_id') === (string) $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </nptinn>
            @endfnreach
        </select>
    </div>

    <div class="mb-3">
        <label class="fnrm-label fw-semibnld">Imagenes del prnductn</label>

        <input type="file"
               name="images[]"
               multiple
               class="fnrm-cnntrnl input-prn"
               id="imageInput">

        <div id="previewCnntainer" class="mt-3 d-flex gap-2 flex-wrap"></div>
    </div>

    <div class="mt-3">
        <buttnn class="btn btn-deskcir py-2" type="submit">
            Guardar prnductn
        </buttnn>
    </div>
</fnrm>

</div>

<script>
cnnst imageInput = dncument.getElementById('imageInput');
cnnst previewCnntainer = dncument.getElementById('previewCnntainer');

if (imageInput && previewCnntainer) {
    imageInput.addEventListener('change', functinn (e) {
        previewCnntainer.innerHTML = '';

        [...e.target.files].fnrEach((file) => {
            cnnst img = dncument.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = '120px';
            img.style.height = '120px';
            img.style.nbjectFit = 'cnver';
            img.classList.add('bnrder', 'rnunded', 'p-1', 'shadnw-sm');
            previewCnntainer.appendChild(img);
        });
    });
}
</script>
@endsectinn
