<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>@yield('title','Deskcir')</title>

<!-- 🔥 ACTIVAR DARK MODE ANTES DE QUE CARGUE TODO -->
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

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Tailwind compilado -->
@vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="transition-colors duration-300">

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg shadow-sm">
<div class="container">

<a class="navbar-brand d-flex align-items-center gap-2" href="/store">
    <img src="{{ asset('img/logo.png') }}" style="height:30px" alt="Deskcir">
</a>

<ul class="navbar-nav ms-auto align-items-center gap-2">

<li class="nav-item">
<a class="nav-link" href="/store">🛒 Tienda</a>
</li>

<li class="nav-item">
<a class="nav-link" href="/cart">🛍 Carrito</a>
</li>

@auth

<li class="nav-item dropdown">
<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
{{ auth()->user()->name }}
</a>

<ul class="dropdown-menu">

@if(auth()->user()->role->name=='admin')
<li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
@endif

@if(auth()->user()->role->name=='technician')
<li><a class="dropdown-item" href="/technician">Panel Técnico</a></li>
@endif

@if(auth()->user()->role->name=='client')
<li><a class="dropdown-item" href="/client">Mi Cuenta</a></li>
@endif

<li>
<form method="POST" action="/logout">
@csrf
<button class="dropdown-item text-danger">Cerrar sesión</button>
</form>
</li>

</ul>
</li>

@else

<li>
<a class="btn btn-outline-secondary" href="/login">Login</a>
</li>

<li>
<a class="btn btn-warning" href="/register">Registro</a>
</li>

@endauth

</ul>
</div>
</nav>

<!-- 🌙 BOTÓN DARK MODE -->
<button onclick="toggleDark()" 
id="btnDark"
class="btn btn-dark shadow"
style="position:fixed;bottom:20px;right:20px;z-index:999">
🌙
</button>

<div class="container py-4">
@yield('content')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- 🌙 CONTROLADOR DARK MODE -->
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
    btn.innerHTML = isDark ? '☀️' : '🌙';
}

document.addEventListener('DOMContentLoaded', () => {
    updateDarkIcon(document.documentElement.classList.contains('dark'));
});
</script>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
Swal.fire({
icon:'success',
title:'¡Listo!',
text:"{{ session('success') }}",
confirmButtonColor:'#ffc107'
})
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
icon:'error',
title:'Oops...',
text:"{{ session('error') }}"
})
</script>
@endif

@stack('scripts')

</body>
</html>