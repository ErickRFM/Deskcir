@extends('layouts.app')

@section('title', 'Tienda')

@section('content')

<div class="store-wrapper store-scope">

<div class="container-fluid px-4">

<div class="row">

    {{-- SIDEBAR FILTRO --}}
    <div class="col-lg-3 col-md-4 mb-4">

        <div class="store-filter">

            <h5 class="filter-title">
                <i class="bi bi-funnel"></i> Filtros
            </h5>

            <div class="filter-section">
                <label class="filter-label">Precio mínimo</label>
                <input type="number" class="form-control filter-input" placeholder="$0">
            </div>

            <div class="filter-section">
                <label class="filter-label">Precio máximo</label>
                <input type="number" class="form-control filter-input" placeholder="$50000">
            </div>

            <div class="filter-section">
                <label class="filter-label">Condición</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label">Nuevo</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label">Usado</label>
                </div>
            </div>

            <button class="btn btn-apply w-100 mt-3">
                <i class="bi bi-search"></i> Aplicar filtros
            </button>

        </div>

    </div>

    {{-- PRODUCTOS --}}
    <div class="col-lg-9 col-md-8">

        @forelse($categories as $category)

            <h5 class="category-title">{{ $category->name }}</h5>

            <div class="row g-4">

                @forelse($category->products as $product)

                <div class="col-lg-4 col-md-6">

                    <div class="product-card">

                        <div class="product-img">

                            @if($product->images->count() > 0)
                            <img src="{{ asset('storage/'.$product->images->first()->path) }}">
                            @else
                            <div class="no-img">Sin imagen</div>
                            @endif

                        </div>

                        <div class="product-body">

                            <h6 class="product-name">
                                {{ $product->name }}
                            </h6>

                            <p class="product-desc">
                                {{ Str::limit($product->description, 70) }}
                            </p>

                            <div class="product-footer">

                                <div>
                                    <span class="product-price">
                                        ${{ number_format($product->price,2) }}
                                    </span>
                                </div>

                                <div class="product-actions">

                                    <a href="/store/product/{{ $product->id }}"
                                    class="btn btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <form method="POST" action="/cart/add/{{ $product->id }}">
                                        @csrf
                                        <button class="btn btn-cart">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                @empty
                <p class="text-muted">No hay productos.</p>
                @endforelse

            </div>

        @empty
        <p>No hay categorías registradas.</p>
        @endforelse

    </div>

</div>

</div>

</div>

@endsection