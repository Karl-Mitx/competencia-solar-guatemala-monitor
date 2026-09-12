<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PanelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100', Rule::unique('solar_panels')->where('brand', $this->input('brand'))->ignore($this->route('panel'))],
            'nominal_power_kw' => ['required', 'numeric', 'between:0.001,10', 'decimal:0,3'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
