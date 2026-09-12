<?php

namespace App\Services;

class SolarSimulation
{
    public function calculate(array $input): array
    {
        $capacity = $input['panels'] * $input['watts'] / 1000;
        $daily = $capacity * $input['sun_hours'] * (1 - $input['loss_percent'] / 100);
        $annual = $daily * 365;
        $monthly = $annual / 12;
        $savings = $annual * $input['self_consumption'] / 100 * $input['tariff'];

        return [
            'capacity_kw' => $capacity,
            'annual_kwh' => $annual,
            'monthly_kwh' => $monthly,
            'co2_tonnes' => $annual * $input['co2_factor'] / 1000,
            'equivalent_households' => $monthly / $input['household_kwh'],
            'annual_savings' => $savings,
            'payback_years' => $savings > 0 ? $input['investment'] / $savings : null,
        ];
    }
}
