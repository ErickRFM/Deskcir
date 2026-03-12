@extends('layouts.app')

@section('title', 'Billetera Deskcir')

@section('content')
<div class="container py-4 wallet-page">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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

    <div class="wallet-hero mb-4">
        <div class="wallet-hero__bg"></div>
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 position-relative">
            <div>
                <span class="wallet-kicker">DESKCIR WALLET</span>
                <h3 class="fw-bold mb-1 d-flex align-items-center gap-2 mt-2">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                    Mi billetera
                </h3>
                <p class="mb-0 text-light-emphasis">Recarga saldo, administra tus tarjetas y revisa movimientos.</p>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn btn-outline-light d-flex align-items-center gap-1">
                <span class="material-symbols-outlined">shopping_cart_checkout</span>
                Ir a compra
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card wallet-card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="small text-muted">Saldo disponible</div>
                    <div class="display-6 fw-bold text-deskcir mb-3">${{ number_format((float) $user->wallet_balance, 2) }}</div>

                    <form method="POST" action="{{ route('wallet.topup') }}">
                        @csrf
                        <label class="form-label">Monto a recargar</label>
                        <input name="amount" type="number" min="50" step="0.01" class="form-control mb-2" placeholder="Ej. 500" required>
                        <button class="btn btn-deskcir w-100 d-flex align-items-center justify-content-center gap-1">
                            <span class="material-symbols-outlined">add_card</span>
                            Recargar saldo
                        </button>
                    </form>

                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <form method="POST" action="{{ route('wallet.topup') }}" class="m-0">
                            @csrf
                            <input type="hidden" name="amount" value="200">
                            <button class="btn btn-sm btn-outline-deskcir" type="submit">+$200</button>
                        </form>
                        <form method="POST" action="{{ route('wallet.topup') }}" class="m-0">
                            @csrf
                            <input type="hidden" name="amount" value="500">
                            <button class="btn btn-sm btn-outline-deskcir" type="submit">+$500</button>
                        </form>
                        <form method="POST" action="{{ route('wallet.topup') }}" class="m-0">
                            @csrf
                            <input type="hidden" name="amount" value="1000">
                            <button class="btn btn-sm btn-outline-deskcir" type="submit">+$1000</button>
                        </form>
                    </div>

                    <div class="small text-muted mt-3">Minimo de recarga: $50.00</div>
                </div>
            </div>

            <div class="card wallet-card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined">payments</span>
                            Metodos disponibles
                        </h6>
                        <span class="badge text-bg-light border">Verificados</span>
                    </div>

                    <div class="wallet-method-grid">
                        <div class="wallet-method-card">
                            <div class="wallet-method-card__head">
                                <span class="wallet-method-card__title">
                                    <span class="material-symbols-outlined">credit_card</span>
                                    Tarjetas y Mercado Pago
                                </span>
                                <span class="wallet-method-card__badge">Recomendado</span>
                            </div>
                            </div>
                            <small class="text-muted">Cargo inmediato y confirmacion automatica.</small>
                        </div>
                        <div class="wallet-method-card">
                            <div class="wallet-method-card__head">
                                <span class="wallet-method-card__title">
                                    <span class="material-symbols-outlined">account_balance</span>
                                    Transferencia bancaria
                                </span>
                                <span class="wallet-method-card__badge">SPEI</span>
                            </div>
                            <ul class="wallet-banks">
                                <li><span class="material-symbols-outlined">check_circle</span>BBVA</li>
                                <li><span class="material-symbols-outlined">check_circle</span>Banorte</li>
                                <li><span class="material-symbols-outlined">check_circle</span>Santander</li>
                                <li><span class="material-symbols-outlined">check_circle</span>Citibanamex</li>
                            </ul>
                        </div>
                        <div class="wallet-method-card">
                            <div class="wallet-method-card__head">
                                <span class="wallet-method-card__title">
                                    <span class="material-symbols-outlined">currency_bitcoin</span>
                                    Cripto Deskcir
                                </span>
                                <span class="wallet-method-card__badge">Manual</span>
                            </div>
                            <div class="small text-muted">BTC, ETH o USDT con referencia.</div>
                            <div class="small">Wallet: <strong>bc1qdeskcirficticia12345</strong></div>
                            <div class="small">Memo: <strong>DSK-{{ now()->format('His') }}</strong></div>
                        </div>
                    </div>

                    <div class="wallet-steps mt-3">
                        <div class="wallet-step">
                            <span class="material-symbols-outlined">check_circle</span>
                            Selecciona el metodo, realiza el pago y conserva tu referencia.
                        </div>
                        <div class="wallet-step">
                            <span class="material-symbols-outlined">check_circle</span>
                            Confirmamos tu recarga en un rango de 5-15 min habiles.
                        </div>
                    </div>
                </div>
            </div>
            <div class="card wallet-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined">credit_card</span>
                            Tarjetas guardadas
                        </h6>
                        @if($cards->isNotEmpty())
                            <form method="POST" action="{{ route('cards.clear') }}" onsubmit="return confirm('Eliminar todas tus tarjetas?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Limpiar</button>
                            </form>
                        @endif
                    </div>

                    <div class="wallet-brand-strip mb-3">
                        <span class="wallet-brand-pill">Visa</span>
                        <span class="wallet-brand-pill">Mastercard</span>
                        <span class="wallet-brand-pill">Amex</span>
                        <span class="wallet-brand-pill">Mercado Pago</span>
                    </div>
                    <form method="POST" action="{{ route('cards.save') }}" class="row g-2 mb-3">
                        @csrf
                        <div class="col-6">
                            <label class="form-label small">Marca</label>
                            <select name="brand" class="form-select form-select-sm" required>
                                <option value="Visa">Visa</option>
                                <option value="Mastercard">Mastercard</option>
                                <option value="Amex">Amex</option>
                                <option value="Tarjeta">Otra</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Ultimos 4</label>
                            <input name="last4" class="form-control form-control-sm" maxlength="4" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Alias</label>
                            <input name="alias" class="form-control form-control-sm" placeholder="Tarjeta principal">
                        </div>
                        <div class="col-3">
                            <label class="form-label small">MM</label>
                            <input name="exp_month" type="number" min="1" max="12" class="form-control form-control-sm">
                        </div>
                        <div class="col-3">
                            <label class="form-label small">AAAA</label>
                            <input name="exp_year" type="number" min="2024" max="2100" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 form-check ms-1">
                            <input class="form-check-input" type="checkbox" value="1" name="is_default" id="newCardPredeterminada">
                            <label class="form-check-label small" for="newCardPredeterminada">Tarjeta predeterminada</label>
                        </div>
                        <div class="col-12 d-grid">
                            <button class="btn btn-deskcir btn-sm">Guardar tarjeta</button>
                        </div>
                    </form>

                    @forelse($cards as $card)
                        <div class="wallet-card-row border rounded-3 p-2 mb-2">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <strong>{{ $card->alias ?: ($card->brand . ' ' . $card->last4) }}</strong>
                                    <div class="small text-muted">{{ $card->brand }} **** {{ $card->last4 }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if($card->is_default)
                                        <span class="badge text-bg-info">Predeterminada</span>
                                    @endif
                                    <form method="POST" action="{{ route('cards.delete', $card->id) }}" onsubmit="return confirm('Eliminar tarjeta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Borrar</button>
                                    </form>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('cards.update', $card->id) }}" class="row g-2">
                                @csrf
                                @method('PUT')
                                <div class="col-6">
                                    <input name="alias" class="form-control form-control-sm" value="{{ $card->alias }}" placeholder="Alias" required>
                                </div>
                                <div class="col-3">
                                    <input name="exp_month" type="number" min="1" max="12" class="form-control form-control-sm" value="{{ $card->exp_month }}" placeholder="MM">
                                </div>
                                <div class="col-3">
                                    <input name="exp_year" type="number" min="2024" max="2100" class="form-control form-control-sm" value="{{ $card->exp_year }}" placeholder="AAAA">
                                </div>
                                <div class="col-8 form-check ms-1">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_default" id="default_{{ $card->id }}" {{ $card->is_default ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="default_{{ $card->id }}">Predeterminada</label>
                                </div>
                                <div class="col-3 d-grid">
                                    <button class="btn btn-sm btn-outline-deskcir">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="small text-muted">Aun no tienes tarjetas guardadas.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card wallet-card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">fact_check</span>
                        Proceso de pago
                    </h5>
                    <div class="wallet-steps">
                        <div class="wallet-step">
                            <span class="material-symbols-outlined">check_circle</span>
                            Escoge metodo, revisa el monto y confirma datos.
                        </div>
                        <div class="wallet-step">
                            <span class="material-symbols-outlined">check_circle</span>
                            Recibe tu comprobante y seguimiento desde esta misma vista.
                        </div>
                        <div class="wallet-step">
                            <span class="material-symbols-outlined">check_circle</span>
                            Si pagas con cripto o SPEI, verifica referencia antes de cerrar.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card wallet-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">receipt_long</span>
                        Movimientos
                    </h5>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td>
                                        @if($tx->type === 'topup')
                                            <span class="badge bg-success">Recarga</span>
                                        @else
                                            <span class="badge bg-info text-dark">Compra</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $tx->reference ?: ('TX-' . $tx->id) }}</td>
                                    <td class="fw-semibold {{ $tx->type === 'topup' ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->type === 'topup' ? '+' : '-' }}${{ number_format((float) $tx->amount, 2) }}
                                    </td>
                                    <td class="small">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aun no hay movimientos.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




