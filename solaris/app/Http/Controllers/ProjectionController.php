<?php

namespace App\Http\Controllers;

use App\Models\SolarFarm;
use App\Services\ProjectionService;
use App\Services\ForecastSummary;
use Illuminate\Http\Request;

class ProjectionController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['solar_farm_id' => ['nullable', 'integer', 'exists:solar_farms,id']]);
        $farms = SolarFarm::with('department')->orderBy('name')->get();
        $selectedFarm = isset($data['solar_farm_id']) ? $farms->firstWhere('id', (int) $data['solar_farm_id']) : $farms->first();

        $projections = $selectedFarm ? $selectedFarm->projections()->with('solarFarm')->orderByDesc('period')->get() : collect();
        $history = $selectedFarm ? $selectedFarm->generations()->orderBy('period')->get() : collect();

        return view('projections.index', compact('farms', 'selectedFarm') + [
            'projections' => $projections, 'history' => $history,
            'forecastSummary' => app(ForecastSummary::class)->summarize($projections, $history)]);
    }

    public function store(Request $request, ProjectionService $service)
    {
        $data = $request->validate(['solar_farm_id' => ['required', 'integer', 'exists:solar_farms,id'], 'horizon' => ['required', 'integer', 'between:1,12']]);
        $farm = SolarFarm::findOrFail($data['solar_farm_id']);
        abort_unless($farm->is_active, 422, 'Reactiva la granja antes de proyectar.');
        $service->generate($farm, (int) $data['horizon']);

        return redirect()->route('projections.index', ['solar_farm_id' => $farm->id])->with('success', 'Proyección calculada. Los pronósticos existentes se conservan para compararlos con los resultados reales.');
    }
}
