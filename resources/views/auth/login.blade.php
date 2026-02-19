@extends('layouts.app')

@section('content')

<div class="login-wrapper">

<div class="login-card">

    <!-- LOGO MÁS PRO -->
    <div class="logo-zone">
        <img src="{{ asset('img/logo.png') }}" class="login-logo">
    </div>

    <h2 class="title">Bienvenido a Deskcir</h2>
    <p class="subtitle">Accede para continuar</p>

    {{-- 👉 MENSAJES DE ERROR GENERALES --}}
    @if($errors->any())
        <div class="alert alert-danger">
            Credenciales incorrectas
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Correo</label>

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control input-pro"
            placeholder="tu@email.com"
            required>

        @error('email')
        <small class="text-danger fw-bold">
            {{ $message }}
        </small>
        @enderror
    </div>

    <div class="mb-2">
        <label class="form-label">Contraseña</label>

        <input
            type="password"
            name="password"
            class="form-control input-pro"
            placeholder="••••••"
            required>

        @error('password')
        <small class="text-danger fw-bold">
            {{ $message }}
        </small>
        @enderror
    </div>

    {{-- 🔥👉 AQUÍ MOVÍ "CREAR CUENTA" --}}
    <div class="mt-2  text-start">
        <a href="/register" class="link-pro">
            Crear cuenta
        </a>
    </div>

    <button class="btn-login mt-3">
        Iniciar sesión →
    </button>

    </form>

    {{-- 👉 LOGIN CON GOOGLE --}}
    <div class="mt-3">
        <a href="{{ route('google.login') }}"
           class="btn btn-client-outline w-100">
            Continuar con Google
        </a>
    </div>

</div>
</div>

<style>

/* =============================
   CARD ELITE
============================= */

.login-card{
background:white;
padding:34px;
border-radius:22px;
width:420px;

box-shadow:
0 6px 14px rgba(0,0,0,.10);

animation: fade .6s ease;
}

.dark .login-card{
background:#0b1220;
border:1px solid #1f293f;
}

/* =============================
   LOGO
============================= */

.logo-zone{
text-align:center;
margin-bottom:10px;
}

.login-logo{
height: 35px;
filter:drop-shadow(0 10px 10px rgba(0,0,0,.15));
}

/* =============================
   TEXTOS
============================= */

.title{
font-weight:800;
margin-bottom:4px;
color:#0f172a;
}

.subtitle{
color:#374151;
margin-bottom:22px;
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

/* =============================
   INPUTS
============================= */

.input-pro{
border-radius:12px;
padding:11px 12px;
transition:.2s;
border:1px solid #d1d5db;
}

.input-pro:focus{
border-color:#111827;
box-shadow:0 0 0 3px rgba(0,0,0,.15);
outline:none;
}

.dark .input-pro{
background:#060a15;
border:1px solid #1f293f;
color:white;
}

/* =============================
   BOTÓN
============================= */

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
background:#000;
}

/* =============================
   LINK
============================= */

.link-pro{
color:#2563eb;
font-weight:500;
text-decoration:none;
}

.link-pro:hover{
text-decoration:underline;
}

/* =============================
   ANIMACIÓN
============================= */

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