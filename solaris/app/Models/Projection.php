<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Projection extends Model
{
    protected $fillable = [
        'solar_farm_id', 'period', 'projected_kwh', 'method', 'training_through', 'sample_size', 'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'period' => 'date', 'projected_kwh' => 'decimal:2', 'training_through' => 'date',
            'sample_size' => 'integer', 'generated_at' => 'datetime',
        ];
    }

    protected function period(): Attribute
    {
        return Attribute::make(set: fn ($value) => Carbon::parse($value)->toDateString());
    }

    protected function trainingThrough(): Attribute
    {
        return Attribute::make(set: fn ($value) => Carbon::parse($value)->toDateString());
    }

    public function solarFarm(): BelongsTo
    {
        return $this->belongsTo(SolarFarm::class);
    }
}
