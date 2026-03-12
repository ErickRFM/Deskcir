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

<h1>Checklist Tecnico</h1>

<div class="section">
<strong>Ticket:</strong> #{{ $ticket->id }} <br>
<strong>Cliente:</strong> {{ $ticket->user->name ?? 'Cliente' }}
</div>

<div class="section">
<div class="title">Diagnostico realizado</div>
<div class="box">
{{ $checklist->diagnostico_notes ?? 'Sin informacion' }}
</div>
</div>

<div class="section">
<div class="title">Reparacion aplicada</div>
<div class="box">
{{ $checklist->reparacion_notes ?? 'Sin informacion' }}
</div>
</div>

<div class="section">
<div class="title">Pruebas finales</div>
<div class="box">
{{ $checklist->pruebas_notes ?? 'Sin informacion' }}
</div>
</div>

@if(!empty($checklist->errores))
<div class="section">
<div class="title">Errores encontrados</div>
<div class="box">{{ $checklist->errores }}</div>
</div>
@endif

@if(!empty($checklist->observaciones))
<div class="section">
<div class="title">Observaciones</div>
<div class="box">{{ $checklist->observaciones }}</div>
</div>
@endif

@if($ticket->images && count($ticket->images))
<div class="section">
<div class="title">Imagenes</div>
@foreach($ticket->images as $img)
    <img src="{{ public_path('storage/'.$img->path) }}" alt="imagen">
@endforeach
</div>
@endif

</body>
</html>