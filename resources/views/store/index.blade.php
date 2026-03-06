@extends('layouts.app')

@section('title','Tienda')

@section('content')

@php
$query = trim((string)($filters['q'] ?? ''));
$hasSearch = $query !== '';
$showFilters = $hasSearch;

$quickOptions = [
['key'=>'offers','label'=>'Ofertas','icon'=>'bi-tag-fill'],
['key'=>'sale','label'=>'Rebajas','icon'=>'bi-tags-fill'],
['key'=>'defective','label'=>'Defectuosos','icon'=>'bi-tools'],
['key'=>'popular','label'=>'Populares','icon'=>'bi-compass']
];

$statsCategories = $categories->count();
@endphp


<div class="store-wrapper store-scope">
<div class="container-fluid px-3 px-lg-4">


{{-- HERO SOLO SI NO HAY BUSQUEDA --}}
@if(!$hasSearch)

<div class="store-hero mb-4">
<div class="store-hero-bg"></div>

<div class="row g-3 align-items-center position-relative">

<div class="col-lg-8">
<span class="store-kicker mb-2">Deskcir Store</span>

<h1 class="store-main-title mb-2">
Tu setup ideal, mas rapido de encontrar.
</h1>

<p class="store-main-subtitle mb-0">
Catalogo optimizado con accesos rapidos para que encuentres equipos, perifericos y componentes sin friccion.
</p>
</div>

<div class="col-lg-4">
<div class="store-stats">

<div>
<span class="stats-number">{{ number_format($totalResults) }}</span>
<span class="stats-label">Productos visibles</span>
</div>

<div>
<span class="stats-number">{{ number_format($statsCategories) }}</span>
<span class="stats-label">Categorias</span>
</div>

<div>
<span class="stats-number">{{ number_format($activeFilters) }}</span>
<span class="stats-label">Filtros activos</span>
</div>

</div>
</div>

</div>
</div>

@endif



{{-- QUICK FILTERS SOLO SI NO HAY BUSQUEDA --}}
@if(!$hasSearch)

<div class="store-quick-tools mb-4">

<div class="quick-header mb-3">
<h3 class="quick-title mb-0">Explora rapido</h3>
<span class="quick-meta">{{ number_format($totalResults) }} resultados</span>
</div>

<div class="quick-actions">

@foreach($quickOptions as $option)

@php
$params=request()->query();
$params['quick']=$option['key'];
$url=url()->current().'?'.http_build_query($params);
@endphp

<a href="{{ $url }}" class="quick-pill {{ ($filters['quick'] ?? '') === $option['key'] ? 'is-active' : '' }}">
<i class="bi {{ $option['icon'] }}"></i>
{{ $option['label'] }}
</a>

@endforeach

</div>

</div>

@endif



{{-- POPULARES SOLO SI NO HAY BUSQUEDA --}}
@if(!$hasSearch && $popularProducts->isNotEmpty())

<section class="popular-articles mb-4">

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">

<h3 class="popular-title mb-0">
Articulos mas populares
</h3>

<span class="popular-note">
Segun historial de carrito y compras
</span>

</div>


<div class="row g-3">

@foreach($popularProducts as $product)

<div class="col-xl-2 col-lg-4 col-md-6">

<a href="/store/product/{{ $product->id }}" class="popular-card text-decoration-none">

<div class="popular-image">

@if($product->images->count())

<img src="{{ asset('storage/'.$product->images->first()->path) }}">

@else

<span>Sin imagen</span>

@endif

</div>

<div class="popular-body">
<h4>{{ Str::limit($product->name,42) }}</h4>
<p>${{ number_format($product->price,2) }}</p>
</div>

</a>

</div>

@endforeach

</div>

</section>

@endif



<div class="row g-4">


{{-- FILTROS --}}
@if($showFilters)

<div class="col-xl-3 col-lg-4">

<form method="GET" action="{{ url()->current() }}" class="store-filter-panel">

<input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">

<div class="filter-panel-head">

<h5 class="filter-title mb-0">
<i class="bi bi-sliders"></i>
Filtros
</h5>

@if($activeFilters>0)
<span class="filter-badge">{{ $activeFilters }}</span>
@endif

</div>


<div class="filter-section">
<label class="filter-label">Precio minimo</label>

<input type="number" name="min_price"
class="form-control filter-input"
value="{{ $filters['min_price'] ?? '' }}">
</div>


<div class="filter-section">
<label class="filter-label">Precio maximo</label>

<input type="number" name="max_price"
class="form-control filter-input"
value="{{ $filters['max_price'] ?? '' }}">
</div>


<div class="filter-section">

<label class="filter-label">Categoria</label>

<select name="category" class="form-select filter-input">

<option value="">Todas</option>

@foreach($categoryOptions as $cat)

<option value="{{ $cat->id }}"
{{ (int)($filters['category'] ?? 0)==$cat->id?'selected':'' }}>

{{ $cat->name }}

</option>

@endforeach

</select>

</div>


<div class="d-grid gap-2 mt-3">

<button class="btn btn-apply">
<i class="bi bi-search"></i>
Aplicar filtros
</button>

<a href="{{ url()->current().'?q='.urlencode($query) }}" class="btn btn-clear">
Limpiar filtros
</a>

</div>

</form>

</div>

@endif



{{-- RESULTADOS --}}
<div class="{{ $showFilters ? 'col-xl-9 col-lg-8' : 'col-12' }}">


@forelse($categories as $category)

<div class="d-flex justify-content-between align-items-center mb-3">

<h5 class="category-title mb-0">
{{ $category->name }}
</h5>

<span class="category-counter">
{{ $category->products->count() }} productos
</span>

</div>


<div class="row g-4 mb-4">

@foreach($category->products as $product)

<div class="col-xl-4 col-md-6">

<div class="product-card">

<div class="product-img">

@if($product->images->count())

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
{{ Str::limit($product->description,70) }}
</p>

<div class="product-footer">

<span class="product-price">
${{ number_format($product->price,2) }}
</span>

<div class="product-actions">

<a href="/store/product/{{ $product->id }}" class="btn btn-view">
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

@endforeach

</div>

@empty

<div class="store-empty-lg">

<i class="bi bi-search"></i>

<h5>No encontramos productos</h5>

<p>Prueba otra busqueda.</p>

<a href="/store" class="btn btn-apply">
Ver catalogo
</a>

</div>

@endforelse

</div>


</div>
</div>
</div>

@endsection