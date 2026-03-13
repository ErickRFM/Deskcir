@extends('layouts.app')

@section('title', 'Caja | Deskcir')

@section('content')
<div class="container py-4 cashier-dashboard">
    <div class="deskcir-ai-inline-banner mb-4">
        <div>
            <p class="deskcir-ai__eyebrow mb-1">Caja Deskcir</p>
            <h3 class="mb-1">Panel operativo de cobros, pedidos e inventario</h3>
            <p class="mb-0">Monitorea ventas del dia, estados de pedido y gestiona el catalogo desde un solo lugar.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/checkout" class="btn btn-deskcir">Cobrar pedido</a>
            <a href="{{ route('cashier.catalog') }}" class="btn btn-outline-light">Inventario</a>
            <a href="{{ route('cashier.profile') }}" class="btn btn-outline-light">Perfil de caja</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4"><div class="card h-100"><div class="card-body p-4"><div class="text-muted text-uppercase small mb-2">Ventas hoy</div><h3 class="fw-bold mb-0">${{ number_format($todaySales, 2) }}</h3></div></div></div>
        <div class="col-md-4"><div class="card h-100"><div class="card-body p-4"><div class="text-muted text-uppercase small mb-2">Pedidos hoy</div><h3 class="fw-bold mb-0">{{ $todayOrders }}</h3></div></div></div>
        <div class="col-md-4"><div class="card h-100"><div class="card-body p-4"><div class="text-muted text-uppercase small mb-2">Pendientes</div><h3 class="fw-bold mb-0">{{ $pendingOrders }}</h3></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Pedidos recientes</h5>
                        <a href="/admin/sales" class="btn btn-sm btn-outline-deskcir">Ver ventas</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>#</th><th>Cliente</th><th>Pago</th><th>Total</th><th>Estado</th></tr></thead>
                            <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'Invitado' }}</td>
                                    <td>{{ ucfirst($order->payment_method) }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>{{ ucfirst($order->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Sin pedidos recientes.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Inventario rapido</h5>
                        <a href="{{ route('cashier.catalog') }}" class="btn btn-sm btn-outline-deskcir">Gestionar</a>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('cashier.catalog') }}" class="btn btn-outline-deskcir">Ver catalogo</a>
                        <a href="{{ route('cashier.catalog') }}" class="btn btn-outline-deskcir">Agregar producto</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Mix de pagos</h5>
                    <div class="d-grid gap-2">
                        @foreach($paymentMix as $mix)
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded-3">
                                <strong class="text-capitalize">{{ str_replace('_', ' ', $mix->payment_method) }}</strong>
                                <span class="badge bg-light text-dark border">{{ $mix->total }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


