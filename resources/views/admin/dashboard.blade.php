@extends('layouts.app')

@section('title','Admin | Deskcir')

@section('content')
<div class="container-fluid py-4">
    <div class="deskcir-ai-inline-banner mb-4">
        <div>
            <p class="deskcir-ai__eyebrow mb-1">Deskcir AI</p>
            <h3 class="mb-1">Asistente para operaciones, tickets y respuesta rapida</h3>
            <p class="mb-0">Usalo para resumir casos, proponer respuestas y ordenar pasos tecnicos desde el dashboard.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('deskcir.ai') }}" class="btn btn-light">Abrir Deskcir AI</a>
            <a href="/admin/tickets" class="btn btn-outline-light">Ver tickets</a>
        </div>
    </div>

    <div class="mb-5">
        <h2 class="fw-bold">Panel Administrador</h2>
        <p class="text-muted mb-0">Gestion general, ventas, usuarios y operaciones</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body p-4"><h6 class="text-uppercase text-muted mb-2">Ventas hoy</h6><h3 class="fw-bold mb-1">$ {{ number_format($ventasHoy,2) }}</h3><small class="text-success d-block mt-2">+8% vs ayer</small></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body p-4"><h6 class="text-uppercase text-muted mb-2">Pedidos</h6><h3 class="fw-bold mb-1">{{ $pedidos }}</h3><small class="text-warning d-block mt-2">5 en proceso</small></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body p-4"><h6 class="text-uppercase text-muted mb-2">Clientes</h6><h3 class="fw-bold mb-1">{{ $clientes }}</h3><small class="text-info d-block mt-2">Activos este mes</small></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body p-4"><h6 class="text-uppercase text-muted mb-2">Tickets abiertos</h6><h3 class="fw-bold mb-1">{{ $ticketsAbiertos }}</h3><small class="text-danger d-block mt-2">Requieren atencion</small></div></div></div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3"><div class="card h-100"><div class="card-body p-4"><h6 class="mb-2">Usuarios</h6><p class="text-muted small mb-3">Administrar cuentas y roles</p><a href="/admin/users" class="btn btn-deskcir btn-sm w-100 py-2">Gestionar</a></div></div></div>
        <div class="col-md-3"><div class="card h-100"><div class="card-body p-4"><h6 class="mb-2">Productos</h6><p class="text-muted small mb-3">Inventario y precios</p><a href="/admin/products" class="btn btn-deskcir btn-sm w-100 py-2">Gestionar</a></div></div></div>
        <div class="col-md-3"><div class="card h-100"><div class="card-body p-4"><h6 class="mb-2">Tickets</h6><p class="text-muted small mb-3">Soporte y seguimiento</p><a href="/admin/tickets" class="btn btn-deskcir btn-sm w-100 py-2">Ver tickets</a></div></div></div>
        <div class="col-md-3"><div class="card h-100"><div class="card-body p-4"><h6 class="mb-2">Deskcir AI</h6><p class="text-muted small mb-3">Resumenes y respuestas para soporte</p><a href="{{ route('deskcir.ai') }}" class="btn btn-outline-deskcir btn-sm w-100 py-2">Abrir IA</a></div></div></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Historial de ventas</h5>
                        <a href="/admin/sales" class="btn btn-deskcir btn-sm">Ver todas</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Metodo</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\Order::with('user')->latest()->take(5)->get() as $o)
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td>{{ $o->user->name ?? 'Invitado' }}</td>
                                <td>{{ ucfirst($o->payment_method) }}</td>
                                <td>${{ number_format($o->total,2) }}</td>
                                <td>
                                    @if($o->status=='entregado')
                                        <span class="badge bg-success">Entregado</span>
                                    @elseif($o->status=='en camino')
                                        <span class="badge bg-warning text-dark">En camino</span>
                                    @elseif($o->status=='cancelado')
                                        <span class="badge bg-danger">Cancelado</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td>{{ $o->created_at->format('d M') }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
