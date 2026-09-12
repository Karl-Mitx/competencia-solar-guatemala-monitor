<?php

namespace App\Models;

use Database\Factories\GenerationRecordFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class GenerationRecord extends Model
{
    /** @use HasFactory<GenerationRecordFactory> */
    use HasFactory;
    protected $fillable = ['solar_farm_id', 'period', 'real_kwh', 'expected_kwh'];

    protected function casts(): array
    {
        return ['period' => 'date', 'real_kwh' => 'decimal:2', 'expected_kwh' => 'decimal:2'];
    }

    protected function period(): Attribute
    {
        // SQLite must receive a date, not Laravel's default date-time serialization.
        return Attribute::make(set: fn ($value) => Carbon::parse($value)->toDateString());
    }

    public function solarFarm(): BelongsTo
    {
        return $this->belongsTo(SolarFarm::class);
    }

    public function alert(): HasOne
    {
        return $this->hasOne(Alert::class);
    }
}
