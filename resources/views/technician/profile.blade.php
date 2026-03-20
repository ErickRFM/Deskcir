@extends('layouts.app')

@section('title', 'Perfil Tecnico | Deskcir')

@section('content')
<div class="container py-4 technician-profile-page">
    <div class="technician-profile-hero mb-4">
        <div class="technician-profile-hero__bg"></div>
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 position-relative">
            <div>
                <span class="technician-profile-kicker">PERFIL TECNICO</span>
                <h2 class="fw-bold mb-1 d-flex align-items-center gap-2 mt-2">
                    <span class="material-symbols-outlined">engineering</span>
                    {{ $user->name }}
                </h2>
                <p class="mb-0 text-light-emphasis">Administra tu informacion, avatar y seguridad desde tu panel tecnico.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('technician.tickets') }}" class="btn btn-outline-light d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined">confirmation_number</span>
                    Mis tickets
                </a>
                <a href="{{ route('technician.dashboard') }}" class="btn btn-deskcir d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined">dashboard</span>
                    Volver al panel
                </a>
            </div>
        </div>
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
            <div class="card border-0 shadow-sm technician-profile-card h-100">
                <div class="card-body p-4 text-center">
                    <img id="techPreview"
                        src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0b6f81&color=fff' }}"
                        class="technician-profile-avatar mx-auto mb-3"
                        alt="Avatar del tecnico">

                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="d-grid gap-2">
                        @csrf
                        <label class="btn btn-outline-deskcir" for="techAvatarInput">Cambiar foto</label>
                        <input id="techAvatarInput" type="file" name="avatar" hidden accept="image/*">
                        <button type="submit" class="btn btn-deskcir">Guardar foto</button>
                    </form>

                    <div class="technician-profile-meta mt-4">
                        <div>
                            <span>Rol</span>
                            <strong>{{ optional($user->role)->name ? ucfirst($user->role->name) : 'Tecnico' }}</strong>
                        </div>
                        <div>
                            <span>Cuenta desde</span>
                            <strong>{{ $user->created_at?->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Tickets asignados</p>
                            <h3 class="fw-bold mb-0">{{ $assignedTickets }}</h3>
                            <p class="text-muted small mt-2 mb-0">Total de tickets en tu bandeja tecnica.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Activos</p>
                            <h3 class="fw-bold mb-0">{{ $activeTickets }}</h3>
                            <p class="text-muted small mt-2 mb-0">Tickets abiertos o en proceso actualmente.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Cerrados</p>
                            <h3 class="fw-bold mb-0">{{ $closedTickets }}</h3>
                            <p class="text-muted small mt-2 mb-0">Servicios completados y cerrados.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <p class="text-uppercase small text-muted mb-2">Agenda proxima</p>
                            <h3 class="fw-bold mb-0">{{ $upcomingAppointments }}</h3>
                            <p class="text-muted small mt-2 mb-0">Citas programadas a partir de hoy.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4 technician-profile-card">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <p class="technician-profile-kicker mb-1">Informacion personal</p>
                        <h4 class="fw-bold mb-1">Datos del tecnico</h4>
                        <p class="text-muted mb-0">Mantener tu informacion actualizada ayuda a soporte y asignacion de tickets.</p>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input name="name" value="{{ old('name', $user->name) }}" class="form-control input-pro @error('name') is-invalid @enderror" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo</label>
                                <input name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control input-pro @error('email') is-invalid @enderror" required>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-deskcir px-4">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm technician-profile-card">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <p class="technician-profile-kicker mb-1">Seguridad</p>
                        <h4 class="fw-bold mb-1">Actualizar contrasena</h4>
                        <p class="text-muted mb-0">Usa una contrasena fuerte para proteger tu acceso tecnico.</p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" id="technicianPasswordForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Contrasena actual</label>
                                <input type="password" name="current_password" id="techCurrentPass" class="form-control input-pro @error('current_password') is-invalid @enderror" autocomplete="current-password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nueva contrasena</label>
                                <input type="password" name="password" id="techPass1" class="form-control input-pro @error('password') is-invalid @enderror" autocomplete="new-password">
                                <small class="text-muted d-block mt-1">Minimo 8 caracteres, una mayuscula y un numero.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmar contrasena</label>
                                <input type="password" name="password_confirmation" id="techPass2" class="form-control input-pro" autocomplete="new-password">
                                <small id="techPassError" class="text-danger fw-bold d-none mt-1">Las contrasenas no coinciden.</small>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-outline-deskcir px-4" id="techPassBtn">Actualizar contrasena</button>
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
    const avatarInput = document.getElementById('techAvatarInput');
    const preview = document.getElementById('techPreview');
    const p1 = document.getElementById('techPass1');
    const p2 = document.getElementById('techPass2');
    const current = document.getElementById('techCurrentPass');
    const form = document.getElementById('technicianPasswordForm');
    const btn = document.getElementById('techPassBtn');
    const errorPass = document.getElementById('techPassError');

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
.technician-profile-hero {
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    border: 1px solid #185473;
    background: linear-gradient(135deg, #071a2a 0%, #0b2a43 52%, #0f3a58 100%);
    color: #eaf9ff;
    padding: 1.25rem 1.25rem;
    box-shadow: 0 18px 38px rgba(5, 23, 35, 0.32);
}
.technician-profile-hero__bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 78% 26%, rgba(56, 189, 248, 0.22), transparent 36%),
        radial-gradient(circle at 18% 76%, rgba(45, 212, 191, 0.2), transparent 32%);
    pointer-events: none;
}
.technician-profile-kicker {
    font-size: .76rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-weight: 800;
    color: #9ed8ea;
}
.technician-profile-card {
    border-radius: 20px;
}
.technician-profile-avatar {
    width: 132px;
    height: 132px;
    object-fit: cover;
    border-radius: 999px;
    border: 4px solid rgba(0, 121, 142, 0.18);
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12);
}
.technician-profile-meta {
    display: grid;
    gap: .75rem;
}
.technician-profile-meta div {
    display: flex;
    justify-content: space-between;
    gap: .75rem;
    font-size: .9rem;
    color: #4a657a;
}
.technician-profile-meta strong {
    color: #112b40;
}
@media (max-width: 767.98px) {
    .technician-profile-avatar {
        width: 112px;
        height: 112px;
    }
}
</style>
@endpush
