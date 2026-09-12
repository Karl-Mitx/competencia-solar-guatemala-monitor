@extends('layouts.app')
@section('title', 'Panorama nacional')
@section('content')
@php
    $t = $dashboard['totals'];
    $nf = fn ($v, $d = 0) => number_format($v ?? 0, $d, '.', ',');
    $performance = $t['performance'];
    $scope = $farms->firstWhere('id', (int) ($filters['solar_farm_id'] ?? 0))?->name ?? $departments->firstWhere('id', (int) ($filters['department_id'] ?? 0))?->name ?? 'Guatemala';
    $periods = collect($dashboard['trend']);
    $periodLabel = $periods->isEmpty() ? 'Sin registros en este período' : $periods->first()['period'].' / '.$periods->last()['period'];
    $ranking = collect($dashboard['ranking'])->sortByDesc('generation_kwh')->take(5);
    $maximum = max(1, $ranking->max('generation_kwh') ?? 1);
@endphp
<div class="page-heading atlas-heading">
    <div><div class="eyebrow"><span class="eyebrow-line"></span> ATLAS SOLAR / GUATEMALA</div><h1>La energía de un territorio<span class="heading-dot">.</span></h1><p>Generación, infraestructura e impacto en una misma lectura.</p></div>
    <a class="button button-white" href="{{ route('reports.export', request()->query()) }}"><x-icon name="download"/> Exportar datos</a>
</div>
<section class="solar-hero-banner" aria-label="Paneles solares en un campo de generación">
    <img src="{{ asset('images/solar-banner.jpg') }}" alt="Campo de paneles solares bajo el cielo" loading="eager">
    <div class="solar-hero-overlay"></div>
    <div class="solar-hero-copy"><span class="eyebrow">ENERGÍA QUE TRANSFORMA</span><h2>El sol también dibuja el futuro.</h2><p>Explora la generación solar de Guatemala.</p></div>
</section>
@include('partials.filters')
@php($monthlyTrend = app(\App\Services\MonthlyTrend::class)->summarize($dashboard['trend']))
<section class="card" style="margin-bottom:20px" aria-label="Variación mensual">
    <h2>Variación mensual de generación</h2>
    @if($monthlyTrend['percent'] !== null)
        <strong>{{ $monthlyTrend['percent'] > 0 ? '+' : '' }}{{ number_format($monthlyTrend['percent'], 1) }}%</strong>
        <span> · {{ ['up'=>'Aumentó','down'=>'Disminuyó','stable'=>'Se mantuvo estable'][$monthlyTrend['direction']] }}</span>
        <p>{{ $monthlyTrend['current'] }} frente a {{ $monthlyTrend['previous'] }}. Totales registrados con los filtros actuales; la cobertura de registros puede variar.</p>
    @else
        <p>Se necesitan dos meses consecutivos y generación mayor que cero en el mes anterior para calcular la variación.</p>
    @endif
</section>
@if($missingHistory->isNotEmpty())
<section class="card" style="margin-bottom:20px" aria-label="Calidad del historial">
    <h2>Historial pendiente para proyecciones</h2>
    <p>{{ $missingHistory->count() }} granjas activas necesitan completar registros de los tres meses cerrados anteriores al mes actual.</p>
    <a class="text-link" href="{{ route('generations.index', array_intersect_key($filters, array_flip(['department_id', 'solar_farm_id']))) }}">Revisar registros →</a>
</section>
@endif
<div class="atlas-overview">
    <section class="solar-reading" aria-labelledby="energy-title">
        <div class="reading-top"><span class="eyebrow">01 / BALANCE ENERGÉTICO</span><x-icon name="sun"/></div>
        <div class="reading-scope">{{ $scope }}</div><h2 id="energy-title">Generación acumulada</h2>
        <div class="reading-value">{{ $nf($t['generation_kwh']) }} <span>kWh</span></div>
        <p class="reading-period"><x-icon name="clock"/> {{ $periodLabel }}</p>
        <div class="solar-dial" style="--dial-progress:{{ min(100, max(0, $performance ?? 0)) }}%"><div class="solar-dial-inner"><x-icon name="sun"/><strong>{{ $performance === null ? '—' : $nf($performance, 1).'%' }}</strong><span>{{ $performance === null ? 'Sin base esperada' : 'de lo esperado' }}</span></div></div>
        <div class="reading-baseline"><span>Generación esperada</span><strong>{{ $nf($t['expected_kwh']) }} <small>kWh</small></strong></div>
        <a class="reading-link" href="{{ route('reports', request()->query()) }}">Explorar el balance <x-icon name="arrow"/></a>
    </section>
    <div class="atlas-territory">@include('partials.territory-map', ['expanded' => true])</div>
