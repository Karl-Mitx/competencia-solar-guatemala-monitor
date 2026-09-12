<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComparisonValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_period_validation_allows_open_ranges_but_not_reversed_dates(): void
    {
        $this->get('/farms/compare?to=2026-08')->assertOk();
        $this->getJson('/farms/compare?from=2026-08&to=2026-07')->assertUnprocessable()->assertJsonValidationErrors('to');
    }

    public function test_empty_comparison_page_is_available(): void
    {
        $this->get('/farms/compare')->assertOk()->assertSee('Elige al menos dos granjas');
    }

    public function test_comparison_rejects_malformed_selection_instead_of_crashing(): void
    {
        $this->getJson('/farms/compare?farm_ids=invalid')
            ->assertUnprocessable()->assertJsonValidationErrors('farm_ids');
    }

    public function test_comparison_does_not_silently_truncate_excess_selections(): void
    {
        $this->getJson('/farms/compare?'.http_build_query(['farm_ids' => [1, 2, 3, 4, 5]]))
            ->assertUnprocessable()->assertJsonValidationErrors('farm_ids');
    }
}
