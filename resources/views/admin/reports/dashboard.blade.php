@extends('layouts.app')

@section('content')

<div class="container py-4">

<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3">
← Regresar
</a>

<h3>📊 Reportes Desckir</h3>

{{-- KPIS --}}
<div class="row g-4 mt-3">

<div class="col-md-3">
<div class="card p-3">
<h6>Ingresos</h6>
<h3>${{ number_format($total,2) }}</h3>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6>Pedidos</h6>
<h3>{{ $pedidos }}</h3>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6>Ticket promedio</h6>
<h3>${{ number_format($ticket,2) }}</h3>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6>Entregados</h6>
<h3>{{ $entregados }}</h3>
</div>
</div>

</div>

{{-- GRAFICA --}}
<div class="card p-3 mt-4">
<h5>Ventas últimos 30 días</h5>

<canvas id="ventas"></canvas>
</div>

{{-- TOP --}}
<div class="card p-3 mt-4">
<h5>Más vendidos</h5>

@foreach($top as $p)
<p>{{ $p->name }} - {{ $p->order_items_count }}</p>
@endforeach

</div>

<a href="/admin/reports/export/excel" class="btn btn-success mt-3">
Excel
</a>

<a href="/admin/reports/export/pdf" class="btn btn-danger mt-3">
PDF
</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(document.getElementById('ventas'),{

type:'line',

data:{

labels:[

@foreach($dias as $d)
'{{$d->dia}}',
@endforeach

],

datasets:[{

label:'Ventas',

data:[

@foreach($dias as $d)
{{$d->total}},
@endforeach

]

}]

}

})

</script>

@endsection