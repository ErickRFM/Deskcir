@extends('layouts.app')

@section('content')

<div class="container py-4">
<div class="row">

<!-- ================= FORMULARIO ================= -->
<div class="col-md-8">

<h3 class="mb-3">🛒 Finalizar compra</h3>

<form method="POST" action="/checkout">
@csrf

<div class="card p-3 mb-3">
<h5> Datos de envío</h5>

<input name="address" class="form-control mb-2" placeholder="Dirección" required>
<input name="city" class="form-control mb-2" placeholder="Ciudad" required>
<input name="postal_code" class="form-control mb-2" placeholder="Código postal" required>
<input name="phone" class="form-control mb-2" placeholder="Teléfono" required>
</div>

<div class="card p-3 mb-3">
<h5> Método de pago</h5>

<select name="payment_method" id="metodo" class="form-control">
<option value="card">Tarjeta</option>
<option value="transfer">Transferencia</option>
<option value="cash">Efectivo</option>
<option value="bitcoin">Bitcoin</option>
</select>
</div>

<!-- ========== TARJETA ========== -->
<div id="cardBox" class="card p-3 mb-3 d-none">
<h5> Datos de tarjeta</h5>

<input class="form-control mb-2" placeholder="Número de tarjeta">
<input class="form-control mb-2" placeholder="Nombre del titular">

<div class="row">
<div class="col">
<input class="form-control" placeholder="MM/YY">
</div>

<div class="col">
<input class="form-control" placeholder="CVV">
</div>
</div>
</div>

<!-- ========== TRANSFERENCIA ========== -->
<div id="transferBox" class="card p-3 mb-3 d-none">
<h5>Transferencia</h5>

<p>CLABE: <b>012345678901234567</b></p>
<p>Banco: BANAMEX</p>
<p>Referencia: <b>#{{ rand(1000,9999) }}</b></p>

<small>Tu pedido se liberará al confirmar pago</small>
</div>

<!-- ========== BITCOIN ========== -->
<div id="btcBox" class="card p-3 mb-3 d-none">
<h5>₿ Bitcoin</h5>

<p>Dirección:</p>
<code>bc1qdeskcirficticia12345</code>

<p class="mt-2">Red: Bitcoin</p>
<p>Tiempo límite: 30 min</p>
</div>

<button class="btn btn-success w-100">
 Confirmar compra
</button>

</form>

</div>

<!-- ================= RESUMEN ================= -->
<div class="col-md-4">

<div class="card p-3">
<h5> Resumen</h5>

@php
$cart = session('cart', []);
$total = 0;
@endphp

@foreach($cart as $item)

<p>
{{ $item['name'] }} x {{ $item['qty'] }}

<span class="float-end">
${{ $item['price'] * $item['qty'] }}
</span>
</p>

@php
$total += $item['price'] * $item['qty'];
@endphp

@endforeach

<hr>

<h4>Total: ${{ $total }}</h4>

</div>
</div>

</div>
</div>

<script>
const metodo = document.getElementById('metodo')

const cardBox = document.getElementById('cardBox')
const transferBox = document.getElementById('transferBox')
const btcBox = document.getElementById('btcBox')

metodo.addEventListener('change', () => {

cardBox.classList.add('d-none')
transferBox.classList.add('d-none')
btcBox.classList.add('d-none')

if(metodo.value === 'card')
cardBox.classList.remove('d-none')

if(metodo.value === 'transfer')
transferBox.classList.remove('d-none')

if(metodo.value === 'bitcoin')
btcBox.classList.remove('d-none')

})
</script>

@endsection