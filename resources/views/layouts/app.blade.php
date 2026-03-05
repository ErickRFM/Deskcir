<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>@yield('title','Deskcir')</title>

<!-- ?? ACTIVAR DARK MODE ANTES DE QUE CARGUE TODO -->
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

<!-- Bootstrap Icons (por si se usan en otras vistas) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- ?? GOOGLE MATERIAL SYMBOLS -->
<link rel="stylesheet"
href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:
opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

<!-- Tailwind compilado -->
@vite(['resources/css/app.css','resources/js/app.js'])

<style>

/* ?? alineaci?n iconos navbar */
.material-symbols-outlined{
font-size:20px;
vertical-align:middle;
margin-right:4px;
}

/* navbar limpio */
.navbar .nav-link{
display:flex;
align-items:center;
gap:6px;
}

</style>

</head>

<body class="transition-colors duration-300">

{{-- ================= NAVBAR ================= --}}
<nav class="navbar navbar-expand-lg shadow-sm bg-light">
<div class="container">

<a class="navbar-brand d-flex align-items-center gap-2" href="/store">
<img src="{{ asset('img/logo.png') }}" style="height:30px" alt="Deskcir">
</a>

<ul class="navbar-nav ms-auto align-items-center gap-2">

<li class="nav-item">
<a class="nav-link" href="/store">
<span class="material-symbols-outlined">store</span>
Tienda
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="/cart">
<span class="material-symbols-outlined">shopping_cart</span>
Carrito
</a>
</li>

@auth

<li class="nav-item dropdown">

<a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
data-bs-toggle="dropdown">

<span class="material-symbols-outlined">
account_circle
</span>

{{ auth()->user()->name }}

</a>

<ul class="dropdown-menu dropdown-menu-end">

@if(auth()->user()->role->name=='admin')

<li>
<a class="dropdown-item d-flex align-items-center gap-2"
href="{{ route('admin.dashboard') }}">

<span class="material-symbols-outlined">
dashboard
</span>

Panel Admin

</a>
</li>

<li>
<a class="dropdown-item d-flex align-items-center gap-2"
href="{{ route('admin.products.index') }}">

<span class="material-symbols-outlined">
inventory_2
</span>

Productos

</a>
</li>

@endif


@if(auth()->user()->role->name=='technician')

<li>
<a class="dropdown-item d-flex align-items-center gap-2"
href="/technician">

<span class="material-symbols-outlined">
build
</span>

Panel Técnico

</a>
</li>

@endif


@if(auth()->user()->role->name=='client')

<li>
<a class="dropdown-item d-flex align-items-center gap-2"
href="/client">

<span class="material-symbols-outlined">
support_agent
</span>

Mi Cuenta

</a>
</li>

@endif


<li><hr class="dropdown-divider"></li>

<li>

<form method="POST" action="/logout">
@csrf

<button class="dropdown-item text-danger d-flex align-items-center gap-2">

<span class="material-symbols-outlined">
logout
</span>

Cerrar sesi?n

</button>

</form>

</li>

</ul>
</li>

@else

<li class="nav-item">
<a class="btn btn-outline-secondary d-flex align-items-center gap-1"
href="/login">

<span class="material-symbols-outlined">
login
</span>

Login

</a>
</li>

<li class="nav-item">
<a class="btn btn-warning d-flex align-items-center gap-1"
href="/register">

<span class="material-symbols-outlined">
person_add
</span>

Registro

</a>
</li>

@endauth

</ul>

</div>
</nav>


<!-- ?? BOT?N DARK MODE -->
<button onclick="toggleDark()" 
id="btnDark"
class="btn btn-dark shadow"
style="position:fixed;bottom:20px;right:20px;z-index:999">

??

</button>


<div class="container-fluid py-4">
<div class="row justify-content-center">
<div class="col-12 col-xxl-10 col-xl-11 col-lg-11">

@yield('content')

</div>
</div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- ?? CONTROLADOR DARK MODE -->
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

btn.innerHTML = isDark ? '??' : '??';

}

document.addEventListener('DOMContentLoaded', () => {

updateDarkIcon(document.documentElement.classList.contains('dark'));

});

</script>

@stack('scripts')

</body>
</html>

