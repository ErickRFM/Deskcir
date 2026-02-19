<h2>Reporte Ventas</h2>

<table border="1">

<tr>
<th>ID</th>
<th>Cliente</th>
<th>Total</th>
<th>Estado</th>
</tr>

@foreach($orders as $o)

<tr>
<td>{{$o->id}}</td>
<td>{{$o->user->name}}</td>
<td>{{$o->total}}</td>
<td>{{$o->status}}</td>
</tr>

@endforeach

</table>