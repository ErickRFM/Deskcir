@extends('layouts.app')

@section('title', $product->name . ' | Deskcir')

@section('content')
@php
    $gallery = collect();

    if ($product->images && $product->images->count()) {
        $gallery = $product->images->map(fn($img) => $img->url);
    }

    if ($gallery->isEmpty() && !empty($product->image)) {
        $gallery = collect([$product->image_url]);
    }

    $mainImage = $gallery->first();
@endphp

<div class="product-detail-page">
    <div class="container py-4 py-lg-5">

        <div class="detail-breadcrumb mb-3">
            <a href="/store" class="breadcrumb-link">Inicio</a>
            <span>/</span>
            <span>{{ $product->category->name ?? 'Producto' }}</span>
            <span>/</span>
            <span class="breadcrumb-current">{{ $product->name }}</span>
        </div>

        <section class="product-shell mb-4">
            <div class="row g-4 g-xl-5 align-items-start">
                <div class="col-lg-6">
                    <div class="product-media-wrap">
                        @if($mainImage)
                            <img id="productMainImage" src="{{ $mainImage }}" alt="{{ $product->name }}" class="product-main-image">
                        @else
                            <div class="product-image-placeholder">Sin imagen disponible</div>
                        @endif
                    </div>

                    @if($gallery->count() > 0)
                        <div class="thumbs-shell mt-3">
                            <button type="button" class="thumb-nav" id="thumbPrev" aria-label="Anterior">&lsaquo;</button>
                            <div class="thumb-track" id="thumbTrack">
                                @foreach($gallery as $index => $src)
                                    <button type="button" class="thumb-btn {{ $index === 0 ? 'is-active' : '' }}" data-src="{{ $src }}" aria-label="Imagen {{ $index + 1 }}">
                                        <img src="{{ $src }}" alt="Miniatura {{ $index + 1 }}" class="thumb-image">
                                    </button>
                                @endforeach
                            </div>
                            <button type="button" class="thumb-nav" id="thumbNext" aria-label="Siguiente">&rsaquo;</button>
                        </div>
                    @endif
                </div>

                <div class="col-lg-6">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 flex-wrap">
                        <h1 class="product-title mb-0">{{ $product->name }}</h1>
                        <a href="/store" class="btn btn-sm btn-outline-deskcir">Regresar a tienda</a>
                    </div>

                    <p class="product-subtitle mb-3">{{ \Illuminate\Support\Str::limit($product->description ?? 'Sin descripcion', 130) }}</p>

                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rating-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <span class="small product-muted">4.6</span>
                        <span class="small product-muted">(46 resenas)</span>
                    </div>

                    <div class="price-row mb-1">
                        <span class="product-price">${{ number_format($product->price, 2) }}</span>
                    </div>
                    <p class="small product-muted mb-3">Hasta 3 MSI en compras seleccionadas.</p>

                    <div class="stock-pill mb-3">
                        @if((int)($product->stock ?? 0) > 0)
                            <span class="in-stock">Stock disponible: {{ (int) $product->stock }}</span>
                        @else
                            <span class="out-stock">Producto sin stock por ahora</span>
                        @endif
                    </div>

                    <form method="POST" action="/cart/add/{{ $product->id }}" class="product-buy-form mb-2">
                        @csrf
                        <div class="d-flex gap-2 align-items-center flex-wrap mb-3">
                            <label for="qty" class="small fw-semibold mb-0 product-label">Cantidad</label>
                            <input id="qty" type="number" name="qty" min="1" max="99" value="1" class="form-control qty-input">
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-deskcir px-4" type="submit">Agregar al carrito</button>
                            <button class="btn btn-buy-now px-4" type="submit" name="buy_now" value="1">Comprar ahora</button>
                        </div>
                    </form>

                    <div class="delivery-note mt-3">
                        <strong>Envio y devolucion</strong>
                        <p class="mb-0">Devolucion gratis dentro de 30 dias y soporte postventa Deskcir.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="section-title mb-0">Articulos relacionados</h4>
            </div>

            <div class="row g-3">
                @forelse($relatedProducts as $item)
                    @php
                        $relatedImage = $item->images->count()
                            ? $item->images->first()->url
                            : (!empty($item->image) ? $item->image_url : null);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <article class="related-card h-100">
                            <a href="/store/product/{{ $item->id }}" class="text-decoration-none related-link">
                                <div class="related-image-wrap">
                                    @if($relatedImage)
                                        <img src="{{ $relatedImage }}" class="related-image" alt="{{ $item->name }}">
                                    @else
                                        <div class="related-no-image">Sin imagen</div>
                                    @endif
                                </div>
                                <div class="p-2 p-md-3">
                                    <h6 class="related-name mb-1">{{ \Illuminate\Support\Str::limit($item->name, 42) }}</h6>
                                    <div class="fw-semibold related-price">${{ number_format($item->price, 2) }}</div>
                                </div>
                            </a>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-box">No hay articulos relacionados por ahora.</div>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="content-card h-100">
                    <h4 class="section-title">Descripcion de producto</h4>
                    <p class="mb-0 detail-text">{{ $product->description ?: 'Sin descripcion disponible para este producto.' }}</p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="content-card h-100">
                    <h4 class="section-title">Calificaciones</h4>
                    <ul class="rating-list mb-0">
                        <li><span class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span> <span>Excelente</span></li>
                        <li><span class="stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span> <span>Muy bueno</span></li>
                        <li><span class="stars">&#9733;&#9733;&#9733;&#9734;&#9734;</span> <span>Bueno</span></li>
                        <li><span class="stars">&#9733;&#9733;&#9734;&#9734;&#9734;</span> <span>Regular</span></li>
                        <li><span class="stars">&#9733;&#9734;&#9734;&#9734;&#9734;</span> <span>Malo</span></li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const mainImage = document.getElementById('productMainImage');
    const thumbs = document.querySelectorAll('.thumb-btn');
    const track = document.getElementById('thumbTrack');
    const prev = document.getElementById('thumbPrev');
    const next = document.getElementById('thumbNext');

    if (mainImage && thumbs.length) {
        thumbs.forEach((btn) => {
            btn.addEventListener('click', () => {
                const src = btn.getAttribute('data-src');
                if (!src) return;
                mainImage.setAttribute('src', src);
                thumbs.forEach((item) => item.classList.remove('is-active'));
                btn.classList.add('is-active');
            });
        });
    }

    if (track && prev && next) {
        prev.addEventListener('click', () => track.scrollBy({ left: -180, behavior: 'smooth' }));
        next.addEventListener('click', () => track.scrollBy({ left: 180, behavior: 'smooth' }));
    }
})();
</script>
@endpush

