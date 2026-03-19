<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de servicio Deskcir</title>
    @include('pdf.partials.document-styles')
</head>
<body>
@php
    $checklist = $ticket->checklist;
@endphp

<div class="document-shell">
    @include('pdf.partials.document-header', [
        'title' => 'Reporte de Servicio',
        'subtitle' => 'Documento de seguimiento tecnico con informacion del caso, evidencia operativa y bitacora de atencion.',
    ])

    <table class="summary-table" cellpadding="0" cellspacing="10">
        <tr>
            <td class="summary-card">
                <span class="summary-label">Ticket</span>
                <span class="summary-value">#{{ $ticket->id }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Cliente</span>
                <span class="summary-value" style="font-size: 13px;">{{ optional($ticket->user)->name ?? 'Sin cliente' }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Tecnico</span>
                <span class="summary-value" style="font-size: 13px;">{{ optional($ticket->technician)->name ?? 'Sin asignar' }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Estado</span>
                <span class="summary-value" style="font-size: 13px;">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
            </td>
        </tr>
    </table>

    <div class="section-block">
        <h3 class="section-title">Resumen del caso</h3>
        <table class="meta-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 30%;">
                    <span class="meta-label">Asunto</span>
                    <span class="meta-value">{{ $ticket->subject }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="meta-label">Prioridad</span>
                    <span class="meta-value">{{ ucfirst($ticket->priority ?? 'media') }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="meta-label">Checklist</span>
                    <span class="meta-value">{{ ucfirst(str_replace('_', ' ', optional($checklist)->status ?? 'pendiente')) }}</span>
                </td>
                <td style="width: 30%;">
                    <span class="meta-label">Fecha de apertura</span>
                    <span class="meta-value">{{ optional($ticket->created_at)->format('d/m/Y H:i') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-block">
        <h3 class="section-title">Descripcion del servicio</h3>
        <div class="note-box">
            {{ $ticket->description ?: 'El cliente no agrego una descripcion adicional al ticket.' }}
        </div>
    </div>

    <div class="section-block">
        <h3 class="section-title">Indicadores de atencion</h3>
        <table class="meta-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <span class="meta-label">Adjuntos del cliente</span>
                    <span class="meta-value">{{ $ticket->files->count() }}</span>
                </td>
                <td>
                    <span class="meta-label">Servicios agendados</span>
                    <span class="meta-value">{{ $ticket->appointments->count() }}</span>
                </td>
                <td>
                    <span class="meta-label">Movimientos en bitacora</span>
                    <span class="meta-value">{{ $logs->count() }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-block">
        <h3 class="section-title">Bitacora del servicio</h3>
        <table class="data-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 20%;">Fecha</th>
                    <th style="width: 22%;">Responsable</th>
                    <th style="width: 58%;">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ optional($log->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ optional($log->user)->name ?? 'Deskcir' }}</td>
                        <td>{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state">No hay eventos registrados en la bitacora para este ticket.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('pdf.partials.document-footer')
</div>
</body>
</html>
