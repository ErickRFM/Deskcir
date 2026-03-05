@extends('layouts.app')

@section('title','Tickets | Técnico')

@section('content')

<div class="container py-4">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Mis Tickets</h3>
        <p class="text-muted mb-0">
            Gestiona incidencias asignadas
        </p>
    </div>

    <a href="/technician" class="btn btn-outline-deskcir py-2">
        ← Regresar
    </a>
</div>

{{-- FILTROS --}}
<form method="GET" action="{{ url()->current() }}">
<div class="card p-3 mb-4">
    <div class="row g-2">

        {{-- PRIORIDAD --}}
        <div class="col-md-3">
            <select name="priority" class="form-select">
                <option value="">Todas las prioridades</option>
                <option value="alta" {{ request('priority')=='alta'?'selected':'' }}>Alta</option>
                <option value="media" {{ request('priority')=='media'?'selected':'' }}>Media</option>
                <option value="baja" {{ request('priority')=='baja'?'selected':'' }}>Baja</option>
            </select>
        </div>

        {{-- ESTADO --}}
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Todos los estados</option>
                <option value="pendiente" {{ request('status')=='pendiente'?'selected':'' }}>Pendiente</option>
                <option value="en_proceso" {{ request('status')=='en_proceso'?'selected':'' }}>En proceso</option>
                <option value="cerrado" {{ request('status')=='cerrado'?'selected':'' }}>Cerrado</option>
            </select>
        </div>

        {{-- BUSCADOR --}}
        <div class="col-md-4">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Buscar por cliente o asunto">
        </div>

        {{-- BOTÓN --}}
        <div class="col-md-2">
            <button type="submit" class="btn btn-deskcir w-100">
                Filtrar
            </button>
        </div>

    </div>
</div>
</form>

{{-- LISTA DE TICKETS --}}
<div class="card">
<table class="table align-middle mb-0">

<thead>
<tr>
    <th>Cliente</th>
    <th>Asunto</th>
    <th>Prioridad</th>
    <th>Estado</th>
    <th>Actualizado</th>
    <th></th>
</tr>
</thead>

<tbody>

@forelse($tickets as $t)

<tr>

<td>
<strong>{{ $t->user->name }}</strong>
</td>

<td>
{{ $t->subject }}
</td>

<td>
<span class="badge bg-{{
$t->priority=='alta'?'danger':
($t->priority=='media'?'warning':'secondary')
}}">
{{ ucfirst($t->priority) }}
</span>
</td>

<td>
<span class="badge bg-{{
$t->status=='cerrado'?'success':
($t->status=='en_proceso'?'warning':'secondary')
}}">
{{ str_replace('_',' ',$t->status) }}
</span>
</td>

<td>
{{ $t->updated_at->diffForHumans() }}
</td>

<td class="text-end">

    <div class="d-flex justify-content-end gap-2">

        <a href="/technician/tickets/{{ $t->id }}"
           class="btn btn-deskcir py-2 px-2">
            Atender
        </a>

        <a href="{{ route('technician.checklist',$t->id) }}"
           class="btn btn-outline-deskcir py-2 px-2">
            Checklist Técnico
        </a>

    </div>

</td>

</tr>

@empty

<tr>
<td colspan="6" class="text-center text-muted py-4">
No tienes tickets asignados
</td>
</tr>

@endforelse

</tbody>
</table>
</div>

</div>

{{-- ================= AUTO REFRESH INTELIGENTE ================= --}}
<script>
(function(){

    let refreshTime = 10000; // 10 segundos
    let refreshTimer = null;

    function startAutoRefresh(){

        if(refreshTimer) return;

        refreshTimer = setInterval(function(){

            // ❌ No refrescar si el usuario cambió de pestaña
            if(document.hidden) return;

            // ❌ No refrescar si está escribiendo en el buscador
            const active = document.activeElement;
            if(active && active.tagName === "INPUT") return;

            // ✅ mantener filtros actuales
            window.location.href = window.location.href;

        }, refreshTime);
    }

    function stopAutoRefresh(){
        if(refreshTimer){
            clearInterval(refreshTimer);
            refreshTimer = null;
        }
    }

    // Pausa si cambia de pestaña
    document.addEventListener("visibilitychange", function(){
        if(document.hidden){
            stopAutoRefresh();
        }else{
            startAutoRefresh();
        }
    });

    // Iniciar
    startAutoRefresh();

})();
</script>

@endsection 