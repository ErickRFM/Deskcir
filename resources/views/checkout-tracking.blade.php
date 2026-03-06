@extends('layouts.app')

@section('title', 'Seguimiento de pedido')

@section('content')
@php
    $current = $statusSteps[$order->status] ?? 1;
@endphp

<div class="container py-4 tracking-page">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Pedido #{{ $order->id }}</h3>
            <p class="text-muted mb-0">Tracking: <strong>{{ $order->tracking_code ?: 'N/A' }}</strong></p>
        </div>
        <a href="/store" class="btn btn-outline-deskcir">Seguir comprando</a>
    </div>

    <div class="card tracking-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Seguimiento</h5>
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="step-box {{ $current >= 1 ? 'active' : '' }}">
                        <strong>1. Pendiente</strong>
                        <div class="small text-muted">Orden registrada</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-box {{ $current >= 2 ? 'active' : '' }}">
                        <strong>2. En camino</strong>
                        <div class="small text-muted">Despacho activo</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-box {{ $current >= 3 ? 'active' : '' }}">
                        <strong>3. Entregado</strong>
                        <div class="small text-muted">Pedido finalizado</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-box {{ $order->status === 'cancelado' ? 'cancelled' : '' }}">
                        <strong>4. Cancelado</strong>
                        <div class="small text-muted">Si aplica</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card tracking-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Detalle de productos</h5>
                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <div class="fw-semibold">{{ optional($item->product)->name ?? 'Producto' }}</div>
                                <small class="text-muted">Cantidad: {{ $item->qty }}</small>
                            </div>
                            <div class="fw-bold">${{ number_format($item->price * $item->qty, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card tracking-card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold">Pago</h6>
                    <p class="mb-1">Metodo: <strong>{{ str_replace('_', ' ', $order->payment_method) }}</strong></p>
                    @if($order->card)
                        <p class="mb-1">Tarjeta: <strong>{{ $order->card->brand }} •••• {{ $order->card->last4 }}</strong></p>
                    @endif
                    <p class="mb-0">Estado: <strong>{{ ucfirst($order->status) }}</strong></p>
                </div>
            </div>

            <div class="card tracking-card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold">Entrega</h6>
                    <p class="mb-1">Tipo: <strong>{{ $order->delivery_type === 'pickup' ? 'Punto de entrega' : 'Envio' }}</strong></p>
                    @if($order->delivery_type === 'pickup')
                        <p class="mb-1">Punto: {{ $order->pickup_point }}</p>
                    @else
                        <p class="mb-1">Direccion: {{ $order->address }}</p>
                        <p class="mb-1">Ciudad: {{ $order->city }}</p>
                        <p class="mb-1">CP: {{ $order->postal_code }}</p>
                    @endif
                    <p class="mb-0">Telefono: {{ $order->phone }}</p>
                </div>
            </div>

            <div class="card tracking-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold">Totales</h6>
                    <div class="d-flex justify-content-between small"><span>Subtotal</span><span>${{ number_format((float) $order->subtotal, 2) }}</span></div>
                    <div class="d-flex justify-content-between small"><span>Envio</span><span>${{ number_format((float) $order->shipping_fee, 2) }}</span></div>
                    <div class="d-flex justify-content-between small"><span>Servicio</span><span>${{ number_format((float) $order->service_fee, 2) }}</span></div>
                    <div class="d-flex justify-content-between small"><span>Descuento</span><span>-${{ number_format((float) $order->discount, 2) }}</span></div>
                    @if((float) $order->wallet_used > 0)
                        <div class="d-flex justify-content-between small"><span>Billetera usada</span><span>-${{ number_format((float) $order->wallet_used, 2) }}</span></div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>${{ number_format((float) $order->total, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
