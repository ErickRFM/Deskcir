<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de ventas Deskcir</title>
    @include('pdf.partials.document-styles')
</head>
<body>
@php
    $totalRevenue = (float) $orders->sum(fn ($order) => (float) $order->total);
    $deliveredCount = $orders->where('status', 'entregado')->count();
    $pendingCount = $orders->where('status', '!=', 'entregado')->count();
@endphp

<div class="document-shell">
    @include('pdf.partials.document-header', [
        'title' => 'Reporte Ejecutivo de Ventas',
        'subtitle' => 'Resumen corporativo de pedidos exportados, ingresos y estado de cumplimiento comercial.',
    ])

    <table class="summary-table" cellpadding="0" cellspacing="10">
        <tr>
            <td class="summary-card">
                <span class="summary-label">Pedidos exportados</span>
                <span class="summary-value">{{ $orders->count() }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Ingresos acumulados</span>
                <span class="summary-value">${{ number_format($totalRevenue, 2) }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Entregados</span>
                <span class="summary-value">{{ $deliveredCount }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Pendientes</span>
                <span class="summary-value">{{ $pendingCount }}</span>
            </td>
        </tr>
    </table>

    <div class="section-block">
        <h3 class="section-title">Resumen del documento</h3>
        <div class="note-box">
            Este archivo concentra las ventas registradas en Deskcir al momento de la exportacion, incluyendo cliente,
            metodo de pago, monto total y estado operativo de cada pedido.
        </div>
    </div>

    <div class="section-block">
        <h3 class="section-title">Detalle de ventas</h3>
        <table class="data-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 28%;">Cliente</th>
                    <th style="width: 16%;">Metodo</th>
                    <th style="width: 14%;">Total</th>
                    <th style="width: 14%;">Estado</th>
                    <th style="width: 20%;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $statusClass = match ($order->status) {
                            'entregado' => 'status-success',
                            'en camino', 'pendiente' => 'status-warning',
                            'cancelado' => 'status-danger',
                            default => 'status-info',
                        };
                    @endphp
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>
                            <strong>{{ optional($order->user)->name ?? 'Invitado' }}</strong>
                            <div class="muted">{{ optional($order->user)->email ?? 'Sin correo registrado' }}</div>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', (string) $order->payment_method)) }}</td>
                        <td><strong>${{ number_format((float) $order->total, 2) }}</strong></td>
                        <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">No hay ventas registradas para incluir en este documento.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('pdf.partials.document-footer')
</div>
</body>
</html>
