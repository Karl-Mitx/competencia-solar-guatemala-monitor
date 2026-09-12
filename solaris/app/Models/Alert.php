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
}
