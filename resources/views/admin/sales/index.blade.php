@extends('layouts.app')

@section('title','Ventas | Deskcir')

@section('content')

<div class="container py-4">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3 class="fw-bold mb-1">Estatus de Ventas</h3>
        <p class="text-muted mb-0">
            Gestion de pedidos y estado de entregas
        </p>
    </div>

    <a href="{{ url('/admin/dashboard') }}" class="btn btn-outline-deskcir py-2">
        Regresar
    </a>

</div>

{{-- TABLA --}}
<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">
<table class="table align-middle">

<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Cliente</th>
    <th>Metodo</th>
    <th>Pago</th>
    <th>Total</th>
    <th>Estado</th>
    <th>Fecha</th>
    <th class="text-center">Acciones</th>
</tr>
</thead>

<tbody>

@forelse($orders as $o)

<tr>

<td>{{ $o->id }}</td>

<td>
    {{ $o->user->name ?? 'Invitado' }}
    <br>
    <small class="text-muted">
        {{ $o->user->email ?? '' }}
    </small>
</td>

<td>
<span class="badge bg-secondary">
    {{ ucfirst($o->payment_method) }}
</span>
</td>

<td>
    @if($o->payment)
        <form method="POST" action="{{ route('admin.payments.update', $o->payment->id) }}">
            @csrf
            @method('PATCH')
            <select name="status" class="form-select form-select-sm mb-1">
                <option value="pending" @if($o->payment->status == 'pending') selected @endif>Pendiente</option>
                <option value="paid" @if($o->payment->status == 'paid') selected @endif>Pagado</option>
                <option value="failed" @if($o->payment->status == 'failed') selected @endif>Fallido</option>
                <option value="cancelled" @if($o->payment->status == 'cancelled') selected @endif>Cancelado</option>
            </select>
            <button class="btn btn-sm btn-outline-deskcir w-100">Actualizar</button>
        </form>
        @if($o->payment->reference)
            <small class="text-muted d-block mt-1">Ref: {{ $o->payment->reference }}</small>
        @endif
    @else
        <span class="text-muted small">Sin registro</span>
    @endif
</td>

<td class="fw-bold text-success">
    ${{ number_format($o->total,2) }}
</td>

<td>

<form method="POST" action="/admin/sales/{{$o->id}}/status">
@csrf

<select name="status"
        class="form-select form-select-sm mb-1">

<option value="pendiente"
    @if($o->status=='pendiente') selected @endif>
    Pendiente
</option>

<option value="en camino"
    @if($o->status=='en camino') selected @endif>
    En camino
</option>

<option value="entregado"
    @if($o->status=='entregado') selected @endif>
    Entregado
</option>

<option value="cancelado"
    @if($o->status=='cancelado') selected @endif>
    Cancelado
</option>

</select>

<button class="btn btn-sm btn-deskcir w-100">
Guardar
</button>

</form>

</td>

<td>
{{ $o->created_at->format('d M Y H:i') }}
</td>

<td class="text-center">

<button class="btn btn-sm btn-outline-primary">
Ver
</button>

</td>

</tr>

@empty

<tr>
<td colspan="8" class="text-center py-4">
    No hay ventas registradas
</td>
</tr>

@endforelse

</tbody>
</table>
</div>

{{-- PAGINACION --}}
<div class="mt-3">
{{ $orders->links() }}
</div>

</div>
</div>

</div>

@endsection




