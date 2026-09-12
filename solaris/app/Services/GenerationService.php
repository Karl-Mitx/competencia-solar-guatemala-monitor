<?php

namespace App\Services;

use App\Models\GenerationRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GenerationService
{
    public function __construct(private readonly EnergyService $energy) {}

    public function save(array $data, ?GenerationRecord $record = null): GenerationRecord
    {
        if (isset($data['period']) && is_string($data['period']) && preg_match('/^\d{4}-\d{2}$/', $data['period'])) {
            $data['period'] .= '-01';
        }

        $validated = Validator::make($data, [
            'solar_farm_id' => ['required', 'integer', 'exists:solar_farms,id'],
            'period' => [
                'required', 'date_format:Y-m-d', 'before_or_equal:'.now()->endOfMonth()->toDateString(),
                function ($attribute, $value, $fail) {
                    if (is_string($value) && ! str_ends_with($value, '-01')) {
                        $fail('El período debe corresponder al primer día de un mes.');
                    }
                },
                Rule::unique('generation_records')->where('solar_farm_id', $data['solar_farm_id'] ?? null)->ignore($record?->id),
            ],
            'real_kwh' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'expected_kwh' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
        ], [
            'period.unique' => 'Esta granja ya tiene un registro para ese mes. Edite el registro existente.',
            'period.before_or_equal' => 'La generación real no puede registrarse para meses futuros.',
        ])->validate();

        // SQLite does not enforce DECIMAL scale; persist the same precision on every engine.
        $validated['real_kwh'] = round((float) $validated['real_kwh'], 2);
        $validated['expected_kwh'] = round((float) $validated['expected_kwh'], 2);

        return DB::transaction(function () use ($validated, $record) {
            $record ??= new GenerationRecord;
            $record->fill($validated)->save();
            // Compare the same two-decimal quantities that are stored and displayed.
            $record->refresh();

            if ($this->energy->isAlert((float) $record->real_kwh, (float) $record->expected_kwh)) {
                $record->alert()->updateOrCreate([], ['status' => 'active', 'resolved_at' => null]);
            } elseif ($alert = $record->alert()->first()) {
                if ($alert->status !== 'resolved') {
                    $alert->update(['status' => 'resolved', 'resolved_at' => now()]);
                }
            }

            return $record->load('solarFarm.department', 'alert');
        });
    }
}
