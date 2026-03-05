<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
font-family: DejaVu Sans, sans-serif;
font-size:12px;
color:#333;
}

h1{
font-size:18px;
margin-bottom:20px;
}

.section{
margin-bottom:20px;
}

.title{
font-weight:bold;
margin-bottom:5px;
}

.box{
border:1px solid #ccc;
padding:8px;
min-height:40px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

td{
padding:6px;
vertical-align:top;
}

img{
width:150px;
margin:5px;
border:1px solid #ccc;
padding:3px;
}

</style>

</head>

<body>

<h1>Checklist Técnico</h1>

<div class="section">
<strong>Ticket:</strong> #{{ $ticket->id }} <br>
<strong>Cliente:</strong> {{ $ticket->user->name ?? 'Cliente' }}
</div>


{{-- DIAGNOSTICO --}}
<div class="section">

<div class="title">Diagnóstico realizado</div>

<div class="box">
{{ $checklist->diagnostico_notes ?? 'Sin información' }}
</div>

</div>


{{-- REPARACION --}}
<div class="section">

<div class="title">Reparación aplicada</div>

<div class="box">
{{ $checklist->reparacion_notes ?? 'Sin información' }}
</div>

</div>


{{-- PRUEBAS --}}
<div class="section">

<div class="title">Pruebas finales</div>

<div class="box">
{{ $checklist->pruebas_notes ?? 'Sin información' }}
</div>

</div>


{{-- ERRORES --}}
<div class="section">

<div class="title">Errores detectados</div>

<div class="box">
{{ $checklist->errores ?? 'Sin información' }}
</div>

</div>


{{-- OBSERVACIONES --}}
<div class="section">

<div class="title">Observaciones técnicas</div>

<div class="box">
{{ $checklist->observaciones ?? 'Sin información' }}
</div>

</div>


{{-- FOTOS --}}
@if($checklist && $checklist->photos && $checklist->photos->count())

<div class="section">

<div class="title">Fotos del servicio</div>

@foreach($checklist->photos as $foto)

<img src="{{ public_path('storage/'.$foto->path) }}">

@endforeach

</div>

@endif

</body>
</html>