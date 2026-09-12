<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'solar_farm_id' => ['nullable', 'integer', 'exists:solar_farms,id'],
            'from' => ['nullable', 'date_format:Y-m'],
            'to' => array_filter(['nullable', 'date_format:Y-m', $this->filled('from') ? 'after_or_equal:from' : null]),
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive,resolved'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
