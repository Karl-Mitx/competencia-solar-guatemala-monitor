@extends('layouts.app')
@section('title','Registrar generación')
@section('content')<div class="page-heading"><div><a class="back-link" href="{{ route('generations.index') }}">← Volver a generación</a><h1>Transforma energía en datos<span class="heading-dot">.</span></h1><p>Registra la producción de un mes completo.</p></div></div>@include('generations.form')@endsection
