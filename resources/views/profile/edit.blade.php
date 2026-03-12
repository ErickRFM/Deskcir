@extends('layouts.app')

@section('title','Mi perfil | Deskcir')

@section('content')
<div class="container py-4 profile-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Mi perfil</h2>
            <p class="text-muted mb-0">Actualiza tu informacion, foto y seguridad desde un solo lugar.</p>
        </div>

        <a href="/dashboard" class="btn btn-outline-deskcir d-inline-flex align-items-center gap-2" data-smart-back data-fallback="/dashboard">
            <span class="material-symbols-outlined">arrow_back</span>
            Regresar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="alert alert-success">Tu contrasena se actualizo correctamente.</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 profile-card">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <img id="preview"
                        src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=00798E&color=fff' }}"
                        class="profile-avatar mx-auto mb-3"
                        alt="Avatar de usuario">

                    <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>

                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="d-grid gap-2">
                        @csrf
                        <label class="btn btn-outline-deskcir" for="avatarInput">Cambiar foto</label>
                        <input id="avatarInput" type="file" name="avatar" hidden accept="image/*">
                        <button type="submit" class="btn btn-deskcir">Guardar foto</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 profile-card">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <p class="profile-kicker mb-1">Informacion personal</p>
                        <h4 class="fw-bold mb-1">Datos de la cuenta</h4>
                        <p class="text-muted mb-0">Mantener esta informacion actualizada ayuda a soporte y seguimiento.</p>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control input-pro @error('name') is-invalid @enderror" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo</label>
                                <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="form-control input-pro @error('email') is-invalid @enderror" required>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-deskcir px-4">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm profile-card">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <p class="profile-kicker mb-1">Seguridad</p>
                        <h4 class="fw-bold mb-1">Actualizar contrasena</h4>
                        <p class="text-muted mb-0">Usa una contrasena larga, con mayuscula y numero, para mantener tu cuenta segura.</p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" id="formPass">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Contrasena actual</label>
                                <input type="password" name="current_password" id="current" class="form-control input-pro @error('current_password') is-invalid @enderror" autocomplete="current-password">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nueva contrasena</label>
                                <input type="password" name="password" id="pass1" class="form-control input-pro @error('password') is-invalid @enderror" autocomplete="new-password">
                                <small class="text-muted d-block mt-1">Minimo 8 caracteres, una mayuscula y un numero.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirmar contrasena</label>
                                <input type="password" name="password_confirmation" id="pass2" class="form-control input-pro" autocomplete="new-password">
                                <small id="errorPass" class="text-danger fw-bold d-none mt-1">Las contrasenas no coinciden.</small>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-outline-deskcir px-4" id="btnPass">Actualizar contrasena</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const avatarInput = document.getElementById('avatarInput');
    const preview = document.getElementById('preview');
    const p1 = document.getElementById('pass1');
    const p2 = document.getElementById('pass2');
    const current = document.getElementById('current');
    const form = document.getElementById('formPass');
    const btn = document.getElementById('btnPass');
    const errorPass = document.getElementById('errorPass');

    if (avatarInput && preview) {
        avatarInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        });
    }

    const isStrong = (value) => /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(value || '');

    function validatePasswords() {
        if (!p1 || !p2 || !current || !btn) return true;

        let valid = true;
        const pass1 = p1.value || '';
        const pass2 = p2.value || '';
        const currentPass = current.value || '';

        if (pass1.length > 0 && !isStrong(pass1)) {
            p1.classList.add('is-invalid');
            valid = false;
        } else {
            p1.classList.remove('is-invalid');
        }

        if (pass2.length > 0 && pass1 !== pass2) {
            p2.classList.add('is-invalid');
            errorPass?.classList.remove('d-none');
            valid = false;
        } else {
            p2.classList.remove('is-invalid');
            errorPass?.classList.add('d-none');
        }

        if ((pass1.length || pass2.length) && currentPass.length < 4) {
            current.classList.add('is-invalid');
            valid = false;
        } else {
            current.classList.remove('is-invalid');
        }

        btn.disabled = !valid;
        return valid;
    }

    [p1, p2, current].forEach((field) => field?.addEventListener('input', validatePasswords));

    form?.addEventListener('submit', function(event){
        if (!validatePasswords()) {
            event.preventDefault();
        }
    });
})();
</script>

<style>
.profile-card {
    border-radius: 20px;
}

.profile-avatar {
    width: 132px;
    height: 132px;
    object-fit: cover;
    border-radius: 999px;
    border: 4px solid rgba(0, 121, 142, 0.18);
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12);
}

.profile-kicker {
    font-size: .78rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-weight: 800;
    color: #0a8698;
}

.profile-page .input-pro {
    border-radius: 14px;
    padding: .85rem .95rem;
}

@media (max-width: 767.98px) {
    .profile-avatar {
        width: 112px;
        height: 112px;
    }
}
</style>
@endpush