</div>
<section class="atlas-inventory" aria-label="Infraestructura e impacto">
    <article><span class="inventory-number">02</span><x-icon name="farm"/><div><span>Granjas registradas</span><strong>{{ $nf($t['farms']) }}</strong><small>{{ $nf($t['active_farms']) }} en operación</small></div></article>
    <article><span class="inventory-number">03</span><x-icon name="sun"/><div><span>Capacidad instalada</span><strong>{{ $nf($t['capacity_kw'], 1) }} <small>kW</small></strong><small>{{ $nf($t['panels']) }} paneles instalados</small></div></article>
    <article><span class="inventory-number">04</span><x-icon name="users"/><div><span>Familias beneficiadas</span><strong>{{ $nf($t['families']) }}</strong><small>Inventario actual</small></div></article>
    <article class="inventory-impact"><span class="inventory-number">05</span><x-icon name="leaf"/><div><span>CO₂ evitado</span><strong>{{ $nf($t['co2_tonnes'], 1) }} <small>t</small></strong><small>{{ $nf($t['co2_kg']) }} kg en el período</small></div></article>
</section>
<div class="atlas-analysis">
    @include('partials.trend')
    <section class="card atlas-ranking">
        <div class="card-heading"><div><span class="eyebrow">LECTURA DEPARTAMENTAL</span><h2>Territorios que generan</h2><p>Los cinco mayores registros del período.</p></div></div>
        <div class="ranking-list">
        <?php if ($ranking->isNotEmpty()): ?>
        <?php $rank = 0; foreach ($ranking as $item): $rank++; ?>
            <a class="ranking-row" href="{{ route('reports', array_merge($filters, ['department_id' => $item['id']])) }}"><span class="rank-number">{{ str_pad($rank, 2, '0', STR_PAD_LEFT) }}</span><div class="rank-detail"><div class="rank-name"><strong>{{ $item['name'] }}</strong><span>{{ $nf($item['generation_kwh']) }} <small>kWh</small></span></div><div class="rank-track"><span style="width:{{ ($item['generation_kwh'] / $maximum) * 100 }}%"></span></div></div></a>
        <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-inline">Sin datos para los filtros seleccionados.</p>
        <?php endif; ?>
        </div>
        <a class="atlas-section-link" href="{{ route('reports', $filters) }}">Comparar departamentos <x-icon name="arrow"/></a>
    </section>
</div>
<section class="card atlas-alerts">
    <div class="card-heading"><div><span class="eyebrow">SEGUIMIENTO DE GENERACIÓN</span><h2>La energía que necesita atención <span class="count-badge">{{ $t['alerts'] }}</span></h2><p>Registros con producción igual o inferior al 80% de la esperada.</p></div><a class="text-link" href="{{ route('alerts.index', $filters) }}">Todas las alertas <x-icon name="arrow"/></a></div>
    <div class="atlas-alert-grid">
    <?php if (collect($dashboard['alerts'])->take(3)->isNotEmpty()): ?>
    <?php foreach (collect($dashboard['alerts'])->take(3) as $alert): ?>
        <?php $record = $alert->generationRecord; $deviation = app(\App\Services\EnergyService::class)->deviation((float) $record->real_kwh, (float) $record->expected_kwh); ?>
        <a class="atlas-alert-item" href="{{ route('farms.show', $record->solarFarm) }}"><div><span class="alert-circle"><x-icon name="bolt"/></span><span class="pill warning">{{ $deviation === null ? 'Sin base' : '−'.$nf($deviation, 1).'%' }}</span></div><h3>{{ $record->solarFarm->name }}</h3><p>{{ $record->solarFarm->department->name }} / {{ $record->period->format('m/Y') }}</p><span class="atlas-alert-action">Consultar granja <x-icon name="arrow"/></span></a>
    <?php endforeach; ?>
    <?php else: ?>
        <div class="atlas-clear"><x-icon name="check"/><div><strong>Sin alertas para estos filtros</strong><p>Los registros disponibles no presentan desviaciones que activen una alerta.</p></div></div>
    <?php endif; ?>
    </div>
</section>
<div class="atlas-footnote"><span>Inventario actual, incluidas las granjas inactivas. Las fechas filtran generación y emisiones.</span><a href="{{ route('manual') }}">Cómo se calculan los datos <x-icon name="arrow"/></a></div>
@endsection
