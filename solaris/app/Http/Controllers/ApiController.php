<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Resources\FarmResource;
use App\Http\Resources\GenerationResource;
use App\Models\Department;
use App\Models\SolarFarm;
use App\Services\AnalyticsService;

class ApiController extends Controller
{
    public function departments()
    {
        return response()->json(['data' => Department::orderBy('name')->get(['id', 'code', 'name', 'latitude', 'longitude', 'color'])]);
    }

    public function farms(FilterRequest $request, AnalyticsService $analytics)
    {
        return FarmResource::collection($analytics->filterFarms(SolarFarm::query(), $request->validated())->with(['department', 'panels'])->orderBy('id')->paginate(20)->withQueryString());
    }

    public function farm(SolarFarm $farm)
    {
        return new FarmResource($farm->load('department', 'panels'));
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
}
