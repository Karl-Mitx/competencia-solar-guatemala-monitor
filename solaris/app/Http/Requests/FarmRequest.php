<?php

namespace App\Http\Requests;

use App\Models\SolarPanel;
use Illuminate\Foundation\Http\FormRequest;

class FarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'location_name' => ['required', 'string', 'max:180'],
            'latitude' => ['required', 'numeric', 'between:13.5,17.9'],
            'longitude' => ['required', 'numeric', 'between:-92.3,-88.1'],
            'families_count' => ['required', 'integer', 'between:0,10000000'],
            'is_active' => ['required', 'boolean'],
            'commissioned_at' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'panels' => ['nullable', 'array', 'max:50'],
            'panels.*.solar_panel_id' => ['required', 'integer', 'distinct', 'exists:solar_panels,id'],
            'panels.*.quantity' => ['required', 'integer', 'between:1,10000000'],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $assigned = $this->route('farm')?->panels()->pluck('solar_panels.id')->all() ?? [];
            foreach ($this->input('panels') ?? [] as $index => $row) {
                $panel = SolarPanel::find($row['solar_panel_id']);
                if (! $panel->is_active && ! in_array($panel->id, $assigned)) {
                    $validator->errors()->add("panels.$index.solar_panel_id", 'No puedes instalar un modelo de panel inactivo.');
                }
            }
        }];
    }
}
