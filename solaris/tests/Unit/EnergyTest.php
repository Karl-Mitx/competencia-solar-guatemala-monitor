<?php

namespace Tests\Unit;

use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Services\EnergyService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;
use PHPUnit\Framework\TestCase;

class EnergyTest extends TestCase
{
    public function test_capacity_comes_from_installed_panel_quantities_including_retired_catalogue_models(): void
    {
        $first = new SolarPanel(['nominal_power_kw' => 0.550, 'is_active' => true]);
        $first->setRelation('pivot', new Pivot(['quantity' => 120]));
        $second = new SolarPanel(['nominal_power_kw' => 0.330, 'is_active' => false]);
        $second->setRelation('pivot', new Pivot(['quantity' => 100]));
        $farm = new SolarFarm;
        $farm->setRelation('panels', new Collection([$first, $second]));

        $this->assertSame(99.0, (new EnergyService)->capacity($farm));
        $farm->setRelation('panels', new Collection);
        $this->assertSame(0.0, (new EnergyService)->capacity($farm));
    }

    public function test_co2_uses_the_required_factor_and_deviation_is_a_shortfall_percentage(): void
    {
        $energy = new EnergyService;
        $this->assertSame(400.0, $energy->co2(1000));
        $this->assertSame(0.0, $energy->co2(0));
        $this->assertSame(20.0, $energy->deviation(80, 100));
        $this->assertSame(-10.0, $energy->deviation(110, 100));
        $this->assertNull($energy->deviation(10, 0));
    }

    public function test_alert_boundary_is_inclusive_and_zero_expectation_has_no_alert(): void
    {
        $energy = new EnergyService;
        $this->assertTrue($energy->isAlert(80, 100));
        $this->assertTrue($energy->isAlert(79.99, 100));
        $this->assertTrue($energy->isAlert(26.68, 33.35));
        $this->assertFalse($energy->isAlert(80.01, 100));
        $this->assertFalse($energy->isAlert(80.01, 100.01));
        $this->assertFalse($energy->isAlert(0, 0));
        $this->assertTrue($energy->isAlert(0, 100));
    }
}
