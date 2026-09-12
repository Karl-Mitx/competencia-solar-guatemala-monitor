@if($farms->isNotEmpty())
<section class="farm-gallery-section" aria-labelledby="farm-gallery-title">
    <h2 id="farm-gallery-title">Explorar las granjas en imágenes</h2>
    <p>Instalaciones de esta página con los filtros seleccionados. Fotografías ilustrativas.</p>
    <div class="farm-gallery">
    @foreach($farms as $farm)
        <article class="farm-gallery-card">
            <img src="{{ asset('images/farms/farm-'.(($farm->id - 1) % 7 + 1).'.png') }}" alt="Instalación solar ilustrativa" width="480" height="260" loading="lazy">
            <div class="farm-gallery-content">
                <span class="pill {{ $farm->is_active ? 'positive' : 'neutral' }}">{{ $farm->is_active ? 'Activa' : 'Inactiva' }}</span>
                <h3><a href="{{ route('farms.show', $farm) }}">{{ $farm->name }}</a></h3>
                <p>{{ $farm->department->name }} · {{ $farm->location_name }}</p>
                <dl>
                    <div><dt>Capacidad</dt><dd>{{ number_format(app(\App\Services\EnergyService::class)->capacity($farm), 2) }} kW</dd></div>
                    <div><dt>Familias</dt><dd>{{ number_format($farm->families_count) }}</dd></div>
                </dl>
                <a class="text-link" href="{{ route('farms.show', $farm) }}">Consultar ficha →</a>
            </div>
        </article>
    @endforeach
    </div>
</section>
<style>
.farm-gallery-section{margin-top:28px}.farm-gallery{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,270px),1fr));gap:18px}
.farm-gallery-card{overflow:hidden;border:1px solid #435477;border-radius:18px;background:linear-gradient(145deg,#192c50,#101a35);color:#fff}
.farm-gallery-card>img{display:block;width:100%;height:180px;object-fit:cover}.farm-gallery-content{padding:20px}.farm-gallery-content h3{margin-top:12px;color:#fff}.farm-gallery-content p,.farm-gallery-content dt{color:#cbd7eb}.farm-gallery-content dl{display:flex;justify-content:space-between;gap:14px}.farm-gallery-content dd{margin:0;font-weight:700;color:#fff}.farm-gallery-card:focus-within{outline:2px solid #c5a2ff;outline-offset:3px}
</style>
@endif
