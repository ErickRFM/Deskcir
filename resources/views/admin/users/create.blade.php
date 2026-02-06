@extends('layouts.app')

@section('content')
<div class="container">

<h3>Nuevo Usuario</h3>

<form method="POST" action="{{ route('admin.users.store') }}">
@csrf

<input name="name" class="form-control mb-2" placeholder="Nombre">

<input name="email" class="form-control mb-2" placeholder="Email">

<input name="password" type="password"
       class="form-control mb-2" placeholder="Contraseña">

<select name="role_id" class="form-control mb-2">
@foreach($roles as $r)
<option value="{{ $r->id }}">{{ $r->name }}</option>
@endforeach
</select>

<button class="btn btn-success">Guardar</button>

</form>

</div>
@endsection
