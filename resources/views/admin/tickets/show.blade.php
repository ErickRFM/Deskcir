@extends('layouts.app')

@section('content')

<div class="container py-4">

<h4 class="mb-3">{{ $ticket->subject }}</h4>

{{-- ============================= --}}
{{-- ASIGNAR TÉCNICO (si no tiene) --}}
{{-- ============================= --}}
@if(!$ticket->technician_id)
<form method="POST" action="{{ route('admin.tickets.assign',$ticket->id) }}" class="mb-4">
@csrf

<div class="row g-2 align-items-end">
    <div class="col-md-6">
        <label class="form-label">Asignar a técnico</label>
        <select name="technician_id" class="form-select" required>
            <option value="">Seleccionar técnico</option>
            @foreach($technicians as $tec)
                <option value="{{ $tec->id }}">
                    {{ $tec->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <button class="btn btn-primary w-100">
            Asignar
        </button>
    </div>
</div>

</form>
@endif


{{-- ============================= --}}
{{-- ACTUALIZAR STATUS / PRIORIDAD --}}
{{-- ============================= --}}
<form method="POST" action="{{ route('admin.tickets.status',$ticket->id) }}">
@csrf

<div class="row g-2">

<div class="col-md-4">
<label class="form-label">Estado</label>
<select name="status" class="form-control input-pro">
<option value="pendiente" {{ $ticket->status=='pendiente'?'selected':'' }}>Pendiente</option>
<option value="en_proceso" {{ $ticket->status=='en_proceso'?'selected':'' }}>En proceso</option>
<option value="cerrado" {{ $ticket->status=='cerrado'?'selected':'' }}>Cerrado</option>
</select>
</div>

<div class="col-md-4">
<label class="form-label">Prioridad</label>
<select name="priority" class="form-control input-pro">
<option value="baja" {{ $ticket->priority=='baja'?'selected':'' }}>Baja</option>
<option value="media" {{ $ticket->priority=='media'?'selected':'' }}>Media</option>
<option value="alta" {{ $ticket->priority=='alta'?'selected':'' }}>Alta</option>
</select>
</div>

{{-- mostrar técnico asignado --}}
@if($ticket->technician)
<div class="col-md-4">
<label class="form-label">Técnico asignado</label>
<input type="text"
class="form-control"
value="{{ $ticket->technician->name }}"
disabled>
</div>
@endif

</div>

<button class="btn btn-warning mt-3">
Actualizar
</button>

</form>


{{-- ============================= --}}
{{-- CHAT DEL TICKET --}}
{{-- ============================= --}}
<div class="mt-4">
<x-chat
:ticket="$ticket"
action="{{ route('admin.tickets.reply',$ticket->id) }}" />
</div>

</div>

@endsection