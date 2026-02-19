@extends('layouts.app')

@section('title','Admin | Deskcir')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="mb-5">
        <h2 class="fw-bold"> Panel Administrador</h2>
        <p class="text-muted mb-0">
            Gestión general, ventas, usuarios y operaciones
        </p>
    </div>

    {{-- ================= KPI ================= --}}
    <div class="row g-4 mb-5">

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">

                    <h6 class="text-uppercase text-muted mb-2">Ventas hoy</h6>

                    <h3 class="fw-bold mb-1">$12,450</h3>

                    <small class="text-success d-block mt-2">
                        ▲ +8% vs ayer
                    </small>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">

                    <h6 class="text-uppercase text-muted mb-2">Pedidos</h6>

                    <h3 class="fw-bold mb-1">23</h3>

                    <small class="text-warning d-block mt-2">
                        5 en proceso
                    </small>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">

                    <h6 class="text-uppercase text-muted mb-2">Clientes</h6>

                    <h3 class="fw-bold mb-1">142</h3>

                    <small class="text-info d-block mt-2">
                        Activos este mes
                    </small>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">

                    <h6 class="text-uppercase text-muted mb-2">
                        Tickets abiertos
                    </h6>

                    <h3 class="fw-bold mb-1">4</h3>

                    <small class="text-danger d-block mt-2">
                        Requieren atención
                    </small>

                </div>
            </div>
        </div>

    </div>

    {{-- ============== MÓDULOS ============== --}}
    <div class="row g-4 mb-5">

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body p-4">

                    <h6 class="mb-2">👥 Usuarios</h6>

                    <p class="text-muted small mb-3">
                        Administrar cuentas y roles
                    </p>

                    <a href="/admin/users"
                       class="btn btn-dark btn-sm w-100 py-2">
                        Gestionar
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body p-4">

                    <h6 class="mb-2">📦 Productos</h6>

                    <p class="text-muted small mb-3">
                        Inventario y precios
                    </p>

                    <a href="/admin/products"
                       class="btn btn-dark btn-sm w-100 py-2">
                        Gestionar
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body p-4">

                    <h6 class="mb-2">🎫 Tickets</h6>

                    <p class="text-muted small mb-3">
                        Soporte y seguimiento
                    </p>

                    <a href="/admin/tickets"
                       class="btn btn-dark btn-sm w-100 py-2">
                        Ver tickets
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body p-4">

                    <h6 class="mb-2">📈 Reportes</h6>

                    <p class="text-muted small mb-3">
                        Estadísticas y métricas
                    </p>

                    <a href="/admin/reports"
                       class="btn btn-dark btn-sm w-100 py-2">
                        Ver reportes
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- ============== VENTAS ============== --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">🧾 Historial de ventas</h5>

                        <a href="/admin/sales"
                           class="btn btn-dark btn-sm">
                            Ver todas
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
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