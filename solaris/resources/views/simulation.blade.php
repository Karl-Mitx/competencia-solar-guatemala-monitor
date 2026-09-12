@extends('layouts.app')
@section('title', 'Simulador solar')
@section('content')
<div class="page-heading simulation-heading"><div><span class="eyebrow">EXPLORAR ESCENARIOS</span><h1>¿Qué podría generar tu instalación?</h1><p>Modifica los supuestos y compara resultados. Esta simulación no cambia el inventario ni las proyecciones guardadas.</p></div></div>
<section class="card simulation-panel">
    <h2>Supuestos del escenario</h2>
    <p>Los valores iniciales son ejemplos editables, no mediciones locales ni cotizaciones. Las horas solares son horas equivalentes a plena potencia, no horas de luz del día.</p>
    <form method="GET" action="{{ route('simulator') }}">
        <div class="simulation-fields">
        @foreach([
            ['panels','Cantidad de paneles',1,1000000,1],
            ['watts','Potencia por panel (W)',1,2000,1],
            ['sun_hours','Horas solares equivalentes diarias',0,12,0.1],
            ['loss_percent','Pérdidas del sistema (%)',0,100,0.1],
            ['household_kwh','Consumo mensual por hogar (kWh)',1,100000,1],
            ['co2_factor','Factor de CO₂ evitado (kg/kWh)',0,5,0.01],
            ['self_consumption','Energía autoconsumida (%)',0,100,0.1],
            ['tariff','Precio evitado de energía (Q/kWh)',0,100,0.01],
            ['investment','Inversión inicial (Q)',0,10000000000,0.01],
        ] as [$key,$label,$min,$max,$step])
            <div><label for="sim-{{ $key }}">{{ $label }}</label><input id="sim-{{ $key }}" name="{{ $key }}" type="number" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" required value="{{ old($key, $input[$key]) }}"></div>
        @endforeach
        </div>
        <div class="simulation-actions"><button class="button button-primary" type="submit">Calcular escenario</button><a class="text-link" href="{{ route('simulator') }}">Restablecer ejemplo</a></div>
    </form>
</section>
<section class="card simulation-panel" aria-labelledby="simulation-results">
    <h2 id="simulation-results">Resultados orientativos</h2>
    <dl class="simulation-results">
    @foreach([
        ['capacity_kw','Capacidad instalada','kW'],
        ['monthly_kwh','Generación mensual media','kWh'],
        ['annual_kwh','Generación anual','kWh'],
        ['co2_tonnes','CO₂ anual evitado según el factor','t'],
        ['equivalent_households','Hogares equivalentes por energía','hogares'],
        ['annual_savings','Ahorro bruto anual por autoconsumo','Q'],
    ] as [$key,$label,$unit])
        <div><dt>{{ $label }}</dt><dd>{{ number_format($result[$key], 2) }} <small>{{ $unit }}</small></dd></div>
    @endforeach
        <div><dt>Recuperación simple de la inversión</dt><dd>{{ $result['payback_years'] === null ? 'No calculable sin ahorro' : number_format($result['payback_years'], 1).' años' }}</dd></div>
    </dl>
    <details><summary>Cómo se calcula y qué no incluye</summary>
        <p>Capacidad = paneles × W ÷ 1,000. Energía anual = capacidad × horas solares × (1 − pérdidas ÷ 100) × 365. Media mensual = energía anual ÷ 12.</p>
        <p>Hogares equivalentes = media mensual ÷ consumo por hogar; no representa familias realmente conectadas. CO₂ = energía anual × factor indicado ÷ 1,000.</p>
        <p>Ahorro bruto = energía anual × autoconsumo ÷ 100 × precio evitado. Recuperación simple = inversión ÷ ahorro bruto anual. No incluye venta de excedentes, mantenimiento, financiación, impuestos, baterías, degradación ni variaciones estacionales. No es una evaluación de rentabilidad ni una garantía de producción.</p>
    </details>
</section>
<style>
html[data-theme="dark"] body main .card.simulation-panel{background:#111d38!important;background-image:none!important;border:1px solid #45577e!important;border-radius:20px!important}html[data-theme="dark"] body main .simulation-panel input{background:#0b142b!important;color:#fff!important}
.simulation-heading p{color:#dbe5ff!important}.simulation-results dt{color:#dbe5ff}.simulation-panel input:focus-visible{outline:3px solid #b69bff;outline-offset:3px}.simulation-panel .simulation-actions{padding-bottom:12px}@media(max-width:600px){.simulation-heading h1{font-size:2rem;overflow-wrap:anywhere}.simulation-panel{padding:20px!important}.simulation-actions .button{width:100%}.simulation-panel:last-of-type{margin-bottom:90px}}
.simulation-panel{margin-bottom:24px}.simulation-fields{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,250px),1fr));gap:20px}.simulation-fields label{display:block;margin-bottom:8px}.simulation-fields input{display:block;box-sizing:border-box;width:100%;min-height:46px;padding:10px;border:1px solid #7889b0;border-radius:8px}.simulation-actions{display:flex;flex-wrap:wrap;align-items:center;gap:20px;margin-top:24px}.simulation-results{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr));gap:24px}.simulation-results dd{margin:8px 0 0;font-size:1.5rem;font-weight:700}.simulation-results small{font-size:.8rem}.simulation-panel summary{cursor:pointer;font-weight:700}.simulation-panel details{margin-top:24px}html[data-theme="dark"] .simulation-panel{background:#111d38!important;color:#fff!important}html[data-theme="dark"] .simulation-panel input{background:#0b142b!important;color:#fff!important}html[data-theme="dark"] .simulation-panel :is(p,label,dt){color:#dbe5ff!important}
</style>
@endsection
