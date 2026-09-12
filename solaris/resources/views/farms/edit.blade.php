@extends('layouts.app')
@section('title','Editar granja')
@section('content')
<div class="page-heading"><div><a class="back-link" href="{{ route('farms.show',$farm) }}">← Volver a la granja</a><h1>Actualiza su historia<span class="heading-dot">.</span></h1><p>{{ $farm->name }} · Datos de operación e infraestructura.</p></div></div>
@include('farms.form')
@endsection
