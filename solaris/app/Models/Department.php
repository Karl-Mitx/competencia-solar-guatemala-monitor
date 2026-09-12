<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['code', 'name', 'latitude', 'longitude', 'color'];

    protected function casts(): array
    {
        return ['latitude' => 'float', 'longitude' => 'float'];
    }

    public function solarFarms(): HasMany
    {
        return $this->hasMany(SolarFarm::class);
    }
}
