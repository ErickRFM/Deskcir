@extends('layouts.app')

@section('title', 'Tienda')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">🛒 Tienda Deskcir</h2>

    @forelse($categories as $category)
        <h4 class="mt-5 mb-3">{{ $category->name }}</h4>

        <div class="row">
            @forelse($category->products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm border-0">

                        {{-- Imagen del producto --}}
                        <div class="bg-light d-flex align-items-center justify-content-center"
                             style="height:180px;">

                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}"
                                     class="img-fluid"
                                     style="max-height:170px;">
                            @else
                                <span class="text-muted">Sin imagen</span>
                            @endif

                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold">{{ $product->name }}</h6>

                            @if($product->description)
                                <p class="small text-muted">
                                    {{ Str::limit($product->description, 60) }}
                                </p>
                            @endif

                            <div class="mt-auto">
                                <p class="fw-bold mb-2">
                                    ${{ number_format($product->price, 2) }}
                                </p>

                                <a href="/store/product/{{ $product->id }}"
                                   class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    Ver producto
                                </a>

                                <form method="POST" action="/cart/add/{{ $product->id }}">
                                    @csrf
                                    <button class="btn btn-warning btn-sm w-100">
                                        Agregar al carrito
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">No hay productos en esta categoría.</p>
            @endforelse
        </div>
    @empty
        <p class="text-center text-muted">No hay categorías registradas.</p>
    @endforelse
</div>
@endsection
