@extends('layouts.app')

@section('title','Cliente | Deskcir')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark dark:text-white">
        Hola, {{ auth()->user()->name }}
    </h2>
    <p class="text-muted">
        Gestiona tus servicios, soporte, pagos y seguimiento desde aqui.
    </p>
</div>

<div class="deskcir-ai-inline-banner mb-4">
    <div>
        <p class="deskcir-ai__eyebrow mb-1">Deskcir AI</p>
        <h3 class="mb-1">Asistencia rapida desde tu panel</h3>
        <p class="mb-0">Pide ayuda para diagnosticar fallas, redactar mensajes o decidir si conviene soporte presencial.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('deskcir.ai') }}" class="btn btn-deskcir">Abrir Deskcir AI</a>
        <a href="/support/create?mode=presencial" class="btn btn-outline-light">Solicitar presencial</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100 client-profile-card">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="client-profile-card__media mb-4">
                    <img 
                        src="{{ auth()->user()->avatar 
                            ? asset('storage/'.auth()->user()->avatar).'?v='.time()
                            : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=00798E&color=fff' }}"
                        class="rounded-circle shadow avatar-user client-profile-card__avatar"
                        alt="Avatar de usuario"
                    >

                    <form method="POST"
                        action="{{ route('profile.avatar') }}"
                        enctype="multipart/form-data"
                        class="client-profile-card__upload">
                        @csrf

                        <label class="btn btn-sm btn-outline-deskcir mt-2 w-100">
                            Cambiar foto
                            <input type="file"
                                name="avatar"
                                hidden
                                onchange="this.form.submit()">
                        </label>
                    </form>
                </div>

                <div class="mb-3">
                    <h5 class="fw-bold mb-1 text-dark dark:text-white">
                        {{ auth()->user()->name }}
                    </h5>

                    <p class="text-muted small mb-0">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <div class="mt-3 px-1 d-grid gap-2">
                    <a href="/profile" class="btn btn-client w-100 py-2">
                        Editar perfil
                    </a>
                    <a href="{{ route('wallet.index') }}" class="btn btn-client-outline w-100 py-2">
                        Abrir billetera
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card client-wallet-spotlight border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase small text-muted mb-2">Billetera Deskcir</p>
                        <h4 class="fw-bold mb-1">Saldo disponible</h4>
                        <div class="display-6 fw-bold text-deskcir mb-2">${{ number_format((float) auth()->user()->wallet_balance, 2) }}</div>
                        <p class="text-muted mb-0">Recarga saldo, administra tus tarjetas y revisa movimientos.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('wallet.index') }}" class="btn btn-deskcir">Abrir billetera</a>
                        <a href="/checkout" class="btn btn-outline-deskcir">Ir a checkout</a>
                    </div>
                </div>
                <div class="client-wallet-spotlight__tags mt-3">
                    <span class="client-wallet-spotlight__tag">Visa</span>
                    <span class="client-wallet-spotlight__tag">Mastercard</span>
                    <span class="client-wallet-spotlight__tag">Amex</span>
                    <span class="client-wallet-spotlight__tag">Mercado Pago</span>
                    <span class="client-wallet-spotlight__tag">Cripto</span>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Tienda</h5>
                        <p class="text-muted mb-3">Compra productos tecnologicos y arma tu pedido.</p>
                        <a href="/store" class="btn btn-client">Ir a tienda</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Soporte</h5>
                        <p class="text-muted mb-3">Reporta fallas y chatea con tecnicos.</p>
                        <a href="/support" class="btn btn-client">Solicitar soporte</a>
                    </div>
                </div>
            </div><div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Deskcir AI</h5>
                        <p class="text-muted mb-3">Diagnosticos iniciales y ayuda para redactar respuestas.</p>
                        <a href="{{ route('deskcir.ai') }}" class="btn btn-client-outline">Abrir asistente</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Soporte presencial</h5>
                        <p class="text-muted mb-3">Registra una visita o recepcion de equipo.</p>
                        <a href="/support/create?mode=presencial" class="btn btn-client-outline">Registrar solicitud</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Historial</h5>
                        <p class="text-muted mb-3">Servicios, tickets, compras y citas.</p>
                        <a href="{{ route('client.history') }}" class="btn btn-client-outline">Ver historial</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Quejas y sugerencias</h5>
                        <p class="text-muted mb-3">Envianos comentarios para mejorar la experiencia.</p>
                        <a href="{{ route('feedback.create') }}" class="btn btn-client-outline">Enviar comentario</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Seguridad</h5>
                        <p class="text-muted mb-3">Actualiza tu contrasena y revisa tu perfil.</p>
                        <a href="/profile#password" class="btn btn-client-outline">Ir a seguridad</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection




