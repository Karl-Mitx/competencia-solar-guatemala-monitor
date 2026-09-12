<?php

namespace Tests\Unit;

use App\Services\MonthlyTrend;
use PHPUnit\Framework\TestCase;

class MonthlyTrendTest extends TestCase
{
    public function test_growth_across_year_boundary_and_unsorted_rows(): void
    {
        $result = (new MonthlyTrend)->summarize([['period'=>'2026-01','real_kwh'=>120],['period'=>'2025-12','real_kwh'=>100]]);
        $this->assertSame(20.0, $result['percent']);
        $this->assertSame('up', $result['direction']);
    }

    public function test_missing_month_or_zero_baseline_is_not_a_valid_comparison(): void
    {
        $service = new MonthlyTrend;
        $this->assertNull($service->summarize([])['percent']);
        $this->assertNull($service->summarize([['period'=>'2026-01','real_kwh'=>10],['period'=>'2026-03','real_kwh'=>20]])['percent']);
        $this->assertNull($service->summarize([['period'=>'2026-01','real_kwh'=>0],['period'=>'2026-02','real_kwh'=>20]])['percent']);
    }
}
