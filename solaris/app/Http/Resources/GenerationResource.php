<?php

namespace App\Http\Resources;

use App\Services\EnergyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenerationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $energy = app(EnergyService::class);

        return ['id' => $this->id, 'solar_farm_id' => $this->solar_farm_id, 'farm_name' => $this->solarFarm->name,
            'period' => $this->period->format('Y-m'), 'real_kwh' => (float) $this->real_kwh, 'expected_kwh' => (float) $this->expected_kwh,
            'co2_kg' => $energy->co2((float) $this->real_kwh), 'deviation_percent' => $energy->deviation((float) $this->real_kwh, (float) $this->expected_kwh),
            'has_alert' => $energy->isAlert((float) $this->real_kwh, (float) $this->expected_kwh)];
    }
}
