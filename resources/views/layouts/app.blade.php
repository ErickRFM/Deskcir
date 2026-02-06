<!DOCTYPE html>
<html lang="es" class="transition-all">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

@vite(['resources/css/app.css','resources/js/app.js'])

<!-- 🌙 SCRIPT MODO OSCURO -->
<script>
if(localStorage.getItem('modo') === 'dark'){
    document.documentElement.classList.add('dark');
}
</script>

</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">

@include('layouts.nav')

<!-- 🔥 BOTÓN FLOTANTE (EL TUYO ORIGINAL SE REEMPLAZA POR EL NUEVO) -->
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
    icon: 'success',
    title: '¡Listo!',
    text: "{{ session('success') }}",
    confirmButtonColor: '#ffc107'
})
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: "{{ session('error') }}"
})
</script>
@endif

@stack('scripts')

</body>
</html>