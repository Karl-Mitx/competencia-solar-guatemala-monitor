@extends('layouts.app')
@section('title', 'Proyecciones de generación')
@section('content')
@php
    $actuals = $history->keyBy(fn ($record) => $record->period->format('Y-m'));
    $ordered = $projections->sortBy('period');
    $upcoming = $ordered->filter(fn ($projection) => $projection->period->gte(now()->startOfMonth()))->take(3);
    $chartHistory = $history->sortBy('period')->map(fn ($record) => ['period' => $record->period->format('Y-m'), 'real_kwh' => (float) $record->real_kwh])->values();
    $chartForecast = $ordered->map(fn ($projection) => ['period' => $projection->period->format('Y-m'), 'projected_kwh' => (float) $projection->projected_kwh])->values();
@endphp
<div class="page-heading atlas-heading"><div><div class="eyebrow"><span class="eyebrow-line"></span> ATLAS SOLAR / HORIZONTE</div><h1>Del registro al horizonte<span class="heading-dot">.</span></h1><p>Explora lo observado, estima lo que viene y contrasta los resultados.</p></div><span class="pill neutral"><x-icon name="trend"/> Promedio diario de 3 meses</span></div>
<form class="filter-bar" method="GET"><div class="filter-heading"><x-icon name="farm"/><span>Granja solar</span></div><label class="farm-filter"><span class="sr-only">Seleccionar granja</span><select name="solar_farm_id" required><option value="">Selecciona una granja</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected($selectedFarm?->id == $farm->id)>{{ $farm->name }} · {{ $farm->department->name }}</option>@endforeach</select></label><button class="button button-dark button-sm">Explorar horizonte</button></form>
<div class="forecast-process" aria-label="Cómo interpretar las proyecciones">
    <div><span>01</span><div><strong>Observar</strong><small>Tres meses cerrados y consecutivos</small></div></div>
    <div><span>02</span><div><strong>Estimar</strong><small>Promedio diario × días del mes</small></div></div>
    <div><span>03</span><div><strong>Contrastar</strong><small>Primera estimación frente al registro real</small></div></div>
