<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Checklist tecnico Deskcir</title>
    @include('pdf.partials.document-styles')
</head>
<body>
<div class="document-shell">
    @include('pdf.partials.document-header', [
        'title' => 'Checklist Tecnico',
        'subtitle' => 'Control documentado de diagnostico, reparacion, pruebas y evidencia visual del servicio.',
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
                <span class="summary-value" style="font-size: 13px;">{{ ucfirst(str_replace('_', ' ', optional($checklist)->status ?? 'pendiente')) }}</span>
            </td>
        </tr>
    </table>

    <div class="section-block">
        <h3 class="section-title">Progreso del servicio</h3>
        <table class="meta-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <span class="meta-label">Diagnostico</span>
                    <span class="meta-value">{{ optional($checklist)->diagnostico ? 'Completado' : 'Pendiente' }}</span>
                </td>
                <td>
                    <span class="meta-label">Reparacion</span>
                    <span class="meta-value">{{ optional($checklist)->reparacion ? 'Completada' : 'Pendiente' }}</span>
                </td>
                <td>
                    <span class="meta-label">Pruebas</span>
                    <span class="meta-value">{{ optional($checklist)->pruebas ? 'Completadas' : 'Pendientes' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-block">
        <h3 class="section-title">Diagnostico realizado</h3>
        <div class="note-box">{{ optional($checklist)->diagnostico_notes ?: 'Sin informacion registrada.' }}</div>
    </div>

    <div class="section-block">
        <h3 class="section-title">Reparacion aplicada</h3>
        <div class="note-box">{{ optional($checklist)->reparacion_notes ?: 'Sin informacion registrada.' }}</div>
    </div>

    <div class="section-block">
        <h3 class="section-title">Pruebas finales</h3>
        <div class="note-box">{{ optional($checklist)->pruebas_notes ?: 'Sin informacion registrada.' }}</div>
    </div>

    <div class="section-block">
        <h3 class="section-title">Observaciones adicionales</h3>
        <table class="meta-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%;">
                    <span class="meta-label">Errores encontrados</span>
                    <span class="meta-value">{{ optional($checklist)->errores ?: 'Sin errores reportados.' }}</span>
                </td>
                <td style="width: 50%;">
                    <span class="meta-label">Observaciones</span>
                    <span class="meta-value">{{ optional($checklist)->observaciones ?: 'Sin observaciones adicionales.' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-block">
        <h3 class="section-title">Evidencia fotografica</h3>
        @if(optional($checklist)->photos && $checklist->photos->count())
            <table class="photo-grid" cellpadding="0" cellspacing="0">
                @foreach($checklist->photos->chunk(2) as $photoRow)
                    <tr>
                        @foreach($photoRow as $photo)
                            @php
                                $disk = $photo->disk ?: 'public';
                                $photoPath = null;

                                try {
                                    $photoPath = \Illuminate\Support\Facades\Storage::disk($disk)->path($photo->path);
                                } catch (\Throwable $e) {
                                    $photoPath = null;
                                }
                            @endphp
                            <td>
                                <div class="photo-card">
                                    @if($photoPath && file_exists($photoPath))
                                        <img src="{{ $photoPath }}" alt="Foto del checklist">
                                    @else
                                        <div class="empty-state">No se pudo cargar la imagen almacenada.</div>
                                    @endif
                                    <div class="muted">Archivo: {{ basename((string) $photo->path) }}</div>
                                </div>
                            </td>
                        @endforeach
                        @if($photoRow->count() === 1)
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @else
            <div class="empty-state">No se adjuntaron fotografias en este checklist.</div>
        @endif
    </div>

    @include('pdf.partials.document-footer')
</div>
</body>
</html>
