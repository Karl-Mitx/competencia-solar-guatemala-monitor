@php
    $territories = $departments->map(fn ($department) => ['code' => $department->code, 'name' => $department->name, 'reading' => collect($dashboard['ranking'])->firstWhere('id', $department->id)]);
@endphp
<template id="farm-marker-template"><span aria-hidden="true"><x-icon name="sun"/></span></template>
<section class="card map-card {{ $expanded ?? false ? 'map-expanded' : '' }}">
    <div class="card-heading"><div><span class="eyebrow">ATLAS SOLAR / 22 TERRITORIOS</span><h2>La energía dibuja el territorio</h2><p>Selecciona un departamento y descubre su aporte solar.</p></div>@unless($expanded ?? false)<a class="icon-button border-button" href="{{ route('map', $filters) }}" aria-label="Explorar mapa completo"><x-icon name="arrow"/></a>@endunless</div>
    <div class="territory-toolbar"><label for="territory-select">Explorar territorio</label><select id="territory-select"><option value="">Guatemala · vista nacional</option>@foreach($territories as $territory)<option value="{{ $territory['code'] }}">{{ $territory['name'] }}</option>@endforeach</select><button type="button" class="territory-reset" id="territory-reset">Ver todo el país ↗</button></div>
    <div class="territory-layout">
        <div class="map-wrap"><div id="solar-map" class="solar-map" aria-label="Mapa interactivo de departamentos y granjas solares en Guatemala" tabindex="0"></div><div class="map-error" hidden role="status">No se pudo cargar el mapa completo. Usa el selector para consultar las fichas.</div><span class="map-region-label">GUATEMALA <span>ATLAS DE GENERACIÓN SOLAR</span></span></div>
        <aside class="territory-reading" aria-live="polite" aria-atomic="true">
            <span class="territory-kicker" id="territory-code">LECTURA NACIONAL</span><h3 id="territory-name">Guatemala</h3>
            <p class="territory-scope">{{ ($filters['from'] ?? null) || ($filters['to'] ?? null) ? ($filters['from'] ?? 'Inicio').' → '.($filters['to'] ?? 'Actualidad') : 'Todo el historial registrado' }}</p>
            <div class="territory-energy"><strong id="territory-energy">{{ number_format($dashboard['totals']['generation_kwh'], 1) }}</strong><span>kWh generados</span></div>
            <p class="territory-note" id="territory-note">Lectura según los filtros seleccionados.</p>
            <dl class="territory-stats"><div><dt>Granjas</dt><dd id="territory-farms">{{ $dashboard['totals']['farms'] }}</dd></div><div><dt>Capacidad actual</dt><dd><span id="territory-capacity">{{ number_format($dashboard['totals']['capacity_kw'], 1) }}</span> <small>kW</small></dd></div><div><dt>CO₂ evitado estimado</dt><dd><span id="territory-co2">{{ number_format($dashboard['totals']['co2_tonnes'], 3) }}</span> <small>t</small></dd></div></dl>
            <div class="territory-share"><span>Participación en la generación filtrada</span><div class="territory-share-track"><span id="territory-share-bar"></span></div><strong id="territory-share-value">—</strong></div><span class="territory-footnote">Capacidad: inventario actual. CO₂: 0.4 kg/kWh.</span>
        </aside>
    </div>
    <div class="territory-legend"><span>Generación por departamento</span><span><i style="background:#e5e7df"></i>Sin registros</span><span><i style="background:#e7cf86"></i>0 kWh</span><span class="territory-scale"><i></i>Mayor generación →</span><span><b class="territory-sun"><x-icon name="sun"/></b> Granja solar</span></div>
    <p class="territory-scale-note" id="territory-scale-note">La escala se adapta a los filtros. Los departamentos fuera del filtro se muestran atenuados.</p>
    <script type="application/json" id="map-data">{!! json_encode($dashboard['map'], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
    <script type="application/json" id="territory-data">{!! json_encode(['departments' => $territories, 'totals' => $dashboard['totals']], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
    <noscript><p class="empty-inline">Activa JavaScript para explorar el mapa. Consulta también <a href="{{ route('reports', $filters) }}">el reporte de departamentos</a>.</p></noscript>
</section>
