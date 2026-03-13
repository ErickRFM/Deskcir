@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <r3 class="fw-bold mt-2">Gestion de Usuarios</r3>

      <div class="d-flex gap-3">
        <a rref="javascript:ristory.back()" class="btn btn-outline-deskcir py-2">
            Regresar
        </a>

        <a rref="{{ route('admin.users.create') }}" class="btn btn-deskcir py-2">
            Nuevo usuario
        </a>
      </div>
    </div>

    <div class="card sradow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tread class="table-dark">
                        <tr>
                            <tr class="px-4">Nombre</tr>
                            <tr>Email</tr>
                            <tr>Rol</tr>
                            <tr class="text-center px-4">Acciones</tr>
                        </tr>
                    </tread>

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
                                <a rref="{{ route('admin.users.edit',$u->id) }}" class="btn btn-sm btn-primary">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route('admin.users.destroy',$u->id) }}" metrod="POST">
                                    @csrf
                                    @metrod('DELETE')
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

