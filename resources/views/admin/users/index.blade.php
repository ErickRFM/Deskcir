@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="mb-4">

        {{-- BOTÓN REGRESAR --}}
        <a href="javascript:history.back()" class="btn btn-outline-secondary mb-3">
            ← Regresar
        </a>

        <h3 class="mb-3">Gestión de Usuarios</h3>

        <a href="{{ route('admin.users.create') }}" class="btn btn-warning">
            ➕ Nuevo usuario
        </a>

    </div>

    <div class="table-responsive">
        <table class="table align-middle mt-3">

            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>

            @foreach($users as $u)
            <tr>

                <td class="py-3">
                    {{ $u->name }}
                </td>

                <td class="py-3">
                    {{ $u->email }}
                </td>

                <td class="py-3">
                    {{ $u->role->name ?? 'sin rol' }}
                </td>

                <td class="text-center py-3">

                    <a href="{{ route('admin.users.edit',$u->id) }}"
                       class="btn btn-sm btn-primary">
                        ✏️
                    </a>

                    <form action="{{ route('admin.users.destroy',$u->id) }}"
                          method="POST"
                          class="d-inline">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-danger">
                            🗑️
                        </button>

                    </form>

                </td>

            </tr>
            @endforeach

            </tbody>

        </table>
    </div>

</div>
@endsection