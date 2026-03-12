@extends('layouts.app')

@section('title', 'Agregar producto')

@section('content')
<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold">Agregar producto</h3>

    <button onclick="history.back()" class="btn btn-outline-deskcir py-2" type="button">
        Regresar
    </button>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST"
      action="{{ route('admin.products.store') }}"
      enctype="multipart/form-data"
      class="card p-4 shadow-sm">

    @csrf

    <div class="mb-3">
        <label class="form-label fw-semibold">Nombre</label>
        <input class="form-control input-pro"
               name="name"
               value="{{ old('name') }}"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Descripcion</label>
        <textarea class="form-control input-pro"
                  name="description"
                  rows="3">{{ old('description') }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Precio</label>
            <input type="number"
                   step="0.01"
                   class="form-control input-pro"
                   name="price"
                   value="{{ old('price') }}"
                   required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Stock</label>
            <input type="number"
                   class="form-control input-pro"
                   name="stock"
                   value="{{ old('stock') }}"
                   required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Categoria</label>

        <select name="category_id" class="form-select input-pro" required>
            <option value="">Selecciona categoria</option>

            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Imagenes del producto</label>

        <input type="file"
               name="images[]"
               multiple
               class="form-control input-pro"
               id="imageInput">

        <div id="previewContainer" class="mt-3 d-flex gap-2 flex-wrap"></div>
    </div>

    <div class="mt-3">
        <button class="btn btn-deskcir py-2" type="submit">
            Guardar producto
        </button>
    </div>
</form>

</div>

<script>
const imageInput = document.getElementById('imageInput');
const previewContainer = document.getElementById('previewContainer');

if (imageInput && previewContainer) {
    imageInput.addEventListener('change', function (e) {
        previewContainer.innerHTML = '';

        [...e.target.files].forEach((file) => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = '120px';
            img.style.height = '120px';
            img.style.objectFit = 'cover';
            img.classList.add('border', 'rounded', 'p-1', 'shadow-sm');
            previewContainer.appendChild(img);
        });
    });
}
</script>
@endsection
