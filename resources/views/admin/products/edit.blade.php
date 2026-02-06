@extends('layouts.app')

@section('content')
<div class="container">
<h3>Editar producto</h3>

{{-- 👉 MOSTRAR ERRORES --}}
@if($errors->any())
<div class="alert alert-danger">
<ul>
@foreach($errors->all() as $e)
<li>{{ $e }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST"
      action="{{ route('admin.products.update', $product->id) }}"
      enctype="multipart/form-data">

@csrf
@method('PUT')

<input class="form-control mb-2"
       name="name"
       value="{{ $product->name }}">

<textarea class="form-control mb-2"
          name="description">{{ $product->description }}</textarea>

<input class="form-control mb-2"
       name="price"
       value="{{ $product->price }}">

<input class="form-control mb-2"
       name="stock"
       value="{{ $product->stock }}">

@if($product->image)
<img src="{{ asset('storage/'.$product->image) }}"
     style="max-width:120px">
@endif

<input type="file" name="image" class="form-control mt-2">

<button class="btn btn-warning mt-3">Actualizar</button>

</form>
</div>
@endsection