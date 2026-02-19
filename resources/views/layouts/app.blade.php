<!DOCTYPE html>
<html lang="es" class="transition-all">
<head>
<meta charset="UTF-8">
<title>@yield('title','Deskcir')</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

@vite(['resources/css/app.css','resources/js/app.js'])

<!-- 🌙 SCRIPT MODO OSCURO -->
<script>
if(localStorage.getItem('modo') === 'dark'){
    document.documentElement.classList.add('dark');
}
</script>

<style>
/* ====== ESTILO PRO GLOBAL ====== */

body{
background:#f5f6fa;
}

.navbar{
backdrop-filter: blur(10px);
}

.card{
border-radius:16px;
transition:.3s;
}

.card:hover{
transform:translateY(-4px);
box-shadow:0 20px 30px rgba(0,0,0,.08);
}

/* Botones */
.btn{
border-radius:12px;
}

/* Inputs */
.form-control{
border-radius:12px;
padding:10px;
}

/* Login */
.login-wrapper{
min-height:80vh;
display:flex;
align-items:center;
justify-content:center;
}

.login-card{
background:white;
padding:30px;
border-radius:20px;
width:420px;
box-shadow:0 20px 40px rgba(0,0,0,.15);
}

</style>

</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">

{{-- ===== NAVBAR PRO ===== --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
<div class="container">

<a class="navbar-brand d-flex align-items-center gap-2" href="/store">
    <img src="{{ asset('img/logo.png') }}" 
         style="height:30px" 
         alt="Deskcir">
    <span class="fw-bold"></span>
</a>

<ul class="navbar-nav ms-auto">

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
<li>
<a class="dropdown-item" href="{{ route('admin.dashboard') }}">
Panel Admin
</a>
</li>
@endif

@if(auth()->user()->role->name=='technician')
<li>
<a class="dropdown-item" href="/technician">
Panel Técnico
</a>
</li>
@endif

@if(auth()->user()->role->name=='client')
<li>
<a class="dropdown-item" href="/client">
Mi Cuenta
</a>
</li>
@endif

<li>
<form method="POST" action="/logout">
@csrf
<button class="dropdown-item text-danger">
Cerrar sesión
</button>
</form>
</li>

</ul>
</li>

@else

<li>
<a class="btn btn-outline-light me-2" href="/login">
Login
</a>
</li>

<li>
<a class="btn btn-warning" href="/register">
Registro
</a>
</li>

@endauth

</ul>
</div>
</nav>

<!-- 🌙 BOTÓN MODO OSCURO -->
<button onclick="toggleDark()" 
id="btnDark"
style="position:fixed;bottom:20px;right:20px;z-index:999"
class="btn btn-dark shadow">
🌙
</button>

<div class="container py-4">
@yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleDark(){
document.documentElement.classList.toggle('dark');

if(document.documentElement.classList.contains('dark')){
localStorage.setItem('modo','dark');
btnDark.innerHTML = '☀';
} else {
localStorage.setItem('modo','light');
btnDark.innerHTML = '🌙';
}
}

window.onload = () => {
if(localStorage.getItem('modo') === 'dark'){
btnDark.innerHTML = '☀';
}
}
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