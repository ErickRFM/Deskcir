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
      class="card p-4 shadow-sm"
      id="productCreateForm">

    @csrf

    <div class="mb-3">
        <label class="form-label fw-semibold">Nombre</label>
        <input class="form-control input-pro"
               name="name"
               value="{{ old('name') }}"
               maxlength="255"
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
                   min="0"
                   class="form-control input-pro"
                   name="price"
                   value="{{ old('price') }}"
                   required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Stock</label>
            <input type="number"
                   min="0"
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
               id="imageInput"
               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <div id="previewContainer" class="mt-3 d-flex gap-2 flex-wrap"></div>
    </div>

    <div class="mt-3">
        <button class="btn btn-deskcir py-2" type="submit" id="productCreateSubmit">
            Guardar producto
        </button>
    </div>
</form>

</div>

<script>
const imageInput = document.getElementById('imageInput');
const previewContainer = document.getElementById('previewContainer');
const productCreateForm = document.getElementById('productCreateForm');
const productCreateSubmit = document.getElementById('productCreateSubmit');

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

function validateCreateForm() {
    const name = productCreateForm?.querySelector('[name="name"]')?.value.trim() ?? '';
    const priceRaw = productCreateForm?.querySelector('[name="price"]')?.value ?? '';
    const stockRaw = productCreateForm?.querySelector('[name="stock"]')?.value ?? '';
    const price = Number(priceRaw);
    const stock = Number(stockRaw);
    const categoryId = productCreateForm?.querySelector('[name="category_id"]')?.value ?? '';

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

function lockCreateSubmit() {
    if (!productCreateSubmit) return;
    productCreateSubmit.disabled = true;
    productCreateSubmit.innerText = 'Guardando...';
}

if (imageInput && previewContainer) {
    imageInput.addEventListener('change', function (e) {
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
            img.classList.add('border', 'rounded', 'p-1', 'shadow-sm');
            previewContainer.appendChild(img);
        });
    });
}

if (productCreateForm) {
    productCreateForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const validationError = validateCreateForm();

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
            title: 'Guardar producto?',
            text: 'Se registrara el producto con la informacion capturada.',
            showCancelButton: true,
            confirmButtonText: 'Si, guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0f766e',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            lockCreateSubmit();

            deskcirFire({
                title: 'Guardando producto',
                text: 'Espera un momento mientras se sube la informacion.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            productCreateForm.submit();
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
