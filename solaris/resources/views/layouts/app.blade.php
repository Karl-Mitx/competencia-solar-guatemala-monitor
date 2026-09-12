<!DOCTYPE html>
<html lang="es">
<head>
    <script src="{{ asset('theme.js') }}"></script>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panorama nacional') · SOLARIS Guatemala</title>
    <meta name="description" content="Monitoreo de generación solar en los 22 departamentos de Guatemala. Granjas, energía, impacto y proyecciones en un solo lugar.">
    <meta name="theme-color" content="#183c34">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#main">Saltar al contenido</a>
<div class="sidebar-scrim" data-menu-close></div>
<aside class="sidebar" id="sidebar" aria-label="Navegación principal">
    <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark"><x-icon name="sun"/></span><span>SOLARIS<small>ATLAS SOLAR DE GUATEMALA</small></span></a>
    <div class="country-label"><span class="flag">▥</span> Guatemala <span class="country-dot"></span></div>
    <p class="nav-label">CENTRO DE MONITOREO</p>
    <nav>
    @foreach([['dashboard','dashboard','Panorama general'],['map','map','Mapa de granjas'],['farms.*','farm','Granjas solares'],['farms.compare','chart','Comparar granjas'],['panels.*','sun','Catálogo de paneles'],['generations.*','bolt','Generación'],['reports','chart','Reportes'],['alerts.*','alert','Alertas'],['projections.*','trend','Proyecciones']] as [$pattern,$icon,$label])
        @php $routeName = str_contains($pattern, '*') ? str_replace('*','index',$pattern) : $pattern; @endphp
        <a href="{{ route($routeName) }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}" @if(request()->routeIs($pattern)) aria-current="page" @endif><x-icon :name="$icon"/><span>{{ $label }}</span>@if(request()->routeIs($pattern))<span class="nav-active-dot"></span>@endif</a>
    @endforeach
    </nav>
    <a class="nav-link {{ request()->routeIs('simulator') ? 'active' : '' }}" href="{{ route('simulator') }}" @if(request()->routeIs('simulator')) aria-current="page" @endif><x-icon name="bolt"/><span>Simulador solar</span></a>
    <div class="sidebar-bottom">
        <p class="nav-label">RECURSOS</p>
        <button type="button" class="nav-link theme-toggle" data-theme-toggle aria-pressed="false"><x-icon name="sun"/><span data-theme-label>Cambiar apariencia</span></button>
        <a class="nav-link {{ request()->routeIs('api.docs') ? 'active' : '' }}" href="{{ route('api.docs') }}"><x-icon name="code"/> API & datos</a>
        <a class="nav-link {{ request()->routeIs('manual') ? 'active' : '' }}" href="{{ route('manual') }}"><x-icon name="book"/> Guía de uso</a>
        <div class="impact-note"><span class="impact-icon"><x-icon name="leaf"/></span><strong>Territorio. Energía. Impacto.</strong><p>22 departamentos en un mismo atlas.</p></div>
        <div class="sidebar-user"><span class="avatar">{{ auth()->check() ? mb_substr(auth()->user()->name,0,1) : 'GT' }}</span><div><strong>{{ auth()->user()->name ?? 'Explorador de energía' }}</strong><small>{{ auth()->check() ? 'Administración' : 'Acceso público' }}</small></div>@auth<form method="POST" action="{{ route('logout') }}">@csrf<button class="icon-button" aria-label="Cerrar sesión"><x-icon name="logout"/></button></form>@else<a class="icon-button" href="{{ route('login') }}" aria-label="Ingresar"><x-icon name="arrow"/></a>@endauth</div>
    </div>
</aside>
<div class="app-shell">
    <header class="topbar"><div class="breadcrumb"><button class="icon-button mobile-menu" data-menu-toggle aria-label="Abrir navegación" aria-expanded="false" aria-controls="sidebar"><x-icon name="menu"/></button><span class="breadcrumb-home">Atlas solar / GT</span><span class="slash">/</span><strong>@yield('title', 'Panorama nacional')</strong></div><div class="topbar-right"><span class="system-status"><span></span> {{ config('solaris.demo_data') ? 'Datos de demostración' : 'Registros de generación' }}</span><a href="{{ route('alerts.index') }}" class="icon-button top-alert" aria-label="Ver alertas"><x-icon name="alert"/></a></div></header>
    <main id="main">
        @if(session('status') || session('success'))<div class="notice success" role="status"><x-icon name="check"/><span>{{ session('status') ?? session('success') }}</span></div>@endif
        @if(session('error'))<div class="notice danger" role="alert"><x-icon name="alert"/><span>{{ session('error') }}</span></div>@endif
        @if($errors->any())<div class="notice danger" role="alert"><x-icon name="alert"/><div><strong>Revisa los datos para continuar.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
        @yield('content')
        <footer class="page-footer"><span><span class="footer-sun">✳</span> SOLARIS <span class="muted">· Guatemala</span></span><span>Energía limpia. Decisiones informadas.</span><a href="{{ route('manual') }}">Acerca de los cálculos <x-icon name="arrow"/></a></footer>
    </main>
</div>
@stack('scripts')
@include('partials.solar-help')
</body></html>