</div>
<div class="projection-grid atlas-projections">
    <div>
    @if($selectedFarm)
        @if($upcoming->isNotEmpty())
        <section class="forecast-months" aria-label="Próximas proyecciones guardadas">
        @foreach($upcoming as $projection)
            <article><span class="eyebrow">{{ $loop->first ? 'PRÓXIMO PERÍODO GUARDADO' : 'PROYECCIÓN GUARDADA' }}</span><h2>{{ $projection->period->translatedFormat('F Y') }}</h2><strong>{{ number_format($projection->projected_kwh, 0) }} <small>kWh</small></strong><span class="forecast-baseline">Base hasta {{ $projection->training_through->format('m/Y') }}</span></article>
        @endforeach
        </section>
        <section class="card forecast-summary" aria-label="Resumen del horizonte">
            <div><span class="eyebrow">HORIZONTE GUARDADO</span><strong>{{ number_format($forecastSummary['total_kwh'], 0) }} <small>kWh proyectados</small></strong><p>{{ $forecastSummary['months'] }} meses disponibles para esta granja.</p></div>
            <div><span class="eyebrow">VARIABILIDAD HISTÓRICA</span><strong>{{ $forecastSummary['volatility'] === null ? '—' : number_format($forecastSummary['volatility'], 1).'%' }}</strong><p>{{ $forecastSummary['confidence'] }} · no es un intervalo de garantía.</p></div>
        </section>
        @endif
        <section class="card chart-card forecast-chart-card">
            <div class="card-heading"><div><span class="eyebrow">TRAYECTORIA ENERGÉTICA</span><h2>{{ $selectedFarm->name }}</h2><p>{{ $selectedFarm->department->name }} / Energía mensual en kWh</p></div><span class="pill {{ $selectedFarm->is_active ? 'positive' : 'neutral' }}">{{ $selectedFarm->is_active ? 'Granja activa' : 'Granja inactiva' }}</span></div>
            @if($history->isNotEmpty() || $projections->isNotEmpty())
                <div class="chart-legend"><span><i class="legend-dot green"></i> Observado</span><span><i class="legend-dot amber-bg"></i> Estimado · línea discontinua</span></div>
                <div class="chart-container projection-chart"><canvas data-chart="projection" data-source="projection-data" role="img" aria-label="Historial y proyecciones mensuales. Valores disponibles en la tabla inferior."></canvas></div>
                <script type="application/json" id="projection-data">{!! json_encode(['history' => $chartHistory, 'forecast' => $chartForecast], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
                <noscript><p class="empty-inline">Los valores de las proyecciones están disponibles en la tabla inferior.</p></noscript>
            @else
                <div class="empty-state"><x-icon name="trend"/><h3>El horizonte empieza con un registro</h3><p>Se necesitan los tres meses cerrados inmediatamente anteriores para estimar.</p><a class="text-link" href="{{ route('generations.create', ['solar_farm_id' => $selectedFarm->id]) }}">Registrar generación <x-icon name="arrow"/></a></div>
            @endif
        </section>
        <section class="card"><div class="card-heading"><div><span class="eyebrow">DEL PRONÓSTICO AL RESULTADO</span><h2>Bitácora de proyecciones</h2><p>La primera estimación se conserva, incluso cuando se corrige el historial.</p></div></div>
            <div class="table-scroll"><table><thead><tr><th>Período y base</th><th class="number">Estimado · kWh</th><th class="number">Real · kWh</th><th class="number">Error absoluto</th></tr></thead><tbody>
            @forelse($ordered as $projection)
                @php $actual = $actuals->get($projection->period->format('Y-m')); @endphp
                <tr><td><strong>{{ $projection->period->format('m/Y') }}</strong><small>Base: {{ $projection->training_through->format('m/Y') }}</small>@if(str_contains($projection->method, 'retrospectiva'))<span class="pill neutral">Retrospectiva</span>@endif</td><td class="number">{{ number_format($projection->projected_kwh, 2) }}</td><td class="number">{{ $actual ? number_format($actual->real_kwh, 2) : 'Pendiente' }}</td><td class="number">{{ $actual ? number_format(abs($actual->real_kwh - $projection->projected_kwh), 2).' kWh' : '—' }}</td></tr>
            @empty
                <tr><td colspan="4" class="empty-inline">Aún no hay proyecciones guardadas para esta granja.</td></tr>
            @endforelse
            </tbody></table></div>
        </section>
    @else
        <section class="card"><div class="empty-state tall-empty"><x-icon name="trend"/><span class="eyebrow">UN HORIZONTE POR CONSTRUIR</span><h2>Primero, una granja solar</h2><p>Registra una instalación y su generación para empezar a proyectar.</p><a class="button button-dark" href="{{ route('farms.create') }}">Registrar granja <x-icon name="arrow"/></a></div></section>
    @endif
    </div>
    <aside>
        <section class="card form-card forecast-controls"><span class="summary-icon"><x-icon name="trend"/></span><span class="eyebrow">CONFIGURAR HORIZONTE</span><h2>La próxima estimación</h2><p class="muted">Se calcula desde el mes actual. Los pronósticos ya guardados se conservan.</p>
        @auth
            <form method="POST" action="{{ route('projections.store') }}" data-submit-form>@csrf<input type="hidden" name="solar_farm_id" value="{{ $selectedFarm?->id }}"><label class="field">Meses a proyectar<select name="horizon">@foreach([3, 6, 12, 1] as $months)<option value="{{ $months }}" @selected((int) old('horizon', 3) === $months)>{{ $months }} {{ $months === 1 ? 'mes' : 'meses' }}</option>@endforeach</select></label><button class="button button-dark full-width" @disabled(!$selectedFarm?->is_active)><x-icon name="trend"/> Generar estimación</button></form>
            @if($selectedFarm && !$selectedFarm->is_active)<p class="forecast-help">Reactiva la granja para generar nuevas proyecciones.</p>@endif
        @else
            <a class="button button-dark full-width" href="{{ route('login') }}"><x-icon name="lock"/> Ingresar para generar</a>
        @endauth
        </section>
        <section class="method-note"><span class="eyebrow">CÁLCULO ABIERTO</span><h3>Una estimación explicable</h3><p>Para cada uno de los tres meses anteriores, dividimos los kWh entre sus días. Promediamos esos tres valores y multiplicamos por los días del mes objetivo.</p><div class="formula">Promedio diario × días del mes</div><p>Necesita meses consecutivos. No incorpora clima ni cambios futuros de capacidad.</p><a class="text-link" href="{{ route('manual') }}">Consultar metodología <x-icon name="arrow"/></a></section>
    </aside>
</div>
@endsection
