@extends('layouts.app')

@section('title','Cliente | Deskcir')

@section('content')
<h2>Bienvenido {{ auth()->user()->name }}</h2>

<div class="row mt-4">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h5>🛒 Tienda</h5>
        <p>Compra productos tecnológicos</p>
        <a href="/store" class="btn btn-primary">Ver tienda</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h5>🧑‍💻 Soporte</h5>
        <p>Solicita ayuda técnica</p>
        <a href="/support" class="btn btn-warning">Solicitar soporte</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h5>📄 Historial</h5>
        <p>Servicios y compras</p>
        <a href="/client/history" class="btn btn-secondary">Ver historial</a>
      </div>
    </div>
  </div>
</div>
@endsection
