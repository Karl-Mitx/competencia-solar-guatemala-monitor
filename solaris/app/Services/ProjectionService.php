<?php

namespace App\Services;

use App\Models\Projection;
use App\Models\SolarFarm;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectionService
{
    public const METHOD = 'Promedio diario de 3 meses';

    /**
     * Mean of the daily production rates of the three immediately preceding closed
     * months, multiplied by each target month's number of days. Cutoff is only for
     * reproducible historical evaluation; it never reads observations on/after it.
     * Persisted forecasts are immutable so later actuals remain honest comparisons.
     *
     * @return Collection<int, Projection>
     */
    public function generate(SolarFarm $farm, int $horizon = 3, ?CarbonInterface $cutoff = null): Collection
    {
        if ($horizon < 1 || $horizon > 12) {
            throw ValidationException::withMessages(['horizon' => 'El horizonte debe estar entre 1 y 12 meses.']);
        }

        $start = CarbonImmutable::instance($cutoff ?? now())->startOfMonth();
        if ($start->greaterThan(CarbonImmutable::now()->startOfMonth())) {
            throw ValidationException::withMessages(['cutoff' => 'La fecha de corte no puede estar en el futuro.']);
        }

        $history = $farm->generations()
            ->whereBetween('period', [$start->subMonths(3)->toDateString(), $start->subMonth()->toDateString()])
            ->orderBy('period')->get();

        if ($history->count() !== 3 || $history->pluck('period')->map(fn ($date) => $date->format('Y-m-d'))->all() !== [
            $start->subMonths(3)->toDateString(), $start->subMonths(2)->toDateString(), $start->subMonth()->toDateString(),
        ]) {
            throw ValidationException::withMessages([
                'solar_farm_id' => 'Se necesitan registros de los tres meses cerrados inmediatamente anteriores, sin meses faltantes.',
            ]);
        }

        $dailyAverage = $history->avg(fn ($record) => (float) $record->real_kwh / $record->period->daysInMonth);

        return DB::transaction(function () use ($farm, $horizon, $start, $dailyAverage) {
            return collect(range(0, $horizon - 1))->map(function ($offset) use ($farm, $start, $dailyAverage) {
                $period = $start->addMonths($offset);

                return $farm->projections()->firstOrCreate(['period' => $period->toDateString()], [
                    'projected_kwh' => round($dailyAverage * $period->daysInMonth, 2),
                    'method' => self::METHOD.($start->lessThan(CarbonImmutable::now()->startOfMonth()) ? ' (retrospectiva)' : ''),
                    'training_through' => $start->subMonth()->toDateString(),
                    'sample_size' => 3,
                    'generated_at' => now(),
                ]);
            });
        });
    }
}
