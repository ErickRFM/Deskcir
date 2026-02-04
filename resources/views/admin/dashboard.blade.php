@extends('layouts.app')

@section('title','Admin | Deskcir')

@section('content')
<h2>Panel Administrador</h2>

<div class="row mt-4">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6>Usuarios</h6>
        <a href="/admin/users" class="btn btn-dark btn-sm">Gestionar</a>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6>Productos</h6>
        <a href="/admin/products" class="btn btn-dark btn-sm">Gestionar</a>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6>Tickets</h6>
        <a href="/admin/tickets" class="btn btn-dark btn-sm">Ver</a>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6>Reportes</h6>
        <a href="/admin/reports" class="btn btn-dark btn-sm">Ver</a>
      </div>
    </div>
  </div>
</div>
@endsection

