@extends('layouts.app')

@section('title','Técnico | Deskcir')

@section('content')
<h2>Panel Técnico</h2>

<div class="row mt-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5>🎫 Tickets asignados</h5>
        <a href="/technician/tickets" class="btn btn-primary">Ver tickets</a>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5>📅 Agenda</h5>
        <a href="/technician/calendar" class="btn btn-success">Ver agenda</a>
      </div>
    </div>
  </div>
</div>
@endsection
