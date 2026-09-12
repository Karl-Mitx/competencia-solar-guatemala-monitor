<?php

namespace App\Services;

use Carbon\CarbonImmutable;

class ProjectionReadiness
{
    public function requiredMonths(?CarbonImmutable $reference = null): array
    {
        $month = ($reference ?? CarbonImmutable::now())->startOfMonth();

        return array_map(fn ($offset) => $month->subMonths($offset)->format('Y-m'), [3, 2, 1]);
    }

    public function missingMonths(array $recordedMonths, ?CarbonImmutable $reference = null): array
    {
        return array_values(array_diff($this->requiredMonths($reference), $recordedMonths));
    }
}
