@extends('layouts.app')

@section('content')
<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h3 class="fw-bold mt-2">
Checklist Técnico — Ticket #{{ $ticket->id }}
</h3>

<div class="d-flex gap-3">

<a href="javascript:history.back()" class="btn btn-outline-deskcir py-2">
← Regresar
</a>

<a href="{{ route('technician.checklist.pdf',$ticket->id) }}"
class="btn btn-deskcir py-2">
Exportar PDF
</a>

</div>

</div>


<div class="card shadow-sm border-0">

<div class="card-body p-4">

<form method="POST"
action="{{ route('technician.checklist.save',$ticket->id) }}"
enctype="multipart/form-data">

@csrf

<div class="row g-4">

{{-- DIAGNOSTICO --}}
<div class="col-12">

<label class="form-check fw-semibold">

<input type="checkbox"
class="form-check-input me-2"
name="diagnostico"
{{ isset($checklist) && $checklist->diagnostico ? 'checked':'' }}>

Diagnóstico realizado

</label>

<textarea
name="diagnostico_notes"
class="form-control mt-2"
rows="3"
placeholder="Describe hallazgos técnicos...">{{ $checklist->diagnostico_notes ?? '' }}</textarea>

</div>


{{-- REPARACION --}}
<div class="col-12">

<label class="form-check fw-semibold">

<input type="checkbox"
class="form-check-input me-2"
name="reparacion"
{{ isset($checklist) && $checklist->reparacion ? 'checked':'' }}>

Reparación aplicada

</label>

<textarea
name="reparacion_notes"
class="form-control mt-2"
rows="3"
placeholder="Acciones realizadas...">{{ $checklist->reparacion_notes ?? '' }}</textarea>

</div>


{{-- PRUEBAS --}}
<div class="col-12">

<label class="form-check fw-semibold">

<input type="checkbox"
class="form-check-input me-2"
name="pruebas"
{{ isset($checklist) && $checklist->pruebas ? 'checked':'' }}>

Pruebas finales realizadas

</label>

<textarea
name="pruebas_notes"
class="form-control mt-2"
rows="3"
placeholder="Resultados de las pruebas...">{{ $checklist->pruebas_notes ?? '' }}</textarea>

</div>


{{-- ERRORES --}}
<div class="col-md-6">

<label class="form-label fw-semibold">
Errores detectados
</label>

<textarea
name="errores"
class="form-control"
rows="4">{{ $checklist->errores ?? '' }}</textarea>

</div>


{{-- OBSERVACIONES --}}
<div class="col-md-6">

<label class="form-label fw-semibold">
Observaciones técnicas
</label>

<textarea
name="observaciones"
class="form-control"
rows="4">{{ $checklist->observaciones ?? '' }}</textarea>

</div>


{{-- FOTOS --}}
<div class="col-12">

<label class="form-label fw-semibold">
Fotos del servicio
</label>

<input type="file"
name="fotos[]"
multiple
class="form-control">

</div>


{{-- STATUS --}}
<div class="col-md-6">

<label class="form-label fw-semibold">
Estado del ticket
</label>

<select name="status" class="form-select">

<option value="diagnostico"
{{ $ticket->status == 'diagnostico' ? 'selected':'' }}>
Diagnóstico
</option>

<option value="reparacion"
{{ $ticket->status == 'reparacion' ? 'selected':'' }}>
En reparación
</option>

<option value="finalizado"
{{ $ticket->status == 'finalizado' ? 'selected':'' }}>
Finalizado
</option>

</select>

</div>


</div>


<div class="mt-4 d-flex gap-3">

<button type="submit" class="btn btn-deskcir px-4 py-2">
Guardar / Actualizar
</button>

<button type="button"
onclick="limpiarChecklist()"
class="btn btn-outline-danger px-4 py-2">
Limpiar todo
</button>

</div>

</form>

</div>

</div>

</div>


<script>

function limpiarChecklist(){

if(confirm("¿Seguro que quieres limpiar todo el checklist?")){

document.querySelectorAll("textarea").forEach(t=>{
t.value="";
});

document.querySelectorAll("input[type=checkbox]").forEach(c=>{
c.checked=false;
});

}

}

</script>

@endsection