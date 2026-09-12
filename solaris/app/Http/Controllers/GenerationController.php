<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\GenerationRequest;
use App\Models\Department;
use App\Models\GenerationRecord;
use App\Models\SolarFarm;
use App\Services\AnalyticsService;
use App\Services\GenerationService;

class GenerationController extends Controller
{
    public function index(FilterRequest $request, AnalyticsService $analytics)
    {
        $filters = array_filter($request->validated());

        return view('generations.index', ['records' => $analytics->records($filters)->with('solarFarm.department', 'alert')->orderByDesc('period')->paginate(20)->withQueryString(),
            'farms' => SolarFarm::orderBy('name')->get(), 'departments' => Department::orderBy('name')->get(), 'filters' => $filters]);
    }

    private function formData(GenerationRecord $record): array
    {
        return ['record' => $record, 'farms' => SolarFarm::where('is_active', true)->orWhere('id', $record->solar_farm_id)->with('department')->orderBy('name')->get()];
    }

    public function create()
    {
        return view('generations.create', $this->formData(new GenerationRecord));
    }

    public function edit(GenerationRecord $generation)
    {
        return view('generations.edit', $this->formData($generation));
    }

    public function store(GenerationRequest $request, GenerationService $service)
    {
        $service->save($request->validated());

        return redirect()->route('generations.index')->with('success', 'Generación registrada. Indicadores y alertas actualizados.');
    }

    public function update(GenerationRequest $request, GenerationRecord $generation, GenerationService $service)
    {
        $service->save($request->validated(), $generation);

        return redirect()->route('generations.index')->with('success','Generación corregida. Las alertas fueron reevaluadas.');
    }
}
