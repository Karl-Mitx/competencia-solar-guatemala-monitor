<?php

namespace App\Services;

use App\Models\SolarFarm;

class EnergyService
{
    public const CO2_KG_PER_KWH = 0.40;

    public function capacity(SolarFarm $farm): float
    {
        if (! $farm->relationLoaded('panels')) {
            $farm->load('panels');
        }

        // Inactive catalogue models still contribute when physically installed.
        return round($farm->panels->sum(fn ($panel) => (float) $panel->nominal_power_kw * $panel->pivot->quantity), 3);
    }

    public function co2(float $kwh): float
    {
        return round($kwh * self::CO2_KG_PER_KWH, 2);
    }

    public function deviation(float $real, float $expected): ?float
    {
        return $expected > 0 ? round(($expected - $real) / $expected * 100, 2) : null;
    }

    public function isAlert(float $real, float $expected): bool
    {
        // Epsilon only absorbs floating point representation at the exact 80% boundary.
        return $expected > 0 && $real <= ($expected * 0.8) + 1e-9;
    }
}
