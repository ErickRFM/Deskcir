<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container">
<a class="navbar-brand" href="/">Deskcir</a>

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link" href="/store">Tienda</a>
</li>

<li class="nav-item">
<a class="nav-link" href="/cart">Carrito</a>
</li>

@auth
<li class="nav-item">
<span class="nav-link">{{ auth()->user()->name }}</span>
</li>

@if(auth()->user()->role->name == 'admin')

<li class="nav-item">
<a class="nav-link" href="{{ route('admin.dashboard') }}">
Administrador
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="{{ route('admin.products.index') }}">
Productos
</a>
</li>

@endif

@if(auth()->user()->role->name == 'technician')
<li class="nav-item">
<a class="nav-link" href="/technician">Técnico</a>
</li>
@endif

@if(auth()->user()->role->name == 'client')
<li class="nav-item">
<a class="nav-link" href="/client">Cliente</a>
</li>
@endif

<li class="nav-item">
<form method="POST" action="/logout">
@csrf
<button class="btn btn-sm btn-danger ms-2">Salir</button>
</form>
</li>

@else

<li class="nav-item">
<a class="nav-link" href="/login">Login</a>
</li>

<li class="nav-item">
<a class="nav-link" href="/register">Registro</a>
</li>

@endauth

</ul>
</div>
</nav>
