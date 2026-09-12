<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolarFarm extends Model
{
    protected $fillable = [
        'department_id', 'name', 'location_name', 'latitude', 'longitude',
        'families_count', 'is_active', 'commissioned_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float', 'longitude' => 'float', 'families_count' => 'integer',
            'is_active' => 'boolean', 'commissioned_at' => 'date',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function panels(): BelongsToMany
    {
        return $this->belongsToMany(SolarPanel::class, 'farm_panel')->withPivot('quantity')->withTimestamps();
    }

    public function generations(): HasMany
    {
        return $this->hasMany(GenerationRecord::class);
    }

    public function projections(): HasMany
    {
        return $this->hasMany(Projection::class);
    }
}
