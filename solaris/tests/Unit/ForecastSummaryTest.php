<?php

namespace Tests\Unit;

use App\Services\ForecastSummary;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ForecastSummaryTest extends TestCase
{
    public function test_summarizes_horizon_and_variability(): void
    {
        $result = (new ForecastSummary)->summarize(new Collection([['projected_kwh' => 100], ['projected_kwh' => 120]]), new Collection([['real_kwh' => 100], ['real_kwh' => 110], ['real_kwh' => 90]]));
        $this->assertSame(220.0, $result['total_kwh']);
        $this->assertSame(2, $result['months']);
        $this->assertSame('Estable', $result['confidence']);
    }
}
