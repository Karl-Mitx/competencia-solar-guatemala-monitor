<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Department;
use App\Models\GenerationRecord;
use App\Models\SolarFarm;

class AnalyticsService
{
    public function __construct(private EnergyService $energy) {}

    public function filterFarms($query, array $filters)
    {
        return $query->when($filters['department_id'] ?? null, fn ($q, $id) => $q->where('department_id', $id))
            ->when($filters['solar_farm_id'] ?? null, fn ($q, $id) => $q->whereKey($id));
    }

    public function filterPeriods($query, array $filters)
    {
        return $query->when($filters['from'] ?? null, fn ($q, $date) => $q->where('period', '>=', $date.'-01'))
            ->when($filters['to'] ?? null, fn ($q, $date) => $q->where('period', '<=', $date.'-01'));
    }

    public function records(array $filters)
    {
        return $this->filterPeriods(GenerationRecord::query(), $filters)
            ->whereHas('solarFarm', fn ($q) => $this->filterFarms($q, $filters));
    }

    public function alerts(array $filters)
    {
        return Alert::query()->with('generationRecord.solarFarm.department')
            ->where('status', $filters['status'] ?? 'active')
            ->whereHas('generationRecord', fn ($q) => $this->filterPeriods($q, $filters)
                ->whereHas('solarFarm', fn ($farms) => $this->filterFarms($farms, $filters)))
            ->orderByDesc('updated_at');
    }

    public function dashboard(array $filters = []): array
    {
        $farms = $this->filterFarms(SolarFarm::query(), $filters)->with(['department', 'panels',
            'generations' => fn ($q) => $this->filterPeriods($q, $filters)->orderBy('period')])->get();
        $departments = Department::orderBy('name')
            ->when($filters['department_id'] ?? null, fn ($q, $id) => $q->whereKey($id))->get();
        $empty = ['farms' => 0, 'active_farms' => 0, 'panels' => 0, 'capacity_kw' => 0, 'generation_kwh' => 0, 'expected_kwh' => 0, 'families' => 0, 'generation_records' => 0];
        $totals = $empty;
        $ranking = $departments->mapWithKeys(fn ($d) => [$d->id => array_merge($empty, ['id' => $d->id, 'name' => $d->name, 'color' => $d->color])])->all();
        $trend = [];
        $map = [];
        foreach ($farms as $farm) {
            $panels = (int) $farm->panels->sum('pivot.quantity');
            $capacity = $this->energy->capacity($farm);
            $real = (float) $farm->generations->sum('real_kwh');
            $expected = (float) $farm->generations->sum('expected_kwh');
            $values = ['farms' => 1, 'active_farms' => $farm->is_active ? 1 : 0, 'panels' => $panels, 'capacity_kw' => $capacity,
                'generation_kwh' => $real, 'expected_kwh' => $expected, 'families' => $farm->families_count, 'generation_records' => $farm->generations->count()];
            foreach ($values as $key => $value) {
                $totals[$key] += $value;
                $ranking[$farm->department_id][$key] += $value;
            }
            foreach ($farm->generations as $record) {
                $period = $record->period->format('Y-m');
                $trend[$period] ??= ['period' => $period, 'label' => $record->period->translatedFormat('M Y'), 'real_kwh' => 0, 'expected_kwh' => 0];
                $trend[$period]['real_kwh'] += (float) $record->real_kwh;
                $trend[$period]['expected_kwh'] += (float) $record->expected_kwh;
            }
            $map[] = ['id' => $farm->id, 'name' => $farm->name, 'department' => $farm->department->name, 'department_id' => $farm->department_id,
                'color' => $farm->department->color, 'latitude' => (float) $farm->latitude, 'longitude' => (float) $farm->longitude,
                'capacity_kw' => $capacity, 'panels' => $panels, 'families' => $farm->families_count, 'generation_kwh' => $real,
                'performance' => $expected > 0 ? round($real / $expected * 100, 1) : null, 'status' => $farm->is_active ? 'active' : 'inactive',
                'url' => route('farms.show', $farm)];
        }
        $enrich = function ($row) {
            $row['co2_kg'] = $this->energy->co2($row['generation_kwh']);
            $row['co2_tonnes'] = round($row['co2_kg'] / 1000, 3);
            $row['performance'] = $row['expected_kwh'] > 0 ? round($row['generation_kwh'] / $row['expected_kwh'] * 100, 1) : null;
            foreach (['capacity_kw', 'generation_kwh', 'expected_kwh'] as $field) {
                $row[$field] = round($row[$field], 2);
            }

            return $row;
        };
        $totals = $enrich($totals);
        $totals['alerts'] = $this->alerts(array_diff_key($filters, ['status' => true]))->count();
        ksort($trend);

        return ['totals' => $totals, 'trend' => array_values($trend),
            'ranking' => collect($ranking)->map($enrich)->sortByDesc('generation_kwh')->values()->all(),
            'map' => $map, 'alerts' => $this->alerts(array_diff_key($filters, ['status' => true]))->limit(6)->get()];
    }
}
