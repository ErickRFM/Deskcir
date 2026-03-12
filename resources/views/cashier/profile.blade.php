@extends('layouts.app')

@section('title', 'Perfil de Caja | Deskcir')

@section('content')
<div class="container py-4 cashier-profile-page">
    <div class="cashier-profile-hero mb-4">
        <div class="cashier-profile-hero__bg"></div>
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 position-relative">
            <div>
                <span class="cashier-profile-kicker">PERFIL DE CAJA</span>
                <h2 class="fw-bold mb-1 d-flex align-items-center gap-2 mt-2">
                    <span class="material-symbols-outlined">point_of_sale</span>
                    {{ $user->name }}
                </h2>
                <p class="mb-0 text-light-emphasis">Panel dedicado para el rol de caja con acceso a cobros, recargas y control operativo.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="/checkout" class="btn btn-outline-light d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined">shopping_cart_checkout</span>
                    Abrir checkout
                </a>
                <a href="{{ route('cashier.dashboard') }}" class="btn btn-deskcir d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined">dashboard</span>
                    Volver a panel
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm cashier-profile-card h-100">
                <div class="card-body p-4 text-center">
                    <div class="cashier-profile-avatar mb-3">
                        <span class="material-symbols-outlined">badge</span>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('wallet.index') }}" class="btn btn-deskcir">Administrar billetera</a>
                        <a href="/admin/sales" class="btn btn-outline-deskcir">Ver ventas</a>
                    </div>
                    <div class="cashier-profile-meta mt-4">
                        <div>
                            <span>Rol</span>
                            <strong>{{ optional($user->role)->name ? ucfirst($user->role->name) : 'Caja' }}</strong>
                        </div>
                        <div>
                            <span>Cuenta desde</span>
                            <strong>{{ $user->created_at?->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Ventas hoy</p>
                            <h3 class="fw-bold mb-0">${{ number_format($todaySales, 2) }}</h3>
                            <p class="text-muted small mt-2 mb-0">Resumen de ingresos del dia.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Pedidos hoy</p>
                            <h3 class="fw-bold mb-0">{{ $todayOrders }}</h3>
                            <p class="text-muted small mt-2 mb-0">Pedidos registrados en el turno.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Pendientes</p>
                            <h3 class="fw-bold mb-0">{{ $pendingOrders }}</h3>
                            <p class="text-muted small mt-2 mb-0">Ordenes en camino o por confirmar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Recargas hoy</p>
                            <h3 class="fw-bold mb-0">${{ number_format($todayTopups, 2) }}</h3>
                            <p class="text-muted small mt-2 mb-0">Saldo acreditado en billeteras.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Checklist operativo</h5>
                            <div class="cashier-checklist">
                                <div class="cashier-checklist__item">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Verifica pagos y referencias antes de confirmar pedidos.
                                </div>
                                <div class="cashier-checklist__item">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Notifica al cliente cuando su recarga queda aplicada.
                                </div>
                                <div class="cashier-checklist__item">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Coordina pedidos pendientes con soporte y almacen.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
