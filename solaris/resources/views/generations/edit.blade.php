@extends('layouts.app')
@section('title','Editar generación')
@section('content')<div class="page-heading"><div><a class="back-link" href="{{ route('generations.index') }}">← Volver a generación</a><h1>Un registro preciso<span class="heading-dot">.</span></h1><p>Actualiza la producción y la generación esperada del período.</p></div></div>@include('generations.form')@endsection
