@extends('layouts.app')

@section('content')

<div class="login-wrapper">
<div class="login-card">
    <div class="logo-zone">
        <img src="{{ asset('img/logo.png') }}" class="login-logo" alt="Deskcir">
    </div>

    <h2 class="title">Bienvenido a Deskcir</h2>
    <p class="subtitle">Accede para continuar</p>

    @if(->any())
        <div class="alert alert-danger">
            Credenciales incorrectas
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="redirect_to" value="{{ old('redirect_to',  ?? request('redirect_to')) }}">

        <div class="mb-3">
            <label class="form-label">Correo electronico</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control input-pro"
                placeholder="tu@email.com"
                autocomplete="email"
                required>

            @error('email')
            <small class="text-danger fw-bold">{{  }}</small>
            @enderror
        </div>

        <div class="mb-2">
            <label class="form-label">Contrasena</label>
            <input
                type="password"
                name="password"
                class="form-control input-pro"
                placeholder="Tu contrasena"
                autocomplete="current-password"
                required>

            @error('password')
            <small class="text-danger fw-bold">{{  }}</small>
            @enderror
        </div>

        <div class="mt-2 text-start">
            <a href="/register{{ request('redirect_to') ? '?redirect_to='.urlencode(request('redirect_to')) : '' }}" class="link-pro">
                Crear cuenta
            </a>
        </div>

        <button type="submit" class="btn-login mt-3">
            Iniciar sesion
        </button>
    </form>

    <div class="mt-3">
        <a href="{{ route('google.login') }}" class="btn btn-client-outline w-100">
            Continuar con Google
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
margin-bottom:12px;
}

.login-logo{
height:35px;
filter:drop-shadow(0 10px 10px rgba(0,0,0,.15));
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

.dark .subtitle{
color:#9ca3af;
}

.dark .form-label{
color:#e5e7eb !important;
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

.btn-login{
width:100%;
padding:12px;
border-radius:14px;
border:none;
background:#00798E;
color:white;
font-weight:600;
transition:.25s;
}

.btn-login:hover{
transform:translateY(-2px);
background:#00687a;
}

.link-pro{
color:#00798E;
font-weight:500;
text-decoration:none;
}

.link-pro:hover{
text-decoration:underline;
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
