<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = ['generation_record_id', 'status', 'resolved_at'];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function generationRecord(): BelongsTo
    {
        return $this->belongsTo(GenerationRecord::class);
    }

    public function severity(): string
    {
        $record = $this->generationRecord;
        if (! $record || $record->expected_kwh <= 0) {
            return 'attention';
        }

        $performance = $record->real_kwh / $record->expected_kwh * 100;

        return $performance < 60 ? 'critical' : ($performance <= 70 ? 'high' : 'attention');
    }

    public function recommendation(): string
    {
        return match ($this->severity()) {
            'critical' => 'Revisar la instalación y el registro de generación con prioridad.',
            'high' => 'Verificar el funcionamiento de la granja y sus paneles.',
            default => 'Dar seguimiento a la próxima lectura de generación.',
        };
    }
}
