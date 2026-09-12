<?php

namespace Tests\Feature;

use App\Services\SolarSimulation;
use Tests\TestCase;

class SolarSimulationTest extends TestCase
{
    public function test_example_is_public_and_explains_assumptions(): void
    {
        $this->get('/simulator')->assertOk()->assertSee('Resultados orientativos')->assertSee('13,140.00')->assertSee('no mediciones locales');
    }

    public function test_invalid_inputs_are_rejected(): void
    {
        $this->getJson('/simulator?panels=-1&loss_percent=101&household_kwh=0')->assertUnprocessable()->assertJsonValidationErrors(['panels', 'loss_percent', 'household_kwh']);
    }

    public function test_zero_production_has_no_payback_and_all_formulas_match(): void
    {
        $input = ['panels'=>20,'watts'=>450,'sun_hours'=>5,'loss_percent'=>20,'household_kwh'=>150,'co2_factor'=>0.4,'self_consumption'=>80,'tariff'=>1,'investment'=>50000];
        $result = (new SolarSimulation)->calculate($input);
        $this->assertEqualsWithDelta(13140, $result['annual_kwh'], 0.001);
        $this->assertEqualsWithDelta(5.256, $result['co2_tonnes'], 0.001);
        $this->assertEqualsWithDelta(7.3, $result['equivalent_households'], 0.001);
        $this->assertEqualsWithDelta(10512, $result['annual_savings'], 0.001);
        $this->assertEqualsWithDelta(50000 / 10512, $result['payback_years'], 0.001);
        $input['loss_percent'] = 100;
        $result = (new SolarSimulation)->calculate($input);
        $this->assertEquals(0, $result['annual_kwh']);
        $this->assertNull($result['payback_years']);
    }
}
