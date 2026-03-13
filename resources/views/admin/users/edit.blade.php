@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="fw-bold mt-2">Editar Usuario</h3>

        <div class="d-flex gap-3">

            {{-- BOTON REGRESAR --}}
            <a href="javascript:history.back()" class="btn btn-outline-deskcir py-2">
                Regresar
            </a>

        </div>

    </div>


    {{-- CARD FORM --}}
    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.users.update',$user->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">

                {{-- NOMBRE --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Nombre
                    </label>

                    <input name="name"
                           class="form-control"
                           value="{{ $user->name }}"
                           placeholder="Ej: Juan Perez">

                </div>


                {{-- EMAIL --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Email
                    </label>

                    <input name="email"
                           class="form-control"
                           value="{{ $user->email }}"
                           placeholder="usuario@email.com">

                </div>


                {{-- PASSWORD --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Contrasena
                    </label>

                    <input name="password"
                           type="password"
                           class="form-control"
                           placeholder="Nueva contrasena (opcional)">

                </div>


                {{-- ROL --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Rol
                    </label>

                    <select name="role_id" class="form-select">

                        @foreach($roles as $r)

                        <option value="{{ $r->id }}"
                        {{ $user->role_id == $r->id ? 'selected' : '' }}>
                            {{ ucfirst($r->name) }}
                        </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- BOTON --}}
            <div class="mt-4 d-flex gap-3">

                <button class="btn btn-deskcir px-4 py-2">
                    Actualizar usuario
                </button>

            </div>

            </form>

        </div>

    </div>

</div>
@endsection



