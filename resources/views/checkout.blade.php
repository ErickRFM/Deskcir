@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
@php
    $walletBalance = (float) (auth()->user()->wallet_balance ?? 0);
    $defaultCardId = optional($cards->firstWhere('is_default', true))->id;
@endphp

<div class="container py-4 checkout-pro">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-hero mb-4">
        <div class="checkout-hero__bg"></div>
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 position-relative">
            <div>
                <span class="checkout-kicker">DESKCIR CHECKOUT</span>
                <h2 class="fw-bold mb-1 d-flex align-items-center gap-2 mt-2">
                    <span class="material-symbols-outlined">shopping_bag</span>
                    Finalizar compra
                </h2>
                <p class="mb-2 text-light-emphasis">Configura entrega, pago y confirma en un flujo claro y profesional.</p>
                <div class="checkout-trust-row">
                    <span class="checkout-trust-pill">Visa</span>
                    <span class="checkout-trust-pill">Mastercard</span>
                    <span class="checkout-trust-pill">Amex</span>
                    <span class="checkout-trust-pill">Mercado Pago</span>
                    <span class="checkout-trust-pill">Cripto</span>
                </div>
            </div>
            <div class="checkout-hero__actions d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('wallet.index') }}" class="btn btn-outline-deskcir d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                    Billetera y tarjetas
                </a>
                <a href="/cart" class="btn btn-outline-light d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Carrito
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
                @csrf

                <div class="card checkout-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                                <span class="material-symbols-outlined">local_shipping</span>
                                Entrega
                            </h5>
                            <span class="badge bg-light text-dark border">Paso 1</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-check card-radio p-3 border rounded-4 h-100">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="deliveryShipping" value="shipping" {{ old('delivery_type', 'shipping') === 'shipping' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="deliveryShipping">
                                        <strong>Envio a domicilio</strong>
                                        <small class="d-block text-muted">Ideal para recibir tu pedido en casa u oficina.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card-radio p-3 border rounded-4 h-100">
                                    <input class="form-check-input" type="radio" name="delivery_type" id="deliveryPickup" value="pickup" {{ old('delivery_type') === 'pickup' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="deliveryPickup">
                                        <strong>Punto de entrega</strong>
                                        <small class="d-block text-muted">Recoge en una sede Deskcir mas cercana.</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="shippingFields" class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Direccion</label>
                                <input name="address" class="form-control" value="{{ old('address') }}" placeholder="Calle, numero, colonia y referencias">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ciudad</label>
                                <input name="city" class="form-control" value="{{ old('city') }}" placeholder="Ciudad">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Codigo postal</label>
                                <input name="postal_code" class="form-control" value="{{ old('postal_code') }}" placeholder="00000">
                            </div>
                        </div>

                        <div id="pickupFields" class="row g-3 d-none">
                            <div class="col-12">
                                <label class="form-label">Punto de entrega</label>
                                <select class="form-select" name="pickup_point">
                                    <option value="">Selecciona un punto</option>
                                    @foreach($pickupPoints as $point)
                                        <option value="{{ $point }}" {{ old('pickup_point') === $point ? 'selected' : '' }}>{{ $point }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Telefono de contacto</label>
                                <input name="phone" class="form-control" value="{{ old('phone') }}" placeholder="10 digitos" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notas de entrega</label>
                                <input name="delivery_notes" class="form-control" value="{{ old('delivery_notes') }}" placeholder="Horario, referencias o acceso">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card checkout-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                                <span class="material-symbols-outlined">credit_card</span>
                                Metodo de pago
                            </h5>
                            <span class="badge bg-light text-dark border">Paso 2</span>
                        </div>

                        <div class="checkout-brand-strip mb-3">
                            <span class="checkout-brand-chip">Visa</span>
                            <span class="checkout-brand-chip">Mastercard</span>
                            <span class="checkout-brand-chip">Amex</span>
                            <span class="checkout-brand-chip">Mercado Pago</span>
                            <span class="checkout-brand-chip">SPEI</span>
                            <span class="checkout-brand-chip">Cripto</span>
                        </div>
                        <div class="checkout-pay-grid mb-3">
                            <label class="checkout-pay-card">
                                <input class="form-check-input" type="radio" name="payment_method" id="paySavedCard" value="card_saved" {{ old('payment_method') === 'card_saved' ? 'checked' : '' }}>
                                <span class="checkout-pay-card__brand">Tarjeta guardada</span>
                                <small>Usa una tarjeta ya validada en tu cuenta.</small>
                            </label>
                            <label class="checkout-pay-card">
                                <input class="form-check-input" type="radio" name="payment_method" id="payNewCard" value="card_new" {{ old('payment_method', 'card_new') === 'card_new' ? 'checked' : '' }}>
                                <span class="checkout-pay-card__brand">Nueva tarjeta</span>
                                <small>Visa, Mastercard, Amex y tarjetas bancarias.</small>
                            </label>
                            <label class="checkout-pay-card">
                                <input class="form-check-input" type="radio" name="payment_method" id="payTransfer" value="transfer" {{ old('payment_method') === 'transfer' ? 'checked' : '' }}>
                                <span class="checkout-pay-card__brand">Transferencia</span>
                                <small>BBVA, Banorte, Santander, Banamex y SPEI.</small>
                            </label>
                            <label class="checkout-pay-card">
                                <input class="form-check-input" type="radio" name="payment_method" id="payCash" value="cash" {{ old('payment_method') === 'cash' ? 'checked' : '' }}>
                                <span class="checkout-pay-card__brand">Efectivo</span>
                                <small>Contra entrega o pago presencial en punto.</small>
                            </label>
                            <label class="checkout-pay-card">
                                <input class="form-check-input" type="radio" name="payment_method" id="payWallet" value="wallet" {{ old('payment_method') === 'wallet' ? 'checked' : '' }}>
                                <span class="checkout-pay-card__brand">Billetera Deskcir</span>
                                <small>Saldo disponible: ${{ number_format($walletBalance, 2) }}</small>
                            </label>
                            <label class="checkout-pay-card">
                                <input class="form-check-input" type="radio" name="payment_method" id="payBitcoin" value="bitcoin" {{ old('payment_method') === 'bitcoin' ? 'checked' : '' }}>
                                <span class="checkout-pay-card__brand">Cripto</span>
                                <small>BTC, ETH o USDT con referencia manual.</small>
                            </label>
                        </div>

                        <div id="savedCardBox" class="payment-box d-none">
                            @if($cards->isEmpty())
                                <div class="alert alert-warning mb-0 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                    <span>No tienes tarjetas guardadas.</span>
                                    <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-deskcir">Agregar tarjeta</a>
                                </div>
                            @else
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Selecciona tarjeta</strong>
                                    <a href="{{ route('wallet.index') }}" class="small text-decoration-none">Administrar tarjetas</a>
                                </div>
                                @foreach($cards as $card)
                                    <label class="saved-card-item mb-2 p-3 border rounded-4 d-flex align-items-center justify-content-between gap-2">
                                        <span class="form-check d-flex align-items-center gap-3 mb-0">
                                            <input class="form-check-input" type="radio" name="saved_card_id" value="{{ $card->id }}" {{ (string) old('saved_card_id', $defaultCardId) === (string) $card->id ? 'checked' : '' }}>
                                            <span>
                                                <strong>{{ $card->alias ?: ($card->brand . ' ' . $card->last4) }}</strong>
                                                <small class="d-block text-muted">
                                                    {{ $card->brand }} •••• {{ $card->last4 }}
                                                    @if($card->exp_month && $card->exp_year)
                                                        | {{ str_pad((string) $card->exp_month, 2, '0', STR_PAD_LEFT) }}/{{ $card->exp_year }}
                                                    @endif
                                                </small>
                                            </span>
                                        </span>
                                        @if($card->is_default)
                                            <span class="badge rounded-pill text-bg-info">Predeterminada</span>
                                        @endif
                                    </label>
                                @endforeach
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="make_default_card" id="makeDefaultSaved">
                                    <label class="form-check-label small" for="makeDefaultSaved">Marcar seleccionada como predeterminada</label>
                                </div>
                            @endif
                        </div>

                        <div id="newCardBox" class="payment-box d-none">
                            <div class="checkout-brand-strip mb-3">
                                <span class="checkout-brand-chip">Visa</span>
                                <span class="checkout-brand-chip">Mastercard</span>
                                <span class="checkout-brand-chip">Amex</span>
                                <span class="checkout-brand-chip">Mercado Pago</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Numero de tarjeta</label>
                                    <input name="card_number" class="form-control" value="{{ old('card_number') }}" placeholder="4111 1111 1111 1111">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Titular</label>
                                    <input name="card_holder" class="form-control" value="{{ old('card_holder') }}" placeholder="Nombre completo">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Mes</label>
                                    <input name="card_exp_month" type="number" min="1" max="12" class="form-control" value="{{ old('card_exp_month') }}" placeholder="MM">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ano</label>
                                    <input name="card_exp_year" type="number" min="2024" max="2100" class="form-control" value="{{ old('card_exp_year') }}" placeholder="AAAA">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">CVV</label>
                                    <input name="card_cvv" class="form-control" value="{{ old('card_cvv') }}" placeholder="123">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="save_card" id="saveCard" {{ old('save_card') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="saveCard">Guardar tarjeta</label>
                                    </div>
                                </div>
                                <div class="col-12 form-check ms-1">
                                    <input class="form-check-input" type="checkbox" value="1" name="make_default_card" id="makeDefaultNew" {{ old('make_default_card') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="makeDefaultNew">Marcar como predeterminada</label>
                                </div>
                            </div>
                        </div>

                        <div id="transferBox" class="payment-box d-none">
                            <div class="row g-3">
                                <div class="col-md-6"><div class="checkout-note-card"><strong>Banco principal</strong><span>BBVA</span></div></div>
                                <div class="col-md-6"><div class="checkout-note-card"><strong>CLABE</strong><span>012180001234567890</span></div></div>
                                <div class="col-md-6"><div class="checkout-note-card"><strong>Referencia</strong><span>DESKCIR-{{ now()->format('His') }}</span></div></div>
                                <div class="col-md-6"><div class="checkout-note-card"><strong>Bancos sugeridos</strong><span>BBVA, Banorte, Santander, Citibanamex, Mercado Pago</span></div></div>
                            </div>
                        </div>

                        <div id="cashBox" class="payment-box d-none">
                            <div class="checkout-note-card">
                                <strong>Pago en efectivo</strong>
                                <span>Disponible contra entrega o en punto de retiro, sujeto a confirmacion de zona.</span>
                            </div>
                        </div>

                        <div id="walletBox" class="payment-box d-none">
                            <div class="alert {{ $walletBalance >= $summary['total'] ? 'alert-success' : 'alert-warning' }} mb-0 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                <span>
                                    Saldo actual: <strong>${{ number_format($walletBalance, 2) }}</strong>.
                                    @if($walletBalance < $summary['total'])
                                        Saldo insuficiente para este pedido.
                                    @endif
                                </span>
                                <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-deskcir">Recargar</a>
                            </div>
                        </div>

                        <div id="bitcoinBox" class="payment-box d-none">
                            <div class="row g-3">
                                <div class="col-md-4"><div class="checkout-note-card"><strong>Red</strong><span>BTC / ETH / USDT</span></div></div>
                                <div class="col-md-4"><div class="checkout-note-card"><strong>Wallet</strong><span>bc1qdeskcirficticia12345</span></div></div>
                                <div class="col-md-4"><div class="checkout-note-card"><strong>Memo</strong><span>DSK-{{ now()->format('His') }}</span></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button class="btn btn-deskcir btn-lg">Confirmar compra</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card checkout-card border-0 shadow-sm position-sticky mb-3" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">receipt_long</span>
                        Resumen
                    </h5>

                    @foreach($cart as $item)
                        <div class="d-flex justify-content-between gap-2 mb-2 small">
                            <span>{{ $item['name'] }} x {{ $item['qty'] }}</span>
                            <strong>${{ number_format($item['price'] * $item['qty'], 2) }}</strong>
                        </div>
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span>${{ number_format($summary['subtotal'], 2) }}</span></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Envio</span><span id="shippingValue">${{ number_format($summary['shipping_fee'], 2) }}</span></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Servicio</span><span>${{ number_format($summary['service_fee'], 2) }}</span></div>
                    <div class="d-flex justify-content-between small mb-2"><span>Descuento</span><span>-${{ number_format($summary['discount'], 2) }}</span></div>

                    <div class="d-flex justify-content-between fs-4 fw-bold pt-2 border-top">
                        <span>Total</span>
                        <span id="totalValue">${{ number_format($summary['total'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="card checkout-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                        Recarga rapida
                    </h6>
                    <p class="small text-muted mb-3">Recarga tu billetera sin salir de checkout.</p>

                    <form method="POST" action="{{ route('wallet.topup') }}" class="quick-topup-form">
                        @csrf
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-deskcir quick-amount" data-value="200">+$200</button>
                            <button type="button" class="btn btn-sm btn-outline-deskcir quick-amount" data-value="500">+$500</button>
                            <button type="button" class="btn btn-sm btn-outline-deskcir quick-amount" data-value="1000">+$1000</button>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input name="amount" id="quickTopupAmount" type="number" min="50" step="0.01" class="form-control" placeholder="Monto">
                            <button class="btn btn-deskcir" type="submit">Recargar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');

    const paymentBoxes = {
        card_saved: document.getElementById('savedCardBox'),
        card_new: document.getElementById('newCardBox'),
        transfer: document.getElementById('transferBox'),
        cash: document.getElementById('cashBox'),
        wallet: document.getElementById('walletBox'),
        bitcoin: document.getElementById('bitcoinBox')
    };

    const shippingFields = document.getElementById('shippingFields');
    const pickupFields = document.getElementById('pickupFields');

    const subtotal = {{ (float) $summary['subtotal'] }};
    const service = {{ (float) $summary['service_fee'] }};
    const discount = {{ (float) $summary['discount'] }};

    const shippingValue = document.getElementById('shippingValue');
    const totalValue = document.getElementById('totalValue');

    function currency(value) {
        return '$' + value.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function refreshPaymentUI() {
        const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
        Object.keys(paymentBoxes).forEach((key) => {
            if (!paymentBoxes[key]) return;
            paymentBoxes[key].classList.toggle('d-none', key !== selected);
        });
    }

    function refreshDeliveryUI() {
        const selected = document.querySelector('input[name="delivery_type"]:checked')?.value;
        const isPickup = selected === 'pickup';
        if (shippingFields) shippingFields.classList.toggle('d-none', isPickup);
        if (pickupFields) pickupFields.classList.toggle('d-none', !isPickup);
        const shipping = isPickup ? 0 : 79;
        const total = Math.max(0, subtotal + shipping + service - discount);
        if (shippingValue) shippingValue.textContent = currency(shipping);
        if (totalValue) totalValue.textContent = currency(total);
    }

    paymentRadios.forEach((radio) => radio.addEventListener('change', refreshPaymentUI));
    deliveryRadios.forEach((radio) => radio.addEventListener('change', refreshDeliveryUI));

    document.querySelectorAll('.quick-amount').forEach((btn) => {
        btn.addEventListener('click', function () {
            const input = document.getElementById('quickTopupAmount');
            if (!input) return;
            input.value = this.dataset.value;
            input.focus();
        });
    });

    refreshPaymentUI();
    refreshDeliveryUI();
})();
</script>
@endsection

