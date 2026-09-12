<section class="card map-card {{ $expanded ?? false ? 'map-expanded' : '' }}">
    <div class="card-heading"><div><span class="eyebrow">TERRITORIO CON ENERGÍA</span><h2>La energía nos conecta</h2><p>{{ count($dashboard['map']) }} granjas en el mapa de Guatemala</p></div>@unless($expanded ?? false)<a class="icon-button border-button" href="{{ route('map') }}" aria-label="Explorar mapa completo"><x-icon name="arrow"/></a>@endunless</div>
    <div class="map-wrap"><div id="solar-map" class="solar-map" aria-label="Mapa interactivo de granjas solares en Guatemala" tabindex="0"></div><div class="map-error" hidden><x-icon name="info"/> El mapa base no está disponible. Puedes seguir explorando los marcadores.</div><span class="map-region-label">GUATEMALA <span>14.6349° N · 90.5069° O</span></span></div>
    <div class="map-bottom"><span><i class="legend-dot green"></i> Granjas por departamento</span><span class="muted">Selecciona un punto para explorar</span></div>
    @if($expanded ?? false)<div class="map-legend">@foreach(collect($dashboard['map'])->unique('department_id') as $item)<span><i class="legend-dot" style="background:{{ $item['color'] }}"></i>{{ $item['department'] }}</span>@endforeach</div>@endif
    @unless(count($dashboard['map']))<p class="empty-inline">No hay granjas que coincidan con los filtros.</p>@endunless
    <script type="application/json" id="map-data">{!! json_encode($dashboard['map'], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
    <noscript><p class="empty-inline">Activa JavaScript para navegar el mapa. Todas las granjas también aparecen en el listado.</p></noscript>
</section>
