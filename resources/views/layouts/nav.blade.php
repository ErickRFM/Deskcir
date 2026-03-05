<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container">

<a class="navbar-brand" href="/">Deskcir</a>

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link" href="/store">
<span class="material-symbols-outlined align-middle">store</span>
Tienda
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="/cart">
<span class="material-symbols-outlined align-middle">shopping_cart</span>
Carrito
</a>
</li>

@auth

<li class="nav-item">
<span class="nav-link">
<span class="material-symbols-outlined align-middle">account_circle</span>
{{ auth()->user()->name }}
</span>
</li>

@if(auth()->user()->role->name == 'admin')

<li class="nav-item">
<a class="nav-link" href="{{ route('admin.dashboard') }}">
<span class="material-symbols-outlined align-middle">dashboard</span>
Administrador
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="{{ route('admin.products.index') }}">
<span class="material-symbols-outlined align-middle">inventory_2</span>
Productos
</a>
</li>

@endif

@if(auth()->user()->role->name == 'technician')

<li class="nav-item">
<a class="nav-link" href="/technician">
<span class="material-symbols-outlined align-middle">build</span>
Técnico
</a>
</li>

@endif

@if(auth()->user()->role->name == 'client')

<li class="nav-item">
<a class="nav-link" href="/client">
<span class="material-symbols-outlined align-middle">support_agent</span>
Cliente
</a>
</li>

@endif

<li class="nav-item">
<form method="POST" action="/logout">
@csrf
<button class="btn btn-sm btn-danger ms-2">
<span class="material-symbols-outlined align-middle">logout</span>
Salir
</button>
</form>
</li>

@else

<li class="nav-item">
<a class="nav-link" href="/login">
<span class="material-symbols-outlined align-middle">login</span>
Login
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="/register">
<span class="material-symbols-outlined align-middle">person_add</span>
Registro
</a>
</li>

@endauth

</ul>
</div>
</nav>

