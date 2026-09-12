@extends('layouts.app')
@section('title','Mapa de granjas')
@section('content')
<div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-line"></span> ENERGÍA EN EL TERRITORIO</div><h1>Un país conectado al sol<span class="heading-dot">.</span></h1><p>Explora la ubicación, capacidad e impacto de cada granja solar.</p></div><a class="button button-white" href="{{ route('farms.index') }}"><x-icon name="farm"/> Ver listado</a></div>
@include('partials.filters')
@include('partials.map',['expanded'=>true])
<div class="notice neutral"><x-icon name="info"/><span>Los indicadores energéticos corresponden al período seleccionado; la capacidad y las familias muestran el inventario actual. Las ubicaciones y cifras de demostración son sintéticas.</span></div>
@endsection
