@extends('layouts.app')

@section('title','Editar producto')

@section('content')

<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
<h3>Editar producto</h3>

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
action="{{ route('admin.products.update',$product->id) }}"
enctype="multipart/form-data"
class="card p-4"
id="productEditForm">

@csrf
@method('PUT')

<div class="mb-3">
<label class="form-label">Nombre</label>
<input name="name"
class="form-control input-pro"
value="{{ old('name',$product->name) }}"
maxlength="255"
required>
</div>

<div class="mb-3">
<label class="form-label">Descripcion</label>
<textarea name="description"
class="form-control input-pro">{{ old('description',$product->description) }}</textarea>
</div>

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Precio</label>
<input type="number"
step="0.01"
min="0"
name="price"
class="form-control input-pro"
value="{{ old('price',$product->price) }}"
required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Stock</label>
<input type="number"
min="0"
name="stock"
class="form-control input-pro"
value="{{ old('stock',$product->stock) }}"
required>
</div>

</div>

<div class="mb-4">
<label class="form-label">Categoria</label>

<select name="category_id" class="form-select input-pro" required>

<option value="">Selecciona categoria</option>

@foreach($categories as $cat)

<option value="{{ $cat->id }}"
{{ old('category_id',$product->category_id)==$cat->id?'selected':'' }}>

{{ $cat->name }}

</option>

@endforeach

</select>
</div>

<div class="mb-4">

<label class="form-label fw-bold mb-2">
Imagenes actuales
</label>

<div class="d-flex flex-wrap gap-3">

@foreach($product->images as $img)

<div class="position-relative">

<img src="{{ $img->url }}"
style="width:140px;height:140px;object-fit:cover"
class="rounded border shadow-sm product-current-image"
onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">

<div class="rounded border shadow-sm d-none align-items-center justify-content-center text-center px-2 text-muted small bg-light"
style="width:140px;height:140px;">
Imagen no disponible
</div>

<button type="button"
onclick="eliminarImagen({{ $img->id }})"
class="btn btn-danger btn-sm position-absolute"
style="top:-8px;right:-8px;border-radius:50%"><span aria-hidden="true">&times;</span></button>

</div>

@endforeach

@if($product->images->isEmpty())
<p class="text-muted">
Este producto no tiene imagenes
</p>
@endif

</div>
</div>

<div class="mb-4">

<label class="form-label fw-bold">
Agregar mas imagenes
</label>

<input type="file"
name="images[]"
multiple
class="form-control input-pro mb-3"
id="imageInput"
accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

<div id="previewContainer"
class="d-flex flex-wrap gap-2"></div>

</div>

<div class="d-flex gap-3">

<button type="submit" class="btn btn-deskcir py-2" id="productEditSubmit">
Guardar cambios
</button>

</div>

</form>
</div>

<script>
const imageDeleteBase = @json(url('/admin/products/image'));
const imageInput = document.getElementById('imageInput');
const previewContainer = document.getElementById('previewContainer');
const productEditForm = document.getElementById('productEditForm');
const productEditSubmit = document.getElementById('productEditSubmit');

function validateProductImages(input) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const files = [...(input?.files ?? [])];

    for (const file of files) {
        if (!allowedTypes.includes(file.type)) {
            return 'Solo se permiten imagenes JPG, PNG o WEBP.';
        }

        if (file.size > (5 * 1024 * 1024)) {
            return `La imagen ${file.name} supera el maximo permitido de 5 MB.`;
        }
    }

    return null;
}

function validateEditForm() {
    const name = productEditForm?.querySelector('[name="name"]')?.value.trim() ?? '';
    const priceRaw = productEditForm?.querySelector('[name="price"]')?.value ?? '';
    const stockRaw = productEditForm?.querySelector('[name="stock"]')?.value ?? '';
    const price = Number(priceRaw);
    const stock = Number(stockRaw);
    const categoryId = productEditForm?.querySelector('[name="category_id"]')?.value ?? '';

    if (name.length < 2) {
        return 'Escribe un nombre valido para el producto.';
    }

    if (priceRaw === '' || !Number.isFinite(price) || price < 0) {
        return 'Ingresa un precio valido igual o mayor a 0.';
    }

    if (stockRaw === '' || !Number.isInteger(stock) || stock < 0) {
        return 'Ingresa un stock valido igual o mayor a 0.';
    }

    if (!categoryId) {
        return 'Selecciona una categoria para continuar.';
    }

    return validateProductImages(imageInput);
}

function lockEditSubmit() {
    if (!productEditSubmit) return;
    productEditSubmit.disabled = true;
    productEditSubmit.innerText = 'Guardando...';
}

function eliminarImagen(id){
    deskcirFire({
        title: 'Eliminar imagen?',
        text: 'La imagen se quitara del producto.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        deskcirFire({
            title: 'Eliminando imagen',
            text: 'Espera un momento.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        fetch(`${imageDeleteBase}/${id}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'DELETE'
            }
        })
        .then(async (response) => {
            const data = await response.json().catch(() => ({}));

            if (!response.ok || data.ok === false) {
                throw new Error(data.message || 'No se pudo eliminar la imagen.');
            }

            await deskcirFire({
                icon: 'success',
                title: 'Imagen eliminada',
                text: data.message || 'La imagen se elimino correctamente.',
            });

            location.reload();
        })
        .catch((error) => {
            deskcirFire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'No se pudo eliminar la imagen.',
            });
        });
    });
}

if (imageInput && previewContainer) {
    imageInput.addEventListener('change', (e) => {
        const imageError = validateProductImages(imageInput);

        if (imageError) {
            imageInput.value = '';
            previewContainer.innerHTML = '';
            deskcirFire({
              icon: 'error',
              title: 'Imagen invalida',
              text: imageError
            });
            return;
        }

        previewContainer.innerHTML = '';

        [...e.target.files].forEach((file) => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = '120px';
            img.style.height = '120px';
            img.style.objectFit = 'cover';
            img.classList.add('border','rounded','shadow-sm');
            previewContainer.appendChild(img);
        });
    });
}

if (productEditForm) {
    productEditForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const validationError = validateEditForm();

        if (validationError) {
            deskcirFire({
              icon: 'error',
              title: 'Revisa el formulario',
              text: validationError
            });
            return;
        }

        deskcirFire({
            icon: 'question',
            title: 'Guardar cambios?',
            text: 'Se actualizara la informacion del producto.',
            showCancelButton: true,
            confirmButtonText: 'Si, guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0f766e',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            lockEditSubmit();

            deskcirFire({
                title: 'Actualizando producto',
                text: 'Espera un momento mientras se guardan los cambios.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            productEditForm.submit();
        });
    });
}
</script>

@php
    $flashModal = null;

    if ($errors->any()) {
        $flashModal = ['icon' => 'error', 'title' => 'Error', 'text' => $errors->first()];
    } elseif (session('error')) {
        $flashModal = ['icon' => 'error', 'title' => 'Error', 'text' => session('error')];
    } elseif (session('success')) {
        $flashModal = ['icon' => 'success', 'title' => 'Listo!', 'text' => session('success')];
    }
@endphp

@if($flashModal)
<script>
deskcirShowFlash(@json($flashModal));
</script>
@endif
@endsection
