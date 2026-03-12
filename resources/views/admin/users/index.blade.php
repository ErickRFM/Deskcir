@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold mt-2">Gestion de Usuarios</h3>

      <div class="d-flex gap-3">
        <a href="javascript:history.back()" class="btn btn-outline-deskcir py-2">
            Regresar
        </a>

        <a href="{{ route('admin.users.create') }}" class="btn btn-deskcir py-2">
            Nuevo usuario
        </a>
      </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-4">Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th class="text-center px-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="px-4 py-3 fw-semibold">{{ $u->name }}</td>
                        <td class="py-3 text-muted">{{ $u->email }}</td>
                        <td class="py-3">
                            @if($u->role)
                                <span class="badge bg-secondary">{{ ucfirst($u->role->name) }}</span>
                            @else
                                <span class="badge bg-warning text-dark">Sin rol</span>
                            @endif
                        </td>
                        <td class="text-center py-3 px-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.users.edit',$u->id) }}" class="btn btn-sm btn-primary">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route('admin.users.destroy',$u->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
