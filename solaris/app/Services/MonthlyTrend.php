<?php

namespace App\Services;

use Carbon\CarbonImmutable;

class MonthlyTrend
{
    public function summarize(array $rows): array
    {
        usort($rows, fn ($a, $b) => strcmp($a['period'], $b['period']));
        $latest = array_pop($rows);
        $previous = array_pop($rows);
        $result = ['current' => $latest['period'] ?? null, 'previous' => $previous['period'] ?? null, 'percent' => null, 'direction' => 'unavailable'];
        if (!$latest || !$previous || $previous['real_kwh'] <= 0) {
            return $result;
        }
        if (CarbonImmutable::parse($previous['period'].'-01')->addMonth()->format('Y-m') !== $latest['period']) {
            return $result;
        }
        $percent = round(($latest['real_kwh'] - $previous['real_kwh']) / $previous['real_kwh'] * 100, 1);
        return array_merge($result, ['percent' => $percent, 'direction' => $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'stable')]);
    }
}
