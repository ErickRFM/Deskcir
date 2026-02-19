@extends('layouts.app')

@section('title','Técnico | Deskcir')

@section('content')

<div class="container py-5">

    {{-- HEADER --}}
    <div class="mb-5">
        <h3 class="fw-bold mb-1">👨‍🔧 Panel del Técnico</h3>
        <p class="text-muted">
            Control de tickets, agenda y seguimiento de servicios
        </p>
    </div>

    {{-- KPIS --}}
    <div class="row g-4 mb-5">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">🎫 Asignados</h6>
                    <h2 class="fw-bold mb-0">{{ $asignados ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">🚧 En proceso</h6>
                    <h2 class="fw-bold mb-0">{{ $proceso ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">✅ Cerrados</h6>
                    <h2 class="fw-bold mb-0">{{ $cerrados ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-2">📅 Hoy</h6>
                    <h2 class="fw-bold mb-0">{{ $hoy ?? 0 }}</h2>
                </div>
            </div>
        </div>

    </div>

    {{-- ACCESOS DIRECTOS --}}
    <div class="row g-4 mb-5">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-1">🎫 Tickets asignados</h5>

                    <p class="text-muted mb-4">
                        Gestiona incidencias, responde al cliente y sube evidencias
                    </p>

                    <a href="/technician/tickets"
                       class="btn btn-primary w-100 py-2">
                        Ir a tickets
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-1">📅 Agenda</h5>

                    <p class="text-muted mb-4">
                        Citas y servicios programados
                    </p>

                    <a href="/technician/calendar"
                       class="btn btn-success w-100 py-2">
                        Ver agenda
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- ACTIVIDAD RECIENTE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h5 class="fw-bold mb-4">🕒 Actividad reciente</h5>

            @if(empty($recientes) || count($recientes) == 0)

                <div class="text-center py-4 text-muted">
                    Sin actividad reciente
                </div>

            @else

                @foreach($recientes as $r)

                    <div class="border-bottom pb-3 mb-3">

                        <div class="d-flex justify-content-between">
                            <strong>{{ $r->subject }}</strong>

                            <small class="text-muted">
                                {{ $r->updated_at }}
                            </small>
                        </div>

                    </div>

                @endforeach

            @endif

        </div>
    </div>

</div>

@endsection