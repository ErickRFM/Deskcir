@extends('layouts.app')

@section('title', 'Agregar producto')

@section('content')
<div class="container py-4">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Agregar producto</h3>

    <button onclick="history.back()" class="btn btn-outline-secondary">
        ← Regresar
    </button>
</div>

{{-- ERRORES --}}
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

{{-- NOMBRE --}}
<div class="mb-3">
<label class="form-label fw-semibold">Nombre</label>
<input class="form-control input-pro"
name="name"
value="{{ old('name') }}"
required>
</div>

{{-- DESCRIPCIÓN --}}
<div class="mb-3">
<label class="form-label fw-semibold">Descripción</label>
<textarea class="form-control input-pro"
name="description"
rows="3">{{ old('description') }}</textarea>
</div>

<div class="row">

{{-- PRECIO --}}
<div class="col-md-6 mb-3">
<label class="form-label fw-semibold">Precio</label>
<input type="number"
step="0.01"
class="form-control input-pro"
name="price"
value="{{ old('price') }}"
required>
</div>

{{-- STOCK --}}
<div class="col-md-6 mb-3">
<label class="form-label fw-semibold">Stock</label>
<input type="number"
class="form-control input-pro"
name="stock"
value="{{ old('stock') }}"
required>
</div>

</div>

{{-- 🔥 CATEGORÍAS FIJAS --}}
<div class="mb-3">
<label class="form-label fw-semibold">Categoría</label>

<select name="category_id" class="form-select input-pro" required>

<option value="">Selecciona categoría</option>

<option value="1">Gabinetes</option>
<option value="2">Laptops</option>
<option value="3">Accesorios</option>
<option value="4">Refacciones</option>

</select>
</div>

{{-- IMÁGENES --}}
<div class="mb-3">
<label class="form-label fw-semibold">
Imágenes del producto
</label>

<input type="file"
name="images[]"
multiple
class="form-control input-pro"
id="imageInput">

<div id="previewContainer"
class="mt-3 d-flex gap-2 flex-wrap">
</div>
</div>

{{-- BOTONES --}}
<div class="mt-3">
<button class="btn btn-warning px-4">
💾 Guardar producto
</button>

<a href="{{ route('admin.products.index') }}"
class="btn btn-secondary ms-2">
Cancelar
</a>
</div>

</form>
</div>

{{-- SCRIPTS --}}
<script>
document.getElementById('imageInput').onchange = function(e) {

const container = document.getElementById('previewContainer');
container.innerHTML = '';

[...e.target.files].forEach(file => {

const img = document.createElement('img');

img.src = URL.createObjectURL(file);

img.style.width = '120px';
img.style.height = '120px';
img.style.objectFit = 'cover';

img.classList.add('border','rounded','p-1','shadow-sm');

container.appendChild(img);
});
}
</script>

@endsection