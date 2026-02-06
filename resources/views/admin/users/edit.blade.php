@extends('layouts.app')

@section('content')
<div class="container">

<h3>Editar Usuario</h3>

<form method="POST"
 action="{{ route('admin.users.update',$user->id) }}">
@csrf
@method('PUT')

<input name="name" value="{{ $user->name }}"
 class="form-control mb-2">

<input name="email" value="{{ $user->email }}"
 class="form-control mb-2">

<input name="password"
 placeholder="Nueva contraseña (opcional)"
 class="form-control mb-2">

<select name="role_id" class="form-control mb-2">
@foreach($roles as $r)
<option value="{{ $r->id }}"
 {{ $user->role_id == $r->id ? 'selected' : '' }}>
 {{ $r->name }}
</option>
@endforeach
</select>

<button class="btn btn-primary">Actualizar</button>

</form>

</div>
@endsection
