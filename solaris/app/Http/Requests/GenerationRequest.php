<?php

namespace App\Http\Requests;

use App\Models\SolarFarm;
use App\Services\EnergyService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->period) && preg_match('/^\d{4}-\d{2}$/', $this->period)) {
            $this->merge(['period' => $this->period.'-01']);
        }
    }

    public function rules(): array
    {
        return [
            'solar_farm_id' => ['required', 'integer', 'exists:solar_farms,id'],
            'period' => ['required', 'date_format:Y-m-d', 'before:'.now()->startOfMonth()->toDateString(),
                Rule::unique('generation_records')->where('solar_farm_id', $this->input('solar_farm_id'))->ignore($this->route('generation'))],
            'real_kwh' => ['required', 'numeric', 'between:0,999999999', 'decimal:0,2'],
            'expected_kwh' => ['required', 'numeric', 'between:0,999999999', 'decimal:0,2'],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $period = Carbon::parse($this->period);
            if ($period->day !== 1) {
                $validator->errors()->add('period', 'El período debe comenzar el primer día del mes.');
            }
            $farm = SolarFarm::with('panels')->find($this->solar_farm_id);
            if (! $farm->is_active) {
                $validator->errors()->add('solar_farm_id', 'La granja está inactiva. Reactívala antes de registrar generación.');
            }
            if ($farm->commissioned_at && $period->endOfMonth()->lt($farm->commissioned_at)) {
                $validator->errors()->add('period', 'El período es anterior al inicio de operación de la granja.');
            }
            $maximum = app(EnergyService::class)->capacity($farm) * 24 * $period->daysInMonth;
            foreach (['real_kwh', 'expected_kwh'] as $field) {
                if ((float) $this->input($field) > $maximum + 0.01) {
                    $validator->errors()->add($field, 'El valor supera la capacidad física de los paneles durante el mes ('.number_format($maximum, 2).' kWh).');
                }
            }
        }];
    }
}
