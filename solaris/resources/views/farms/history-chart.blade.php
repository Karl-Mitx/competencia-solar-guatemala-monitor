@php
    $chartRows = $history->map(fn ($record) => [
        'period' => $record->period->format('Y-m'),
        'real_kwh' => (float) $record->real_kwh,
        'expected_kwh' => (float) $record->expected_kwh,
    ])->values()->all();
@endphp
<section class="card chart-card" style="margin-top:24px" aria-labelledby="farm-chart-title">
    <h2 id="farm-chart-title">Energía real y esperada por mes</h2><p>Historial de {{ $farm->name }}. Las cifras completas están en la tabla de generación histórica.</p>
    @if(count($chartRows))
        <div class="chart-legend"><span><i class="legend-dot green"></i> Generación real</span><span><i class="legend-line"></i> Generación esperada</span></div>
        <div class="chart-container"><canvas data-chart="generation" data-source="farm-history-data" role="img" aria-label="Evolución mensual de generación real y esperada de {{ $farm->name }}"></canvas></div>
        <script type="application/json" id="farm-history-data">{!! json_encode($chartRows, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
        <noscript><p>La tabla contiene los mismos datos sin necesidad de JavaScript.</p></noscript>
    @else
        <p>Aún no hay registros para dibujar la evolución de esta granja.</p>
    @endif
</section>
