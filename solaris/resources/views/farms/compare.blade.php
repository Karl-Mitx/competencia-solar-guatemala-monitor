@extends('layouts.app')
@section('title','Comparar granjas')
@section('content')
<div class="page-heading"><div><a class="back-link" href="{{ route('farms.index') }}">← Volver a granjas</a><div class="eyebrow"><span class="eyebrow-line"></span> ANÁLISIS COMPARATIVO</div><h1>Granjas frente a frente<span class="heading-dot">.</span></h1><p>Selecciona entre dos y cuatro instalaciones para comparar su desempeño.</p></div></div>
<form class="card compare-picker" method="GET" action="{{ route('farms.compare') }}">
    @foreach(['from', 'to'] as $dateFilter)
        @if(request()->filled($dateFilter))<input type="hidden" name="{{ $dateFilter }}" value="{{ request($dateFilter) }}">@endif
    @endforeach
    <div class="card-heading"><div><h2>Elegir granjas</h2><p>Marca entre dos y cuatro instalaciones. El período seleccionado se conserva.</p></div><button class="button button-dark">Comparar selección</button></div>
    <div class="compare-options">
    @foreach($allFarms as $farm)
        <label class="compare-option"><input type="checkbox" name="farm_ids[]" value="{{ $farm->id }}" @checked($ids->contains($farm->id))><span><strong>{{ $farm->name }}</strong><small>{{ $farm->department->name }} · {{ $farm->is_active ? 'Activa' : 'Inactiva' }}</small></span></label>
    @endforeach
    </div>
</form>
@if($farms->count() >= 2)
<form class="card" method="GET" action="{{ route('farms.compare') }}" style="margin-top:24px">
    @foreach($ids as $id)<input type="hidden" name="farm_ids[]" value="{{ $id }}">@endforeach
    <h2>Período de comparación</h2>
    <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:end">
        <div><label for="compare-from">Desde</label><input id="compare-from" type="month" name="from" value="{{ request('from') }}"></div>
        <div><label for="compare-to">Hasta</label><input id="compare-to" type="month" name="to" value="{{ request('to') }}"></div>
        <button class="button button-primary" type="submit">Aplicar período</button>
        <a class="text-link" href="{{ route('farms.compare', ['farm_ids' => $ids->all()]) }}">Todo el historial</a>
    </div>
</form>
@include('farms.compare-performance')
<section class="card comparison-table"><div class="card-heading"><div><span class="eyebrow">LECTURA COMPARADA</span><h2>Indicadores principales</h2><p>Los valores se calculan con el inventario y el historial disponible.</p></div><span class="pill neutral">{{ $farms->count() }} granjas</span></div><div class="table-scroll"><table><thead><tr><th>Indicador</th>@foreach($farms as $farm)<th class="number">{{ $farm->name }}</th>@endforeach</tr></thead><tbody>@foreach([['Capacidad instalada',fn($f)=>number_format($f->panels->sum(fn($p)=>$p->nominal_power_kw*$p->pivot->quantity),2).' kW'],['Paneles instalados',fn($f)=>number_format($f->panels->sum('pivot.quantity'))],['Familias beneficiadas',fn($f)=>number_format($f->families_count)],['Generación real',fn($f)=>number_format($f->generations->sum('real_kwh'),2).' kWh'],['CO₂ evitado',fn($f)=>number_format($f->generations->sum('real_kwh')*.4/1000,2).' t']] as [$label,$value])<tr><th>{{ $label }}</th>@foreach($farms as $farm)<td class="number">{{ $value($farm) }}</td>@endforeach</tr>@endforeach</tbody></table></div></section>
@else
<div class="empty-state compare-empty"><x-icon name="chart"/><h3>Elige al menos dos granjas</h3><p>Podrás comparar capacidad, producción, familias y CO₂ evitado.</p></div>
@endif
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.compare-picker');
    const boxes = [...form.querySelectorAll('input[type="checkbox"]')];
    const button = form.querySelector('button');
    const status = document.createElement('p');
    status.setAttribute('role', 'status');
    form.prepend(status);
    const update = () => {
        const count = boxes.filter(box => box.checked).length;
        status.textContent = `${count} de 4 granjas seleccionadas. Selecciona al menos dos.`;
        button.disabled = count < 2 || count > 4;
        boxes.forEach(box => { box.disabled = !box.checked && count >= 4; });
    };
    boxes.forEach(box => box.addEventListener('change', update));
    update();
});
</script>
@endpush
