@extends('layouts.app')

@section('title', 'Carrito | Deskcir')

@section('content')
@php
    $items = collect($cart);
    $subtotal = $items->sum(fn ($item) => ((float) ($item['price'] ?? 0)) * ((int) ($item['qty'] ?? 0)));
    $shipping = $subtotal > 0 ? 149 : 0;
    $total = $subtotal + $shipping;
@endphp

<div class="cart-page">
    <section class="cart-page__hero mb-4">
        <div>
            <p class="deskcir-ai__eyebrow mb-2">Deskcir Store</p>
            <h1 class="cart-page__title mb-2">Tu carrito listo para cerrar la compra.</h1>
            <p class="cart-page__subtitle mb-0">Revisa productos, confirma cantidades y usa el resumen flotante para avanzar sin perder contexto.</p>
        </div>
        <div class="cart-page__hero-meta">
            <span>{{ $items->count() }} producto{{ $items->count() === 1 ? '' : 's' }}</span>
            <span>Total estimado: ${{ number_format($total, 2) }}</span>
        </div>
    </section>

    @if($items->isEmpty())
        <section class="cart-empty card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5 text-center">
                <span class="material-symbols-outlined cart-empty__icon">shopping_cart</span>
                <h2 class="fw-bold mb-2">Tu carrito esta vacio</h2>
                <p class="text-muted mb-4">Explora la tienda y agrega productos para continuar con la compra.</p>
                <a href="/store" class="btn btn-deskcir px-4">Ir a la tienda</a>
            </div>
        </section>
    @else
        <div class="row g-4 align-items-start">
            <div class="col-xl-8">
                <section class="card border-0 shadow-sm cart-table-shell">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                            <div>
                                <h2 class="cart-section-title mb-1">Productos agregados</h2>
                                <p class="text-muted mb-0">Diseño compacto, limpio y congruente con el resto del sistema.</p>
                            </div>
                            <a href="/store" class="btn btn-outline-deskcir">Seguir comprando</a>
                        </div>

                        <div class="cart-table-wrap">
                            <table class="table cart-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio unitario</th>
                                        <th>Total</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $id => $item)
                                    @php
                                        $qty = (int) ($item['qty'] ?? 0);
                                        $price = (float) ($item['price'] ?? 0);
                                        $lineTotal = $qty * $price;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="cart-product-cell">
                                                <div class="cart-product-icon">
                                                    <span class="material-symbols-outlined">inventory_2</span>
                                                </div>
                                                <div>
                                                    <strong>{{ $item['name'] }}</strong>
                                                    <div class="text-muted small">Articulo agregado al carrito</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="cart-qty-pill">{{ $qty }}</span></td>
                                        <td>${{ number_format($price, 2) }}</td>
                                        <td class="fw-bold">${{ number_format($lineTotal, 2) }}</td>
                                        <td class="text-end">
                                            <form method="POST" action="/cart/remove/{{ $id }}">
                                                @csrf
                                                <button class="btn btn-outline-danger btn-sm cart-remove-btn">
                                                    <span class="material-symbols-outlined">delete</span>
                                                    Quitar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xl-4">
                <aside class="cart-summary-card">
                    <div class="cart-summary-card__inner">
                        <p class="deskcir-ai__eyebrow mb-2">Resumen flotante</p>
                        <h3 class="cart-section-title mb-3">Totales del pedido</h3>

                        <div class="cart-summary-row">
                            <span>Subtotal</span>
                            <strong>${{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="cart-summary-row">
                            <span>Envio estimado</span>
                            <strong>${{ number_format($shipping, 2) }}</strong>
                        </div>
                        <div class="cart-summary-row is-total">
                            <span>Total</span>
                            <strong>${{ number_format($total, 2) }}</strong>
                        </div>

                        <a href="/checkout" class="btn btn-deskcir w-100 py-3 mt-3">Finalizar pedido</a>
                        <a href="{{ route('deskcir.ai', ['prompt' => 'Ayudame a decidir si los productos de mi carrito cubren lo que necesito.']) }}" class="btn btn-outline-deskcir w-100 mt-2">Consultar a Deskcir AI</a>

                        <div class="cart-summary-note">
                            <span class="material-symbols-outlined">verified_user</span>
                            <div>
                                <strong>Compra con confianza</strong>
                                <p class="mb-0">Resumen visible mientras haces scroll y acceso rapido a checkout.</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    @endif
</div>
@endsection
