<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ForecastSummary
{
    public function summarize(Collection $projections, Collection $history): array
    {
        $read = fn ($record, string $key) => is_array($record) ? ($record[$key] ?? null) : ($record->{$key} ?? null);
        $values = $history->filter(fn ($record) => (float) $read($record, 'real_kwh') >= 0)->map(fn ($record) => (float) $read($record, 'real_kwh'));
        $mean = $values->count() ? $values->avg() : 0;
        $variance = $values->count() > 1 ? $values->map(fn ($value) => ($value - $mean) ** 2)->avg() : 0;
        $coefficient = $mean > 0 ? sqrt($variance) / $mean : null;

        return [
            'total_kwh' => round($projections->sum(fn ($projection) => (float) $read($projection, 'projected_kwh')), 2),
            'months' => $projections->count(),
            'volatility' => $coefficient === null ? null : round($coefficient * 100, 1),
            'confidence' => $coefficient === null ? 'Sin historial suficiente' : ($coefficient <= .15 ? 'Estable' : ($coefficient <= .3 ? 'Variable' : 'Alta variabilidad')),
        ];
    }
}
