@extends('layouts.app')

@section('title', 'Agregar producto')

@section('content')
<div class="container py-4">

    <h3 class="mb-4"> Agregar producto</h3>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="description"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" name="price" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" class="form-control" name="stock" required>
        </div>

        {{-- SUBIR MÚLTIPLES IMÁGENES CON PREVIEW --}}
        <div class="mb-3">
            <label>Imágenes del producto</label>

            <input type="file" name="images[]" multiple class="form-control" id="imageInput">

            <div id="previewContainer" class="mt-2 d-flex gap-2 flex-wrap"></div>
        </div>

        <button class="btn btn-warning">
            💾 Guardar producto
        </button>

        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ms-2">
            Cancelar
        </a>
    </form>

</div>

<script>
document.getElementById('imageInput').onchange = function(e) {

    const container = document.getElementById('previewContainer');
    container.innerHTML = '';

    [...e.target.files].forEach(file => {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.maxWidth = '120px';
        img.classList.add('border','rounded','p-1');
        container.appendChild(img);
    });
}
</script>

@endsection
