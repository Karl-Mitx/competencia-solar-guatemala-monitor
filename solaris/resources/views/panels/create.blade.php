@extends('layouts.app')
@section('title','Nuevo modelo de panel')
@section('content')<div class="page-heading"><div><a class="back-link" href="{{ route('panels.index') }}">← Volver al catálogo</a><h1>Agrega potencial solar<span class="heading-dot">.</span></h1><p>Registra un modelo y su potencia nominal.</p></div></div>@include('panels.form')@endsection
