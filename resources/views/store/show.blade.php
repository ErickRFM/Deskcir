@extends('layouts.app')

@section('title', $product->name . ' | Deskcir')

@section('content')
@php
    $gallery = collect();

    if ($product->images && $product->images->count()) {
        $gallery = $product->images->map(fn($img) => asset('storage/' . ltrim($img->path, '/')));
    }

    if ($gallery->isEmpty() && !empty($product->image)) {
        $gallery = collect([asset('storage/' . ltrim($product->image, '/'))]);
    }

    $mainImage = $gallery->first();
@endphp

<div class="store-product-page">
    <div class="container py-4 py-lg-5">

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="small text-secondary-subtle">
                <a href="/store" class="text-decoration-none text-secondary-subtle">Inicio</a>
                <span class="mx-2">/</span>
                <span>{{ $product->category->name ?? 'Producto' }}</span>
                <span class="mx-2">/</span>
                <span class="text-light">{{ $product->name }}</span>
            </div>
            <a href="/store" class="btn btn-sm btn-outline-deskcir">Regresar a tienda</a>
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
                    <h1 class="product-title mb-2">{{ $product->name }}</h1>
                    <p class="product-subtitle mb-3">{{ \Illuminate\Support\Str::limit($product->description ?? 'Sin descripcion', 130) }}</p>

                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rating-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <span class="small text-secondary-subtle">4.6</span>
                        <span class="small text-secondary-subtle">(46 resenas)</span>
                    </div>

                    <div class="price-row mb-2">
                        <span class="product-price">${{ number_format($product->price, 2) }}</span>
                    </div>
                    <p class="small text-secondary-subtle mb-3">Hasta 3 MSI en compras seleccionadas.</p>

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
                            <label for="qty" class="small fw-semibold mb-0 text-light-emphasis">Cantidad</label>
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
                            ? asset('storage/' . ltrim($item->images->first()->path, '/'))
                            : (!empty($item->image) ? asset('storage/' . ltrim($item->image, '/')) : null);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <article class="related-card h-100">
                            <a href="/store/product/{{ $item->id }}" class="text-decoration-none text-light">
                                <div class="related-image-wrap">
                                    @if($relatedImage)
                                        <img src="{{ $relatedImage }}" class="related-image" alt="{{ $item->name }}">
                                    @else
                                        <div class="related-no-image">Sin imagen</div>
                                    @endif
                                </div>
                                <div class="p-2 p-md-3">
                                    <h6 class="related-name mb-1">{{ \Illuminate\Support\Str::limit($item->name, 42) }}</h6>
                                    <div class="fw-semibold text-info-emphasis">${{ number_format($item->price, 2) }}</div>
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
                    <p class="mb-0 text-secondary-emphasis">{{ $product->description ?: 'Sin descripcion disponible para este producto.' }}</p>
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

<style>
.store-product-page {
    --deskcir-cyan: #00a0bd;
    --deskcir-bg-1: #091123;
    --deskcir-bg-2: #0f1b34;
    --deskcir-border: #24395f;
    --deskcir-text: #e8f2ff;
    background: linear-gradient(180deg, #070f22 0%, #0a1430 100%);
    border: 1px solid #1a2e52;
    border-radius: 22px;
    padding: .35rem .45rem 1rem;
    box-shadow: 0 14px 36px rgba(2, 8, 23, 0.35);
}

.product-shell,
.content-card,
.related-card,
.empty-box {
    background: var(--deskcir-bg-2);
    border: 1px solid var(--deskcir-border);
    border-radius: 16px;
    box-shadow: none;
}

.product-shell {
    box-shadow: 0 20px 48px rgba(2, 8, 23, 0.42);
}

.product-shell,
.content-card {
    padding: 1.1rem;
}

.product-media-wrap {
    background: #081227;
    border: 1px solid #24395f;
    border-radius: 14px;
    min-height: 360px;
    display: grid;
    place-items: center;
    overflow: hidden;
}

.product-main-image {
    width: 100%;
    max-height: 440px;
    object-fit: contain;
    display: block;
}

.product-image-placeholder,
.related-no-image {
    color: #9db0d3;
    font-size: 0.95rem;
}

.thumbs-shell {
    display: grid;
    grid-template-columns: 36px minmax(0, 1fr) 36px;
    align-items: center;
    gap: .5rem;
}

.thumb-track {
    display: flex;
    gap: .55rem;
    overflow-x: auto;
    scrollbar-width: thin;
}

.thumb-btn {
    border: 1px solid #2a4066;
    background: #0a162d;
    border-radius: 10px;
    width: 82px;
    height: 62px;
    padding: 0;
    flex: 0 0 auto;
}

.thumb-btn.is-active {
    border-color: var(--deskcir-cyan);
}

.thumb-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 9px;
}

.thumb-nav {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    border: 1px solid #2b4269;
    background: #0a162d;
    color: #d6e6ff;
    line-height: 1;
}

.product-title {
    color: var(--deskcir-text);
    font-size: clamp(1.6rem, 2.3vw, 2.3rem);
    font-weight: 800;
    line-height: 1.15;
}

.product-subtitle { color: #bfd0ea; }

.rating-stars,
.stars {
    color: #f59e0b;
    letter-spacing: 0.06em;
}

.product-price {
    font-size: clamp(1.8rem, 2.4vw, 2.3rem);
    font-weight: 900;
    color: #f8fcff;
}

.qty-input {
    width: 92px;
    background: #081227;
    border-color: #2d4570;
    color: #f1f7ff;
}

.qty-input:focus {
    background: #081227;
    color: #f1f7ff;
    box-shadow: none;
    border-color: #31b9d8;
}

.btn-buy-now {
    background: #9a4f1d;
    border: 1px solid #8c4415;
    color: #fff;
}

.btn-buy-now:hover {
    background: #7e3f14;
    color: #fff;
}

.stock-pill .in-stock { color: #34d399; font-weight: 700; }
.stock-pill .out-stock { color: #fb7185; font-weight: 700; }

 .product-shell .text-secondary-subtle { color: #a8bfdc !important; }
.product-shell .text-light-emphasis { color: #d6e7ff !important; }

.delivery-note {
    border: 1px dashed #35507a;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    background: #081227;
    color: #c5d6f3;
    font-size: 0.94rem;
}

.section-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #e6f1ff;
    margin-bottom: 0.95rem;
}

.related-card {
    overflow: hidden;
}

.related-image-wrap {
    background: #081227;
    border-bottom: 1px solid #22395e;
    min-height: 144px;
    display: grid;
    place-items: center;
}

.related-image {
    width: 100%;
    height: 144px;
    object-fit: contain;
}

.related-name {
    min-height: 2.5rem;
    font-size: 0.92rem;
    font-weight: 700;
    color: #dbe8ff;
}

.empty-box {
    padding: 1rem;
    color: #9fb3d7;
}

.rating-list {
    padding-left: 0;
    margin: 0;
    list-style: none;
    display: grid;
    gap: 0.5rem;
    color: #bcd0ee;
}

@media (max-width: 991.98px) {
    .product-media-wrap { min-height: 280px; }
    .product-shell,
    .content-card { padding: 1rem; }
}
</style>

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
@endsection


