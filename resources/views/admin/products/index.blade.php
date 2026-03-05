@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mt-2">Gestión de Productos</h2>
        <div class="d-flex gap-3">
            
            <a href="javascript:history.back()" class="btn btn-outline-deskcir py-2">
                ← Regresar
            </a>

            <a href="{{ route('admin.products.create') }}" class="btn btn-deskcir py-2">
            Agregar producto
            </a>
        </div>

        
    </div>

    @if($products->isEmpty())
        <div class="alert alert-info text-center">
            No hay productos registrados aún.
        </div>
    @else

        <div class="table-responsive">
            <table class="table table-bordered align-middle mt-3">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($products as $product)
                        <tr>

                            <td class="py-3">
                                {{ $product->id }}
                            </td>

                            <td class="py-3">

                                {{-- GALERÍA DE IMÁGENES --}}
                                <div class="mb-2">
                                @foreach($product->images as $img)
                                    <img src="{{ asset('storage/'.$img->path) }}"
                                         class="img-fluid mb-1 me-1 rounded"
                                         style="max-width:100px">
                                @endforeach
                                </div>

                                <strong>{{ $product->name }}</strong>

                                <br>

                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit($product->description, 50) }}
                                </small>

                            </td>

                            <td class="py-3">
                                @if($product->category)
                                    <span class="badge bg-secondary">
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        Sin categoría
                                    </span>
                                @endif
                            </td>

                            <td class="py-3">
                                ${{ number_format($product->price, 2) }}
                            </td>

                            <td class="py-3">
                                @if($product->stock > 0)
                                    <span class="badge bg-success">
                                        {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Sin stock
                                    </span>
                                @endif
                            </td>

                            <td class="text-center py-3">

                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <span class="material-symbols-outlined">
                                        edit
                                    </span>
                                </a>

                                {{-- 🔥 ELIMINAR CON SWEETALERT --}}
                                <form id="delete{{$product->id}}"
                                      action="{{ route('admin.products.destroy', $product->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="eliminar({{$product->id}})">

                                        <span class="material-symbols-outlined">
                                            delete
                                        </span>

                                    </button>

                                </form>

                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    @endif

</div>

{{-- SCRIPT SWEETALERT ELIMINAR --}}
<script>
function eliminar(id){
    Swal.fire({
        title: '¿Eliminar producto?',
        text: "No podrás recuperarlo",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete'+id).submit();
        }
    })
}
</script>

{{-- ✅ MENSAJE DE ÉXITO --}}
@if(session('success'))
<script>
Swal.fire({
  icon: 'success',
  title: '¡Listo!',
  text: '{{ session('success') }}'
})
</script>
@endif

@endsection