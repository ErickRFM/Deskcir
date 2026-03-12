@extends('layouts.app')

@section('title','Admin | Deskcir')

@section('content')
<div class="container-fluid py-4 admin-dashboard-page">
    <div class="deskcir-ai-inline-banner admin-dashboard-hero mb-4">
        <div>
            <p class="deskcir-ai__eyebrow mb-1">Deskcir AI + Operaciones</p>
            <h3 class="mb-1">Centro de control para ventas, soporte y decisiones rapidas</h3>
            <p class="mb-0">Usa el dashboard para revisar metricas, abrir reportes y entrar directo a usuarios, productos, tickets y analitica.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('deskcir.ai') }}" class="btn btn-deskcir admin-dashboard-hero__btn">Abrir Deskcir AI</a>
            <a href="/admin/tickets" class="btn btn-outline-light admin-dashboard-hero__btn">Ver tickets</a>
            <a href="/admin/reports" class="btn btn-outline-light admin-dashboard-hero__btn">Ver reportes</a>
        </div>
    </div>

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Panel Administrador</h2>
        <p class="text-muted mb-0">Gestion general, ventas, usuarios, soporte y reportes</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 admin-stat-card">
                <div class="card-body p-4">
                    <div class="admin-stat-card__top">
                        <h6 class="text-uppercase text-muted mb-2">Ventas hoy</h6>
                        <span class="admin-stat-card__icon is-success"><span class="material-symbols-outlined">payments</span></span>
                    </div>
                    <h3 class="fw-bold mb-1">${{ number_format($ventasHoy, 2) }}</h3>
                    <small class="{{ $crecimiento >= 0 ? 'text-success' : 'text-danger' }} d-block mt-2">
                        {{ $crecimiento >= 0 ? '+' : '' }}{{ number_format($crecimiento, 2) }}% vs ayer
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 admin-stat-card">
                <div class="card-body p-4">
                    <div class="admin-stat-card__top">
                        <h6 class="text-uppercase text-muted mb-2">Ingresos del mes</h6>
                        <span class="admin-stat-card__icon is-info"><span class="material-symbols-outlined">monitoring</span></span>
                    </div>
                    <h3 class="fw-bold mb-1">${{ number_format($ventasMes, 2) }}</h3>
                    <small class="text-info d-block mt-2">Base para reportes y exportaciones</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 admin-stat-card">
                <div class="card-body p-4">
                    <div class="admin-stat-card__top">
                        <h6 class="text-uppercase text-muted mb-2">Clientes</h6>
                        <span class="admin-stat-card__icon is-primary"><span class="material-symbols-outlined">group</span></span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $clientes }}</h3>
                    <small class="text-primary d-block mt-2">Cuentas de cliente registradas</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 admin-stat-card">
                <div class="card-body p-4">
                    <div class="admin-stat-card__top">
                        <h6 class="text-uppercase text-muted mb-2">Tickets abiertos</h6>
                        <span class="admin-stat-card__icon is-danger"><span class="material-symbols-outlined">support_agent</span></span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $ticketsAbiertos }}</h3>
                    <small class="text-danger d-block mt-2">Abiertos o en proceso</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-xl-8">
            <div class="row g-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 admin-action-card">
                        <div class="card-body p-4">
                            <div class="admin-action-card__icon"><span class="material-symbols-outlined">manage_accounts</span></div>
                            <h6 class="mb-2">Usuarios</h6>
                            <p class="text-muted small mb-3">Administrar cuentas, roles y altas nuevas.</p>
                            <a href="/admin/users" class="btn btn-deskcir btn-sm w-100 py-2">Gestionar</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 admin-action-card">
                        <div class="card-body p-4">
                            <div class="admin-action-card__icon"><span class="material-symbols-outlined">inventory_2</span></div>
                            <h6 class="mb-2">Productos</h6>
                            <p class="text-muted small mb-3">Inventario, precios, imagenes y stock.</p>
                            <a href="/admin/products" class="btn btn-deskcir btn-sm w-100 py-2">Gestionar</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 admin-action-card">
                        <div class="card-body p-4">
                            <div class="admin-action-card__icon"><span class="material-symbols-outlined">confirmation_number</span></div>
                            <h6 class="mb-2">Tickets</h6>
                            <p class="text-muted small mb-3">Soporte, asignaciones y seguimiento tecnico.</p>
                            <a href="/admin/tickets" class="btn btn-deskcir btn-sm w-100 py-2">Ver tickets</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 admin-action-card">
                        <div class="card-body p-4">
                            <div class="admin-action-card__icon"><span class="material-symbols-outlined">analytics</span></div>
                            <h6 class="mb-2">Reportes</h6>
                            <p class="text-muted small mb-3">Resumen ejecutivo, productos, clientes y exportaciones.</p>
                            <a href="/admin/reports" class="btn btn-deskcir btn-sm w-100 py-2">Abrir reportes</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 admin-action-card">
                        <div class="card-body p-4">
                            <div class="admin-action-card__icon"><span class="material-symbols-outlined">receipt_long</span></div>
                            <h6 class="mb-2">Ventas</h6>
                            <p class="text-muted small mb-3">Pedidos, estados y flujo comercial reciente.</p>
                            <a href="/admin/sales" class="btn btn-deskcir btn-sm w-100 py-2">Ver ventas</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 admin-action-card">
                        <div class="card-body p-4">
                            <div class="admin-action-card__icon"><span class="material-symbols-outlined">auto_awesome</span></div>
                            <h6 class="mb-2">Deskcir AI</h6>
                            <p class="text-muted small mb-3">Respuestas, resumenes y apoyo operativo rapido.</p>
                            <a href="{{ route('deskcir.ai') }}" class="btn btn-outline-deskcir btn-sm w-100 py-2">Abrir IA</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow-sm h-100 admin-reports-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <p class="deskcir-ai__eyebrow mb-1">Reportes integrados</p>
                            <h5 class="mb-1">Resumen rapido</h5>
                            <p class="text-muted small mb-0">Atajos que antes veias dentro del dashboard.</p>
                        </div>
                        <span class="admin-stat-card__icon is-info"><span class="material-symbols-outlined">query_stats</span></span>
                    </div>

                    <div class="admin-report-shortcuts mb-4">
                        <a href="/admin/reports/sales" class="admin-report-shortcut">Ventas</a>
                        <a href="/admin/reports/products" class="admin-report-shortcut">Productos</a>
                        <a href="/admin/reports/clients" class="admin-report-shortcut">Clientes</a>
                        <a href="/admin/reports/export/pdf" class="admin-report-shortcut">PDF</a>
                    </div>

                    <div class="admin-mini-metrics">
                        <div class="admin-mini-metric">
                            <span>Tecnicos activos</span>
                            <strong>{{ $tecnicosActivos }}</strong>
                        </div>
                        <div class="admin-mini-metric">
                            <span>Pedidos totales</span>
                            <strong>{{ $pedidos }}</strong>
                        </div>
                    </div>

                    <div class="admin-trend-list mt-4">
                        @forelse($ventasGrafica as $punto)
                            <div class="admin-trend-item">
                                <span>{{ \Carbon\Carbon::parse($punto->fecha)->format('d M') }}</span>
                                <strong>${{ number_format($punto->total, 2) }}</strong>
                            </div>
                        @empty
                            <div class="admin-trend-item is-empty">
                                <span>Sin ventas recientes</span>
                                <strong>$0.00</strong>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Tickets recientes</h5>
                        <a href="/admin/tickets" class="btn btn-outline-deskcir btn-sm">Ver todos</a>
                    </div>

                    <div class="admin-list-stack">
                        @forelse($ticketsRecientes as $ticket)
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="admin-list-item">
                                <div>
                                    <strong>{{ $ticket->subject }}</strong>
                                    <span>{{ $ticket->user->name ?? 'Sin usuario' }}</span>
                                </div>
                                <span class="badge bg-{{ $ticket->priority === 'alta' ? 'danger' : ($ticket->priority === 'media' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($ticket->priority ?? 'media') }}</span>
                            </a>
                        @empty
                            <div class="admin-list-item is-static">
                                <div>
                                    <strong>Sin tickets recientes</strong>
                                    <span>No hay casos nuevos por revisar.</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Top productos</h5>
                        <a href="/admin/reports/products" class="btn btn-outline-deskcir btn-sm">Ver reporte</a>
                    </div>

                    <div class="admin-list-stack">
                        @forelse($topProductos as $producto)
                            <a href="/admin/products/{{ $producto->id }}/edit" class="admin-list-item">
                                <div>
                                    <strong>{{ $producto->name }}</strong>
                                    <span>{{ $producto->order_items_count }} ventas registradas</span>
                                </div>
                                <span class="admin-list-item__metric">${{ number_format($producto->price, 2) }}</span>
                            </a>
                        @empty
                            <div class="admin-list-item is-static">
                                <div>
                                    <strong>Sin productos destacados</strong>
                                    <span>Aun no hay ventas asociadas.</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Acciones sugeridas</h5>
                    </div>

                    <div class="admin-list-stack">
                        <a href="{{ route('deskcir.ai', ['prompt' => 'Resume el estado operativo del panel admin y dime las prioridades de hoy.']) }}" class="admin-list-item">
                            <div>
                                <strong>Resumen operativo con IA</strong>
                                <span>Prioriza tickets, ventas y cuellos de botella.</span>
                            </div>
                        </a>
                        <a href="/admin/reports" class="admin-list-item">
                            <div>
                                <strong>Revisar reportes ejecutivos</strong>
                                <span>Analiza ingresos, entregas y ticket promedio.</span>
                            </div>
                        </a>
                        <a href="/admin/products/create" class="admin-list-item">
                            <div>
                                <strong>Agregar producto nuevo</strong>
                                <span>Mantiene activo el catalogo sin salir del panel.</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h5 class="mb-0">Historial de ventas</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="/admin/sales" class="btn btn-deskcir btn-sm">Ver todas</a>
                            <a href="/admin/reports/export/excel" class="btn btn-outline-deskcir btn-sm">Exportar Excel</a>
                        </div>
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
                            @forelse($ultimasVentas as $o)
                                <tr>
                                    <td>{{ $o->id }}</td>
                                    <td>{{ $o->user->name ?? 'Invitado' }}</td>
                                    <td>{{ ucfirst($o->payment_method) }}</td>
                                    <td>${{ number_format($o->total, 2) }}</td>
                                    <td>
                                        @if($o->status == 'entregado')
                                            <span class="badge bg-success">Entregado</span>
                                        @elseif($o->status == 'en camino')
                                            <span class="badge bg-warning text-dark">En camino</span>
                                        @elseif($o->status == 'cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @else
                                            <span class="badge bg-secondary">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $o->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Todavia no hay ventas para mostrar.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
