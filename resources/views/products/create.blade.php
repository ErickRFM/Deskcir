@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Agregar producto</h3>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        <input class="form-control mb-2" name="name" placeholder="Nombre">
        <textarea class="form-control mb-2" name="description"></textarea>
        <input class="form-control mb-2" name="price" placeholder="Precio">
        <input class="form-control mb-2" name="stock" placeholder="Stock">
        <input class="form-control mb-2" name="category" placeholder="Categoría">
        <input class="form-control mb-2" name="image" placeholder="imagen.jpg">

        <button class="btn btn-warning">Guardar</button>
    </form>
</div>
@endsection
