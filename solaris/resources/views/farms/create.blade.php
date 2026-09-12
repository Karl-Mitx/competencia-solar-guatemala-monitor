@extends('layouts.app')
@section('title','Nueva granja')
@section('content')
<div class="page-heading"><div><a class="back-link" href="{{ route('farms.index') }}">← Volver a granjas</a><h1>Una nueva fuente de energía<span class="heading-dot">.</span></h1><p>Registra una granja y los paneles que definen su capacidad.</p></div></div>
@include('farms.form')
@endsection
