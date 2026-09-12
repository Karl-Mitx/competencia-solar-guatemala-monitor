@extends('layouts.app')
@section('title','Editar modelo de panel')
@section('content')<div class="page-heading"><div><a class="back-link" href="{{ route('panels.index') }}">← Volver al catálogo</a><h1>Datos del modelo<span class="heading-dot">.</span></h1><p>{{ $panel->brand }} {{ $panel->model }}</p></div></div>@include('panels.form')@endsection
