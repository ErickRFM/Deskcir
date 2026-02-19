@extends('layouts.app')

@section('title','Cliente | Deskcir')

@section('content')

{{-- 👉 MENSAJE DE ÉXITO --}}
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark dark:text-white">
        Hola, {{ auth()->user()->name }} 👋
    </h2>
    <p class="text-muted">
        Gestiona tus servicios, soporte y compras desde aquí
    </p>
</div>

{{-- GRID PRINCIPAL --}}
<div class="row g-4">

    {{-- PERFIL --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">

                {{-- AVATAR --}}
                <div class="mb-3 position-relative">

                    <img 
                        src="{{ auth()->user()->avatar 
                            ? asset('storage/'.auth()->user()->avatar).'?v='.time()
                            : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=00798E&color=fff' }}"
                        class="rounded-circle shadow avatar-user"
                    >

                    {{-- BOTÓN CAMBIAR FOTO --}}
                    <form method="POST"
                        action="{{ route('profile.avatar') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <label class="btn btn-sm btn-light mt-2">
                            Cambiar foto
                            <input type="file"
                                name="avatar"
                                hidden
                                onchange="this.form.submit()">
                        </label>
                    </form>

                </div>

                {{-- INFO --}}
                <div class="mb-3">
                    <h5 class="fw-bold mb-0 text-dark dark:text-white">
                        {{ auth()->user()->name }}
                    </h5>

                    <p class="text-muted small mb-0">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                {{-- BOTÓN --}}
                <div class="mt-3 px-3">
                    <a href="/profile" class="btn btn-client w-100 py-2">
                        Editar perfil
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ACCIONES --}}
    <div class="col-lg-8">
        <div class="row g-4">

            {{-- TIENDA --}}
            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">🛒 Tienda</h5>
                        <p class="text-muted mb-3">
                            Compra productos tecnológicos
                        </p>
                        <a href="/store" class="btn btn-client">
                            Ir a tienda
                        </a>
                    </div>
                </div>
            </div>

            {{-- SOPORTE --}}
            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">🧑‍💻 Soporte</h5>
                        <p class="text-muted mb-3">
                            Reporta fallas y chatea con técnicos
                        </p>
                        <a href="/support" class="btn btn-client">
                            Solicitar soporte
                        </a>
                    </div>
                </div>
            </div>

            {{-- HISTORIAL --}}
            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">📄 Historial</h5>
                        <p class="text-muted mb-3">
                            Servicios, tickets y compras
                        </p>
                        <a href="/client/history" class="btn btn-client-outline">
                            Ver historial
                        </a>
                    </div>
                </div>
            </div>

            {{-- SEGURIDAD --}}
            <div class="col-md-6">
                <div class="card card-action h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">🔐 Seguridad</h5>
                        <p class="text-muted mb-3">
                            Cambia tu contraseña
                        </p>
                        <a href="/profile#password" class="btn btn-client-outline">
                            Cambiar contraseña
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection