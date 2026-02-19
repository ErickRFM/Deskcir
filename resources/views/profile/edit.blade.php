@extends('layouts.app')

@section('title','Mi perfil | Deskcir')

@section('content')

<div class="container py-4">
<div class="row justify-content-center">
<div class="col-lg-9">

{{-- ================= PERFIL ================= --}}
<div class="card mb-4">
<div class="card-body p-4">

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1">Mi perfil</h4>
        <p class="text-muted mb-0">
            Actualiza tu información personal
        </p>
    </div>

    {{-- 🔥 REGRESO REAL --}}
    <button onclick="history.back()" class="btn btn-sm btn-outline-secondary">
        ← Regresar
    </button>
</div>

<form method="POST"
action="{{ route('profile.update') }}"
enctype="multipart/form-data">
@csrf
@method('PATCH')

{{-- ===== AVATAR ===== --}}
<div class="d-flex flex-column align-items-center mb-4">

<img id="preview"
src="{{ auth()->user()->avatar
? asset('storage/'.auth()->user()->avatar)
: 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=00798E&color=fff' }}"
class="rounded-circle mb-2 shadow avatar-pro">

<label class="btn btn-sm btn-outline-secondary">
Cambiar foto
<input id="avatarInput"
type="file"
name="avatar"
hidden
accept="image/*">
</label>

@error('avatar')
<small class="text-danger fw-bold">{{ $message }}</small>
@enderror

</div>

{{-- ===== DATOS ===== --}}
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Nombre</label>
<input name="name"
value="{{ old('name', auth()->user()->name) }}"
class="form-control input-pro @error('name') is-invalid @enderror">

@error('name')
<small class="text-danger fw-bold">{{ $message }}</small>
@enderror
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Correo</label>
<input name="email"
value="{{ old('email', auth()->user()->email) }}"
class="form-control input-pro @error('email') is-invalid @enderror">

@error('email')
<small class="text-danger fw-bold">{{ $message }}</small>
@enderror
</div>
</div>

<div class="text-center mt-3">
<button class="btn btn-client px-5">
Guardar cambios
</button>
</div>

</form>
</div>
</div>

{{-- ================= CONTRASEÑA ================= --}}
<div class="card">
<div class="card-body p-4">

<div class="mb-4">
<h4 class="fw-bold mb-1">Seguridad</h4>
<p class="text-muted mb-0">
Cambia tu contraseña
</p>
</div>

{{-- 🔥 MODALES --}}
@if (session('status') === 'password-updated')
<script>
document.addEventListener('DOMContentLoaded', () => {
Swal.fire({
    icon: 'success',
    title: '¡Contraseña actualizada!',
    text: 'Tu contraseña fue cambiada correctamente',
    confirmButtonColor: '#00798E'
})
})
</script>
@endif

@if ($errors->has('current_password'))
<script>
document.addEventListener('DOMContentLoaded', () => {
Swal.fire({
    icon: 'error',
    title: 'Contraseña incorrecta',
    text: 'La contraseña actual no coincide',
    confirmButtonColor: '#00798E'
})
})
</script>
@endif


<form method="POST" action="{{ route('password.update') }}" id="formPass">
@csrf
@method('PUT')

<div class="row">

{{-- ACTUAL --}}
<div class="col-md-6 mb-3">
<label class="form-label">Contraseña actual</label>

<input type="password"
name="current_password"
id="current"
class="form-control input-pro @error('current_password') is-invalid @enderror">

@error('current_password')
<small class="text-danger fw-bold">{{ $message }}</small>
@enderror
</div>

{{-- NUEVA --}}
<div class="col-md-6 mb-3">
<label class="form-label">Nueva contraseña</label>

<input type="password"
name="password"
id="pass1"
class="form-control input-pro @error('password') is-invalid @enderror">

<small class="text-muted">
Mínimo 8 caracteres, una mayúscula y un número
</small>

@error('password')
<small class="text-danger fw-bold d-block">{{ $message }}</small>
@enderror
</div>

{{-- CONFIRMAR --}}
<div class="col-md-6 mb-3">
<label class="form-label">Confirmar</label>

<input type="password"
name="password_confirmation"
id="pass2"
class="form-control input-pro">

<small id="errorPass" class="text-danger fw-bold d-none">
Las contraseñas no coinciden
</small>
</div>

</div>

<div class="text-center mt-3">
<button class="btn btn-client-outline px-5" id="btnPass">
Actualizar contraseña
</button>
</div>

</form>
</div>
</div>

{{-- ===== ESTILOS ===== --}}
<style>
.avatar-pro{
width:120px;
height:120px;
object-fit:cover;
border-radius:50%;
border:3px solid #00798E;
}

.btn-client{
background:#00798E;
color:white;
border-radius:12px;
}

.btn-client-outline{
border:1px solid #00798E;
color:#00798E;
border-radius:12px;
}

.input-pro{
border-radius:12px;
padding:10px;
}
.is-invalid{
border:2px solid #dc3545 !important;
}
</style>

@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ===== PREVIEW AVATAR ===== --}}
<script>
avatarInput.addEventListener('change', e => {
const [file] = e.target.files
if(file){
preview.src = URL.createObjectURL(file)
}
})
</script>

{{-- ===== VALIDACIÓN CONTRASEÑAS PRO ===== --}}
<script>
const p1 = document.getElementById('pass1')
const p2 = document.getElementById('pass2')
const actual = document.getElementById('current')
const form = document.getElementById('formPass')
const btn = document.getElementById('btnPass')

function reglaSegura(pass){
    return /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(pass)
}

function validar(){

    let ok = true

    // 👉 NUEVA CONTRASEÑA
    if(reglaSegura(p1.value)){
        p1.classList.remove('is-invalid')
        p1.classList.add('is-valid')
    }else{
        p1.classList.add('is-invalid')
        p1.classList.remove('is-valid')
        ok = false
    }

    // 👉 CONFIRMAR
    if(p1.value === p2.value && p1.value !== ''){
        p2.classList.remove('is-invalid')
        p2.classList.add('is-valid')
    }else{
        p2.classList.add('is-invalid')
        p2.classList.remove('is-valid')
        ok = false
    }

    // 👉 CONTRASEÑA ACTUAL
    if(actual.value.length >= 4){
        actual.classList.add('is-valid')
    }else{
        actual.classList.remove('is-valid')
        ok = false
    }

    btn.disabled = !ok
    return ok
}

// Eventos
p1.addEventListener('keyup', validar)
p2.addEventListener('keyup', validar)
actual.addEventListener('keyup', validar)

// Submit con alerta pro
form.addEventListener('submit', e => {

    if(!validar()){
        e.preventDefault()

        Swal.fire({
            icon: 'error',
            title: 'Contraseña insegura',
            html: `
            <div class="text-start">
            Debe contener:<br>
            • 8 caracteres mínimo<br>
            • 1 mayúscula<br>
            • 1 número<br>
            • Coincidir confirmación
            </div>`,
            confirmButtonColor:'#00798E'
        })
    }

})
</script>

@endpush