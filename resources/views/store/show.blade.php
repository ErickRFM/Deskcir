@extends('layouts.app')

@section('content')
<div class="row">
<div class="col-md-6">
<img src="{{ $product->image }}" class="img-fluid">
</div>

<div class="col-md-6">
<h2>{{ $product->name }}</h2>
<p>{{ $product->description }}</p>
<h4>${{ $product->price }}</h4>

<form method="POST" action="/cart/add/{{ $product->id }}">
@csrf
<button class="btn btn-warning">Agregar al carrito</button>
</form>
</div>
</div>
@endsection
