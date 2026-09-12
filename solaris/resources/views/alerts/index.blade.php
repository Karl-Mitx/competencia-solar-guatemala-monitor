@extends('layouts.app')
@section('title','Alertas de generación')
@section('content')
<div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-line"></span> MONITOREO PREVENTIVO</div><h1>Atención donde importa<span class="heading-dot">.</span></h1><p>Identifica a tiempo las desviaciones en la generación solar.</p></div><span class="pill warning large"><x-icon name="alert"/> {{ $alerts->total() }} alertas</span></div>
@include('partials.filters')
@if($alerts->isNotEmpty())
<section class="card" aria-label="Recomendaciones de seguimiento" style="margin-bottom:20px">
    <h2>Prioridad y próximos pasos</h2>
    <p class="muted">Orientación basada en el rendimiento registrado; no sustituye una inspección técnica.</p>
    @foreach($alerts as $item)
        <article style="padding:14px 0;border-bottom:1px solid var(--line)">
            <a class="text-link" href="{{ route('farms.show', $item->generationRecord->solarFarm) }}">{{ $item->generationRecord->solarFarm->name }}</a>
            <span class="pill {{ $item->status === 'active' ? 'warning' : 'positive' }}">{{ $item->status === 'active' ? ['critical'=>'Crítica','high'=>'Alta','attention'=>'Atención'][$item->severity()] : 'Resuelta' }}</span>
            <small>{{ $item->generationRecord->period->format('m/Y') }}</small>
            <p style="margin:8px 0 0">{{ $item->status === 'active' ? $item->recommendation() : 'El registro fue corregido. Se conserva la alerta como historial.' }}</p>
        </article>
    @endforeach
</section>
@endif
<div class="notice warning"><x-icon name="info"/><span>Se activa una alerta cuando la generación real es igual o inferior al <strong>80% de la esperada</strong>. Al corregir un registro, el estado se actualiza automáticamente.</span></div>
<section class="card"><div class="card-heading"><div><h2>Seguimiento de desempeño</h2><p>Revisa la generación registrada y la magnitud de cada desviación.</p></div></div><div class="table-scroll"><table><thead><tr><th>Granja solar</th><th>Período</th><th class="number">Esperada · kWh</th><th class="number">Real · kWh</th><th class="number">Desviación</th><th>Estado</th><th><span class="sr-only">Acciones</span></th></tr></thead><tbody>@forelse($alerts as $alert)@php $r=$alert->generationRecord; $deviation=$r->expected_kwh>0?max(0,(1-$r->real_kwh/$r->expected_kwh)*100):0; @endphp<tr><td><a class="table-name" href="{{ route('farms.show',$r->solarFarm) }}">{{ $r->solarFarm->name }}</a><small class="block muted">{{ $r->solarFarm->department->name }}</small></td><td>{{ $r->period->format('m/Y') }}</td><td class="number">{{ number_format($r->expected_kwh,2) }}</td><td class="number">{{ number_format($r->real_kwh,2) }}</td><td class="number amber-text">−{{ number_format($deviation,1) }}%</td><td><span class="pill {{ $alert->status==='active'?'warning':'positive' }}">{{ $alert->status==='active'?'Activa':'Resuelta' }}</span></td><td>@auth<a class="text-link" href="{{ route('generations.edit',$r) }}">Revisar <x-icon name="arrow"/></a>@else<a class="text-link" href="{{ route('farms.show',$r->solarFarm) }}">Ver granja <x-icon name="arrow"/></a>@endauth</td></tr>@empty<tr><td colspan="7"><div class="empty-state"><x-icon name="check"/><h3>Todo en su lugar</h3><p>No hay alertas que coincidan con tus filtros.</p></div></td></tr>@endforelse</tbody></table></div>{{ $alerts->withQueryString()->links('components.pagination') }}</section>
@endsection
