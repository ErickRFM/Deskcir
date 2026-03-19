@extends('layouts.app')

@section('title', 'Caja | Catalogo y Cobro')

@section('content')
<div class="container py-4 cashier-dashboard">
    <div class="deskcir-ai-inline-banner mb-4">
        <div>
            <p class="deskcir-ai__eyebrow mb-1">Caja Deskcir</p>
            <h3 class="mb-1">Catalogo rapido y cobro en una sola vista</h3>
            <p class="mb-0">Busca productos, agrega nuevos y cobra pedidos sin salir del panel.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/checkout" class="btn btn-deskcir">Cobrar pedido</a>
            <a href="/cashier" class="btn btn-outline-light">Volver al panel</a>
            <a href="{{ route('cashier.profile') }}" class="btn btn-outline-light">Perfil de caja</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4 desk-table-card">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 desk-table-toolbar">
                        <div>
                            <h5 class="fw-bold mb-1">Catalogo rapido</h5>
                            <p class="text-muted mb-0">Encuentra productos y valida stock en segundos.</p>
                        </div>
                        <form method="GET" action="{{ route('cashier.catalog') }}" class="d-flex gap-2">
                            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Buscar por nombre o categoria">
                            <button class="btn btn-sm btn-outline-deskcir" type="submit">Buscar</button>
                        </form>
                    </div>

                    <div class="table-responsive desk-table-wrap">
                        <table class="table align-middle mb-0 desk-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoria</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($product->image_url)
                                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width:44px;height:44px;object-fit:cover;border-radius:10px;">
                                                @else
                                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:44px;height:44px;">—</div>
                                                @endif
                                                <div>
                                                    <strong class="d-block">{{ $product->name }}</strong>
                                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $product->category?->name ?? 'Sin categoria' }}</td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>
                                            @if($product->stock > 0)
                                                <span class="badge bg-success">{{ $product->stock }}</span>
                                            @else
                                                <span class="badge bg-danger">Sin stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="desk-table-empty">No hay productos que coincidan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Agregar producto rapido</h5>
                            <p class="text-muted mb-0">Carga el producto y sus imagenes desde caja.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('cashier.products.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input class="form-control input-pro" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descripcion</label>
                            <textarea class="form-control input-pro" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Precio</label>
                                <input type="number" step="0.01" class="form-control input-pro" name="price" value="{{ old('price') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Stock</label>
                                <input type="number" class="form-control input-pro" name="stock" value="{{ old('stock') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Categoria</label>
                            <select name="category_id" class="form-select input-pro" required>
                                <option value="">Selecciona categoria</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Imagenes del producto</label>
                            <input type="file" name="images[]" multiple class="form-control input-pro" id="cashierImageInput">
                            <div id="cashierPreview" class="mt-3 d-flex gap-2 flex-wrap"></div>
                        </div>
                        <button class="btn btn-deskcir w-100" type="submit">Guardar producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const cashierImageInput = document.getElementById('cashierImageInput');
const cashierPreview = document.getElementById('cashierPreview');

if (cashierImageInput && cashierPreview) {
    cashierImageInput.addEventListener('change', (e) => {
        cashierPreview.innerHTML = '';
        [...e.target.files].forEach((file) => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = '88px';
            img.style.height = '88px';
            img.style.objectFit = 'cover';
            img.classList.add('border', 'rounded', 'shadow-sm');
            cashierPreview.appendChild(img);
        });
    });
}
</script>

@if(session('success'))
<script>
Swal.fire({
  icon: 'success',
  title: 'Listo!',
  text: '{{ session('success') }}'
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
  icon: 'error',
  title: 'Error',
  text: '{{ session('error') }}'
});
</script>
@endif

@if($errors->any())
<script>
Swal.fire({
  icon: 'error',
  title: 'Error',
  text: '{{ $errors->first() }}'
});
</script>
@endif
@endsection

