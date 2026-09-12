<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportPrintTest extends TestCase
{
    use RefreshDatabase;
    public function test_printable_report_contains_summary_filters_and_table(): void
    {
        $this->withoutVite();
        $this->get('/reports/print?from=2026-01&to=2026-08')
            ->assertOk()->assertSee('Reporte energético nacional')
            ->assertSee('Generación real')->assertSee('Comparativo departamental')
            ->assertSee('Este documento es informativo');
    }
}
