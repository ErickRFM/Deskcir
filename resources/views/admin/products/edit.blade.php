@extends('layouts.app')

@section('title','Editar producto')

@section('content')

<div class="container py-4">

<div class="d-flex justify-content-between mb-3">
<h3>Editar producto</h3>

<button onclick="history.back()" class="btn btn-outline-secondary">
← Regresar
</button>
</div>

@if($errors->any())
<div class="alert alert-danger">
<ul>
@foreach($errors->all() as $e)
<li>{{ $e }}</li>
@endforeach
</ul>
</div>
@endif


{{-- 🔥 FORM PRINCIPAL --}}
<form method="POST"
action="{{ route('admin.products.update',$product->id) }}"
enctype="multipart/form-data"
class="card p-4">

@csrf
@method('PUT')

{{-- NOMBRE --}}
<div class="mb-3">
<label class="form-label">Nombre</label>
<input name="name"
class="form-control input-pro"
value="{{ old('name',$product->name) }}">
</div>

{{-- DESCRIPCIÓN --}}
<div class="mb-3">
<label class="form-label">Descripción</label>
<textarea name="description"
class="form-control input-pro">{{ old('description',$product->description) }}</textarea>
</div>


<div class="row">

<div class="col-md-6 mb-3">
<label>Precio</label>
<input type="number"
step="0.01"
name="price"
class="form-control input-pro"
value="{{ old('price',$product->price) }}">
</div>

<div class="col-md-6 mb-3">
<label>Stock</label>
<input type="number"
name="stock"
class="form-control input-pro"
value="{{ old('stock',$product->stock) }}">
</div>

</div>


{{-- CATEGORÍA --}}
<div class="mb-4">
<label>Categoría</label>

<select name="category_id" class="form-select input-pro">

<option value="">Selecciona categoría</option>

@foreach($categories as $cat)

<option value="{{ $cat->id }}"
{{ old('category_id',$product->category_id)==$cat->id?'selected':'' }}>

{{ $cat->name }}

</option>

@endforeach

</select>
</div>


{{-- 🔥 IMÁGENES ACTUALES (SIN FORM ANIDADO) --}}
<div class="mb-4">

<label class="form-label fw-bold mb-2">
Imágenes actuales
</label>

<div class="d-flex flex-wrap gap-3">

@foreach($product->images as $img)

<div class="position-relative">

<img src="{{ asset('storage/'.$img->path) }}"
style="width:140px;height:140px;object-fit:cover"
class="rounded border shadow-sm">

<button type="button"
onclick="eliminarImagen({{ $img->id }})"
class="btn btn-danger btn-sm position-absolute"
style="top:-8px;right:-8px;border-radius:50%">
✕
</button>

</div>

@endforeach

@if($product->images->isEmpty())
<p class="text-muted">
Este producto no tiene imágenes
</p>
@endif

</div>
</div>


{{-- SUBIR NUEVAS --}}
<div class="mb-4">

<label class="form-label fw-bold">
Agregar más imágenes
</label>

<input type="file"
name="images[]"
multiple
class="form-control input-pro mb-3"
id="imageInput">

<div id="previewContainer"
class="d-flex flex-wrap gap-2"></div>

</div>


{{-- BOTONES --}}
<div class="mt-4 pt-3 border-top text-center">

<div class="d-flex gap-3 justify-content-center">

<button type="submit" class="btn btn-warning px-5 py-2">
Guardar cambios
</button>

<a href="{{ route('admin.products.index') }}"
class="btn btn-secondary px-5 py-2">
Cancelar
</a>

</div>

</div>

</form>
</div>


{{-- 🔥 SCRIPTS --}}
<script>
function eliminarImagen(id){

fetch('/products/image/'+id,{
method:'POST',
headers:{
'X-CSRF-TOKEN':'{{ csrf_token() }}',
'X-HTTP-Method-Override':'DELETE'
}
})
.then(()=> location.reload())

}

imageInput.onchange = e => {

previewContainer.innerHTML=''

;[...e.target.files].forEach(f=>{

const img=document.createElement('img')

img.src=URL.createObjectURL(f)

img.style.width='120px'
img.style.height='120px'
img.style.objectFit='cover'

img.classList.add('border','rounded','shadow-sm')

previewContainer.appendChild(img)

})
}
</script>

@endsection