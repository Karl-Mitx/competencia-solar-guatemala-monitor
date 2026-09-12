<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Resources\FarmResource;
use App\Http\Resources\GenerationResource;
use App\Models\Department;
use App\Models\SolarFarm;
use App\Services\AnalyticsService;
use App\Services\EnergyService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function departments()
    {
        return response()->json(['data' => Department::orderBy('name')->get(['id', 'code', 'name', 'latitude', 'longitude', 'color'])]);
    }

    public function farms(FilterRequest $request, AnalyticsService $analytics)
    {
        return FarmResource::collection($analytics->filterFarms(SolarFarm::query(), $request->validated())->with(['department', 'panels', 'generations'])->orderBy('id')->paginate(20)->withQueryString());
    }

    public function farm(SolarFarm $farm)
    {
        return new FarmResource($farm->load('department', 'panels', 'generations'));
    }

    public function generations(FilterRequest $request, AnalyticsService $analytics)
    {
        return GenerationResource::collection($analytics->records($request->validated())->with('solarFarm')->orderByDesc('period')->orderBy('id')->paginate(50)->withQueryString());
    }

    public function statistics(FilterRequest $request, AnalyticsService $analytics)
    {
        $data = $analytics->dashboard($request->validated());

        return response()->json(['data' => ['totals' => $data['totals'], 'departments' => $data['ranking'], 'trend' => $data['trend']],
            'meta' => ['filters' => $request->validated(), 'asset_scope' => 'Inventario actual; filtros temporales aplican a generación y emisiones.', 'co2_factor_kg_per_kwh' => 0.40, 'alert_threshold' => 0.80, 'demo_data' => config('solaris.demo_data')]]);
    }

    public function compareFarms(Request $request)
    {
        $data = $request->validate(['farm_ids' => ['required', 'array', 'min:2', 'max:4'], 'farm_ids.*' => ['integer', 'distinct', 'exists:solar_farms,id']]);
        $farms = SolarFarm::with(['department', 'panels', 'generations'])->whereIn('id', $data['farm_ids'])->get();
        $energy = app(EnergyService::class);

        return response()->json(['data' => $farms->map(function ($farm) use ($energy) {
            $real = (float) $farm->generations->sum('real_kwh');
            $expected = (float) $farm->generations->sum('expected_kwh');

            return ['id' => $farm->id, 'name' => $farm->name, 'department' => $farm->department->name,
                'capacity_kw' => $energy->capacity($farm), 'panel_count' => (int) $farm->panels->sum('pivot.quantity'),
                'families_count' => $farm->families_count, 'generation_kwh' => round($real, 2), 'expected_kwh' => round($expected, 2),
                'performance' => $expected > 0 ? round($real / $expected * 100, 1) : null, 'co2_tonnes' => round($energy->co2($real) / 1000, 3), 'is_active' => $farm->is_active];
        })->values(), 'meta' => ['compared' => $farms->count(), 'max_allowed' => 4]]);
    }
}
