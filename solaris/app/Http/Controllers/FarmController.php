<?php

namespace App\Http\Controllers;

use App\Http\Requests\FarmRequest;
use App\Http\Requests\FilterRequest;
use App\Models\Department;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FarmController extends Controller
{
    public function compare(Request $request)
    {
        $data = $request->validate([
            'farm_ids' => ['sometimes', 'array', 'min:2', 'max:4'],
            'farm_ids.*' => ['required', 'integer', 'distinct', 'exists:solar_farms,id'],
            'from' => ['nullable', 'date_format:Y-m'],
            'to' => ['nullable', 'date_format:Y-m', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ]);
        $ids = collect($data['farm_ids'] ?? [])->map(fn ($id) => (int) $id);
        $farms = SolarFarm::with(['department', 'panels', 'generations' => fn ($query) => $query
            ->when($data['from'] ?? null, fn ($q, $month) => $q->where('period', '>=', $month.'-01'))
            ->when($data['to'] ?? null, fn ($q, $month) => $q->where('period', '<=', $month.'-01'))
            ->orderBy('period')])->whereIn('id', $ids)->get()->sortBy(fn ($farm) => $ids->search($farm->id))->values();
        $allFarms = SolarFarm::with('department')->orderBy('name')->get();

        return view('farms.compare', compact('farms', 'allFarms', 'ids'));
    }

    public function index(FilterRequest $request, AnalyticsService $analytics)
    {
        $filters = array_filter($request->validated());
        $farms = $analytics->filterFarms(SolarFarm::query(), $filters)->with(['department', 'panels', 'generations'])
            ->when($filters['search'] ?? null, fn ($q, $text) => $q->where('name', 'like', '%'.$text.'%'))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('is_active', $status === 'active'))
            ->orderBy('name')->paginate(12)->withQueryString();

        return view('farms.index', compact('farms', 'filters') + ['departments' => Department::orderBy('name')->get()]);
    }

    private function formData(SolarFarm $farm): array
    {
        $farm->load('panels');

        return ['farm' => $farm, 'departments' => Department::orderBy('name')->get(),
            'panels' => SolarPanel::where('is_active', true)->orWhereIn('id', $farm->panels->pluck('id'))->orderBy('brand')->get()];
    }

    public function create()
    {
        return view('farms.create', $this->formData(new SolarFarm(['is_active' => true, 'families_count' => 0])));
    }

    public function edit(SolarFarm $farm)
    {
        return view('farms.edit', $this->formData($farm));
    }

    public function show(SolarFarm $farm)
    {
        $farm->load(['department', 'panels', 'generations.alert', 'projections']);

        return view('farms.show', ['farm' => $farm, 'projections' => $farm->projections->sortBy('period'),
            'history' => $farm->generations->sortBy('period')->values()]);
    }

    private function persist(FarmRequest $request, SolarFarm $farm): SolarFarm
    {
        return DB::transaction(function () use ($request, $farm) {
            $data = $request->validated();
            if ($request->hasFile('photo')) {
                if ($farm->photo_path) Storage::disk('public')->delete($farm->photo_path);
                $data['photo_path'] = $request->file('photo')->store('farms', 'public');
            }
            unset($data['photo']);
            $rows = $data['panels'] ?? [];
            unset($data['panels']);
            $farm->fill($data)->save();
            $farm->panels()->sync(collect($rows)->mapWithKeys(fn ($row) => [$row['solar_panel_id'] => ['quantity' => $row['quantity']]])->all());

            return $farm;
        });
    }

    public function store(FarmRequest $request)
    {
        return redirect()->route('farms.show', $this->persist($request, new SolarFarm))->with('success', 'Granja registrada correctamente.');
    }

    public function update(FarmRequest $request, SolarFarm $farm)
    {
        return redirect()->route('farms.show', $this->persist($request, $farm))->with('success', 'Granja actualizada. La capacidad se recalculó a partir de sus paneles.');
    }

    public function destroy(SolarFarm $farm)
    {
        $farm->update(['is_active' => false]);

        return redirect()->route('farms.show', $farm)->with('success', 'Granja desactivada. Su historial permanece disponible.');
    }
}
