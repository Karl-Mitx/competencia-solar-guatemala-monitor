<?php

namespace Tests\Unit;

use App\Services\ProjectionReadiness;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class ProjectionReadinessTest extends TestCase
{
    public function test_required_months_cross_year_boundary(): void
    {
        $this->assertSame(['2025-10', '2025-11', '2025-12'], (new ProjectionReadiness)->requiredMonths(CarbonImmutable::parse('2026-01-31')));
    }

    public function test_old_and_current_records_do_not_replace_missing_closed_months(): void
    {
        $service = new ProjectionReadiness;
        $reference = CarbonImmutable::parse('2026-09-12');
        $this->assertSame(['2026-07'], $service->missingMonths(['2025-01', '2026-06', '2026-08', '2026-09'], $reference));
        $this->assertSame([], $service->missingMonths(['2026-06', '2026-07', '2026-08'], $reference));
        $this->assertCount(3, $service->missingMonths([], $reference));
    }
}
