@extends('layouts.app')

@section('content')

<div class="login-wrapper">
<div class="login-card">
    <div class="logo-zone">
        <img src="{{ asset('img/logo.png') }}" class="login-logo" alt="Deskcir">
    </div>

    <h2 class="title">Crear cuenta</h2>
    <p class="subtitle">Unete a Deskcir</p>

    @if($errors->any())
        <div class="alert alert-danger">
            Revisa los datos del formulario
        </div>
    @endif

    <form method="POST" action="{{ route('register', [], false) }}">
        @csrf
        <input type="hidden" name="redirect_to" value="{{ old('redirect_to', $redirectTo ?? request('redirect_to')) }}">

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control input-pro" placeholder="Tu nombre" autocomplete="name" required>
            @error('name')
            <small class="text-danger fw-bold">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electronico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control input-pro" placeholder="tu@email.com" autocomplete="email" required>
            @error('email')
            <small class="text-danger fw-bold">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contrasena</label>
            <input id="password" type="password" name="password" class="form-control input-pro" placeholder="Crea una contrasena" autocomplete="new-password" required>
            @error('password')
            <small class="text-danger fw-bold">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-2">
            <label for="password_confirmation" class="form-label">Confirmar contrasena</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control input-pro" placeholder="Repite la contrasena" autocomplete="new-password" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Rol</label>
            <select id="role" name="role" class="form-select input-pro">
                @foreach(($roles ?? collect()) as $role)
                    <option value="{{ $role->name }}" @selected(old('role', 'client') === $role->name)>
                        @switch($role->name)
                            @case('admin')
                                Administrador
                                @break
                            @case('technician')
                                Tecnico
                                @break
                            @case('cashier')
                                Caja
                                @break
                            @default
                                Cliente
                        @endswitch
                    </option>
                @endforeach
            </select>
            @error('role')
            <small class="text-danger fw-bold">{{ $message }}</small>
            @enderror
        </div>

        <div class="text-start mt-2">
            <a href="{{ route('login') }}{{ request('redirect_to') ? '?redirect_to='.urlencode(request('redirect_to')) : '' }}" class="link-pro">
                Ya tengo cuenta
            </a>
        </div>

        <div class="mt-3 text-center">
            <button type="submit" class="btn-login">Crear cuenta</button>
        </div>
    </form>

    <div class="mt-3">
        <a href="{{ route('google.login') }}" class="btn btn-client-outline w-100">
            Registrarse con Google
        </a>
    </div>
</div>
</div>

<style>
.login-wrapper{
min-height:calc(100vh - 120px);
display:flex;
align-items:center;
justify-content:center;
padding:40px 20px;
}

.login-card{
background:white;
padding:34px;
border-radius:22px;
width:420px;
box-shadow:0 6px 14px rgba(0,0,0,.10);
animation:fade .6s ease;
}

.dark .login-card{
background:#0b1220;
border:1px solid #1f293f;
}

.logo-zone{
text-align:center;
margin-bottom:10px;
}

.login-logo{
height:35px;
}

.title{
font-weight:800;
margin-bottom:4px;
color:#0f172a;
text-align:center;
}

.subtitle{
color:#374151;
margin-bottom:22px;
text-align:center;
}

.form-label{
color:#0f172a !important;
font-weight:600;
}

.dark .title{
color:white;
}

.dark .form-label{
color:#e5e7eb !important;
}

.dark .subtitle{
color:#9ca3af;
}

.input-pro{
border-radius:12px;
padding:11px 12px;
transition:.2s;
border:1px solid #d1d5db;
}

.input-pro:focus{
border-color:#00798E;
box-shadow:0 0 0 3px rgba(0,121,142,.15);
outline:none;
}

.dark .input-pro{
background:#060a15;
border:1px solid #1f293f;
color:white;
}

.link-pro{
color:#00798E;
font-weight:500;
text-decoration:none;
}

.link-pro:hover{
text-decoration:underline;
}

.btn-login{
width:100%;
padding:12px;
border-radius:14px;
border:none;
background:#00798E;
color:white;
font-weight:600;
transition:.3s;
}

.btn-login:hover{
transform:translateY(-2px);
background:#00687a;
}

@keyframes fade{
from{
opacity:0;
transform:translateY(20px);
}
to{
opacity:1;
transform:none;
}
}
</style>

@endsection

