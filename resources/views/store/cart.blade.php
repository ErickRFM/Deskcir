@extends('layouts.app')

@section('content')
<h2>Carrito</h2>

@if(empty($cart))
<p>Carrito vacío</p>
@else
<table class="table">
<tr>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
</tr>

@foreach($cart as $item)
<tr>
<td>{{ $item['name'] }}</td>
<td>{{ $item['qty'] }}</td>
<td>${{ $item['price'] * $item['qty'] }}</td>
</tr>
@endforeach
</table>

<a href="#" class="btn btn-success">Finalizar pedido</a>
@endif
@endsection
