@extends('layouts.app')

@section('content')
<div class="container py-4">

<h3>Gestión de Usuarios</h3>

<a href="{{ route('admin.users.create') }}" class="btn btn-warning mb-3">
    ➕ Nuevo usuario
</a>

<table class="table">
<tr>
    <th>Nombre</th>
    <th>Email</th>
    <th>Rol</th>
    <th>Acciones</th>
</tr>

@foreach($users as $u)
<tr>
    <td>{{ $u->name }}</td>
    <td>{{ $u->email }}</td>
    <td>{{ $u->role->name ?? 'sin rol' }}</td>

    <td>
        <a href="{{ route('admin.users.edit',$u->id) }}"
           class="btn btn-sm btn-primary">✏️</a>

        <form action="{{ route('admin.users.destroy',$u->id) }}"
              method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">🗑️</button>
        </form>
    </td>
</tr>
@endforeach

</table>

</div>
@endsection