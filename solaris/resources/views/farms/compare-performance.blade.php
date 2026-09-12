<section class="card" style="margin-top:24px">
    <h2>Cumplimiento y cobertura del período</h2>
    <p>Del {{ request('from') ?: 'primer registro' }} al {{ request('to') ?: 'último registro' }}. Comprueba la cobertura antes de comparar totales: no todas las granjas necesariamente tienen los mismos meses registrados.</p>
    <div class="table-scroll"><table>
        <thead><tr><th>Granja</th><th>Meses registrados</th><th>Generación esperada</th><th>Cumplimiento</th></tr></thead>
        <tbody>
        @foreach($farms as $farm)
            @php($expected = $farm->generations->sum('expected_kwh'))
            <tr><th><a href="{{ route('farms.show', $farm) }}">{{ $farm->name }}</a></th>
                <td>{{ $farm->generations->count() }}<br><small>{{ $farm->generations->first()?->period->format('Y-m') ?? 'Sin registros' }} @if($farm->generations->isNotEmpty()) → {{ $farm->generations->last()->period->format('Y-m') }} @endif</small></td>
                <td>{{ number_format($expected, 2) }} kWh</td>
                <td>{{ $expected > 0 ? number_format($farm->generations->sum('real_kwh') / $expected * 100, 1).'%' : 'Sin base esperada' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    <p>Capacidad, paneles y familias representan el inventario actual; el filtro de fechas solo se aplica a la generación y al CO₂.</p>
</section>
