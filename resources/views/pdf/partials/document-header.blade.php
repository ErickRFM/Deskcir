@php
    $logoPath = public_path('img/logo.png');
    $generatedAt = $generatedAt ?? now();
@endphp

<table class="doc-header" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width: 120px;">
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Deskcir" class="doc-logo">
            @endif
        </td>
        <td>
            <div class="doc-kicker">Deskcir Document Center</div>
            <div class="doc-title">{{ $title ?? 'Documento Deskcir' }}</div>
            <div class="doc-subtitle">{{ $subtitle ?? 'Documento generado por Deskcir para control operativo, trazabilidad y respaldo comercial.' }}</div>
        </td>
        <td style="width: 180px;" class="doc-stamp">
            <strong>Deskcir</strong><br>
            Soporte tecnico, tienda y operaciones<br>
            Generado: {{ $generatedAt->format('d/m/Y H:i') }}
        </td>
    </tr>
</table>
