<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Deskcir')</title>

<script>
(function () {
    const theme = localStorage.getItem('modo');

    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else if (theme === 'light') {
        document.documentElement.classList.remove('dark');
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    }
})();
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

@vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="transition-colors duration-300">
@php
    $roleName = optional(optional(auth()->user())->role)->name;
    $hideFloatingAi = request()->routeIs('deskcir.ai');
    $cart = session('cart', []);
    $cartCount = collect($cart)->sum(fn ($item) => (int) ($item['qty'] ?? 0));
    $searchValue = trim((string) request('q', ''));
    $showGlobalSearch = auth()->check() || request()->is('store*') || request()->is('cart') || request()->is('support*') || request()->routeIs('deskcir.ai');
    $isStoreActive = request()->is('store*');
    $isCartActive = request()->is('cart') || request()->is('checkout*');
    $isAiActive = request()->routeIs('deskcir.ai');
    $isSupportActive = request()->is('support*');
@endphp

<nav class="navbar navbar-expand-lg navbar-light shadow-sm bg-light sticky-top">
    <div class="container nav-shell">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/store">
            <img src="{{ asset('img/logo.png') }}" style="height:30px" alt="Deskcir">
        </a>

        <button
            class="navbar-toggler nav-hamburger"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Abrir menu"
        >
            <span class="bi bi-list fs-3 lh-1"></span>
        </button>

        <div class="collapse navbar-collapse nav-collapse-shell" id="mainNavbar">
            @if($showGlobalSearch)
                <form method="GET" action="/store" class="nav-store-search" role="search">
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchValue }}"
                        class="nav-store-search-input"
                        placeholder="Buscar productos o volver rapido a la tienda..."
                        aria-label="Buscar producto"
                    >
                    <button class="nav-store-search-btn" type="submit" aria-label="Buscar">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            @endif

            <ul class="navbar-nav ms-lg-auto align-items-lg-center gap-2 nav-main-links">
                <li class="nav-item">
                    <a class="nav-link {{ $isStoreActive ? 'is-current' : '' }}" href="/store">
                        <span class="material-symbols-outlined">store</span>
                        Tienda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $isCartActive ? 'is-current' : '' }}" href="/cart">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        Carrito
                        @if($cartCount > 0)
                            <span class="nav-cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-ai-link {{ $isAiActive ? 'is-current is-ai-current' : '' }}" href="{{ route('deskcir.ai') }}">
                        <span class="material-symbols-outlined">auto_awesome</span>
                        Deskcir AI
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $isSupportActive ? 'is-current' : '' }}" href="/support/create">
                        <span class="material-symbols-outlined">support_agent</span>
                        Solicitar soporte
                    </a>
                </li>

                @auth
                    @if($roleName === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'is-current' : '' }}" href="{{ route('admin.dashboard') }}">
                                <span class="material-symbols-outlined">admin_panel_settings</span>
                                Administrador
                            </a>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown"
                            role="button"
                            aria-expanded="false"
                        >
                            <span class="material-symbols-outlined">account_circle</span>
                            {{ auth()->user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('deskcir.ai') }}">
                                    <span class="material-symbols-outlined">auto_awesome</span>
                                    Abrir Deskcir AI
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/support/create">
                                    <span class="material-symbols-outlined">support_agent</span>
                                    Nuevo soporte
                                </a>
                            </li>

                            @if($roleName === 'admin')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                                        <span class="material-symbols-outlined">dashboard</span>
                                        Panel Admin
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.products.index') }}">
                                        <span class="material-symbols-outlined">inventory_2</span>
                                        Productos
                                    </a>
                                </li>
                            @endif

                            @if($roleName === 'technician')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="/technician">
                                        <span class="material-symbols-outlined">build</span>
                                        Panel Tecnico
                                    </a>
                                </li>
                            @endif

                            @if($roleName === 'client')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="/client">
                                        <span class="material-symbols-outlined">support_agent</span>
                                        Mi cuenta
                                    </a>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="/logout" class="logout-form">
                                    @csrf
                                    <button class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <span class="material-symbols-outlined">logout</span>
                                        Cerrar sesion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-deskcir d-flex align-items-center justify-content-center gap-1" href="/login">
                            <span class="material-symbols-outlined">login</span>
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-deskcir d-flex align-items-center justify-content-center gap-1" href="/register">
                            <span class="material-symbols-outlined">person_add</span>
                            Registro
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<button
    onclick="toggleDark()"
    id="btnDark"
    class="btn btn-dark shadow app-theme-toggle"
    aria-label="Cambiar tema"
>
    <i class="bi bi-moon-fill"></i>
</button>

<div class="container-fluid app-shell py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xxl-10 col-xl-11 col-lg-11">
            @yield('content')
        </div>
    </div>
</div>

@if($cartCount > 0 && !$isCartActive)
    <a href="/cart" class="cart-fab" aria-label="Abrir carrito">
        <span class="material-symbols-outlined">shopping_bag</span>
        <span class="cart-fab__content">
            <strong>Carrito</strong>
            <small>{{ $cartCount }} articulo{{ $cartCount === 1 ? '' : 's' }}</small>
        </span>
    </a>
@endif

@if(!$hideFloatingAi)
    <x-floating-ai-chat />
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleDark() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('modo', isDark ? 'dark' : 'light');
    updateDarkIcon(isDark);
}

function updateDarkIcon(isDark) {
    const btn = document.getElementById('btnDark');
    if (!btn) return;

    btn.innerHTML = isDark
        ? '<i class="bi bi-sun-fill"></i>'
        : '<i class="bi bi-moon-fill"></i>';
}

document.addEventListener('DOMContentLoaded', () => {
    updateDarkIcon(document.documentElement.classList.contains('dark'));

    document.querySelectorAll('.logout-form').forEach((form) => {
        form.addEventListener('submit', () => {
            Object.keys(localStorage).forEach((key) => {
                if (key.startsWith('deskcir-ai-')) {
                    localStorage.removeItem(key);
                }
            });

            Object.keys(sessionStorage).forEach((key) => {
                if (key.startsWith('deskcir-ai-') || key.startsWith('deskcir-support-')) {
                    sessionStorage.removeItem(key);
                }
            });
        });
    });
});
</script>

@stack('scripts')
</body>

</html>
