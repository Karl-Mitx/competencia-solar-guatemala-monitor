<?php

namespace App\Models;

use Database\Factories\SolarPanelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SolarPanel extends Model
{
    /** @use HasFactory<SolarPanelFactory> */
    use HasFactory;
    protected $fillable = ['brand', 'model', 'nominal_power_kw', 'is_active'];

    protected function casts(): array
    {
        return ['nominal_power_kw' => 'decimal:3', 'is_active' => 'boolean'];
    }

    public function solarFarms(): BelongsToMany
    {
        return $this->belongsToMany(SolarFarm::class, 'farm_panel')->withPivot('quantity')->withTimestamps();
    }
}
