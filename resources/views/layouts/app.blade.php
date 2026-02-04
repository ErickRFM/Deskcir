<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>

@include('layouts.nav')

<div class="container py-4">
@yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- 👇 AGREGADO SWEETALERT -->
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
<!-- 👆 FIN SWEETALERT -->

@stack('scripts')
</body>
</html>
