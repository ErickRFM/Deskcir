@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="fw-bold mt-2">Nuevo Usuario</h3>

        <div class="d-flex gap-3">

            {{-- BOTÓN REGRESAR --}}
            <a href="javascript:history.back()" class="btn btn-outline-deskcir py-2">
                ← Regresar
            </a>

        </div>

    </div>


    {{-- CARD FORM --}}
    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="row g-3">

                {{-- NOMBRE --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Nombre
                    </label>

                    <input name="name"
                           class="form-control"
                           placeholder="Ej: Juan Pérez">

                </div>


                {{-- EMAIL --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Email
                    </label>

                    <input name="email"
                           class="form-control"
                           placeholder="usuario@email.com">

                </div>


                {{-- PASSWORD --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Contraseña
                    </label>

                    <input name="password"
                           type="password"
                           class="form-control"
                           placeholder="********">

                </div>


                {{-- ROL --}}
                <div class="col-md-6">

                    <label class="form-label fw-semibold">
                        Rol
                    </label>

                    <select name="role_id" class="form-select">

                        @foreach($roles as $r)

                        <option value="{{ $r->id }}">
                            {{ ucfirst($r->name) }}
                        </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- BOTONES --}}
            <div class="mt-4 d-flex gap-3">

                <button class="btn btn-deskcir px-4 py-2">
                    Guardar usuario
                </button>

                
            </div>

            </form>

        </div>

    </div>

</div>
@endsection