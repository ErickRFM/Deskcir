@extends('layouts.app')

@section('content')

<h2>Carrito</h2>

@if(empty($cart))
    <p>Carrito vacío</p>
@else

<table class="table">
<thead>
<tr>
    <th>Producto</th>
    <th>Cantidad</th>
    <th>Precio</th>
    <th></th>
</tr>
</thead>

<tbody>
@foreach($cart as $id => $item)
<tr>
    <td>{{ $item['name'] }}</td>

    <td>{{ $item['qty'] }}</td>

    <td>${{ $item['price'] * $item['qty'] }}</td>

    <td>
        <form method="POST" action="/cart/remove/{{ $id }}">
            @csrf
            <button class="btn btn-sm btn-danger">
                🗑️ Quitar
            </button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

<a href="/checkout" class="btn btn-success">
    Finalizar pedido
</a>

@endif

@endsection