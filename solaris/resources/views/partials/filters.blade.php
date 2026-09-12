<form class="filter-bar" method="GET" action="{{ url()->current() }}">
    <div class="filter-heading"><x-icon name="filter"/><span>Filtrar por</span></div>
    <label><span>Departamento</span><select name="department_id"><option value="">Todos los departamentos</option>@foreach($departments as $department)<option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>{{ $department->name }}</option>@endforeach</select></label>
    @if(isset($farms) && !($farms instanceof \Illuminate\Pagination\LengthAwarePaginator))<label class="farm-filter"><span>Granja</span><select name="solar_farm_id"><option value="">Todas las granjas</option>@foreach($farms as $filterFarm)<option value="{{ $filterFarm->id }}" @selected(($filters['solar_farm_id'] ?? '') == $filterFarm->id)>{{ $filterFarm->name }}</option>@endforeach</select></label>@endif
    <div class="date-range"><label><span>Desde</span><input type="month" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="Período desde"></label><span class="date-dash">—</span><label><span>Hasta</span><input type="month" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="Período hasta"></label></div>
    <button class="button button-dark button-sm" type="submit">Aplicar</button>
    @if(array_filter($filters ?? []))<a class="filter-reset" href="{{ url()->current() }}" aria-label="Limpiar filtros"><x-icon name="close"/></a>@endif
</form>
