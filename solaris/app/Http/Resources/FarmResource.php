<?php

namespace App\Http\Resources;

use App\Services\EnergyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $real = (float) $this->whenLoaded('generations', fn () => $this->generations->sum('real_kwh'), 0);
        $expected = (float) $this->whenLoaded('generations', fn () => $this->generations->sum('expected_kwh'), 0);

        return ['id' => $this->id, 'name' => $this->name, 'department' => ['id' => $this->department_id, 'name' => $this->department->name],
            'location_name' => $this->location_name, 'latitude' => (float) $this->latitude, 'longitude' => (float) $this->longitude,
            'families_count' => $this->families_count, 'is_active' => $this->is_active,
            'capacity_kw' => app(EnergyService::class)->capacity($this->resource), 'panel_count' => (int) $this->panels->sum('pivot.quantity'),
            'panels' => $this->panels->map(fn ($p) => ['id' => $p->id, 'brand' => $p->brand, 'model' => $p->model, 'nominal_power_kw' => (float) $p->nominal_power_kw, 'quantity' => $p->pivot->quantity]),
            'commissioned_at' => $this->commissioned_at?->toDateString(), 'generation' => ['real_kwh' => round($real, 2), 'expected_kwh' => round($expected, 2),
                'performance' => $expected > 0 ? round($real / $expected * 100, 1) : null, 'co2_tonnes' => round($real * 0.4 / 1000, 3)]];
    }
}
