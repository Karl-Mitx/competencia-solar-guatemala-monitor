<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\SolarFarm;
use App\Services\GenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TerritoryMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_distinguishes_zero_missing_and_excluded_departments(): void
    {
        $this->withoutVite();
        $first = Department::create(['code' => '01', 'name' => 'Guatemala', 'latitude' => 14.63, 'longitude' => -90.5, 'color' => '#267456']);
        $second = Department::create(['code' => '02', 'name' => 'El Progreso', 'latitude' => 14.85, 'longitude' => -90.06, 'color' => '#267456']);
        $farm = SolarFarm::create(['department_id' => $first->id, 'name' => 'Sol', 'location_name' => 'Guatemala', 'latitude' => 14.63, 'longitude' => -90.5, 'families_count' => 10, 'is_active' => true]);
        app(GenerationService::class)->save(['solar_farm_id' => $farm->id, 'period' => '2026-01', 'real_kwh' => 0, 'expected_kwh' => 100]);
        app(GenerationService::class)->save(['solar_farm_id' => $farm->id, 'period' => '2026-02', 'real_kwh' => 200, 'expected_kwh' => 200]);
        $read = function (string $query) {
            $response = $this->get('/map'.$query)->assertOk();
            preg_match('/id="territory-data">(.*?)<\/script>/s', $response->getContent(), $matches);

            return json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR);
        };
        $january = $read('?from=2026-01&to=2026-01');
        $byCode = collect($january['departments'])->keyBy('code');
        $this->assertSame(1, $byCode['01']['reading']['generation_records']);
        $this->assertEquals(0, $byCode['01']['reading']['generation_kwh']);
        $this->assertSame(0, $byCode['02']['reading']['generation_records']);
        $february = $read('?from=2026-02&to=2026-02&department_id='.$first->id);
        $this->assertEquals(200, $february['totals']['generation_kwh']);
        $this->assertEquals(0.08, $february['totals']['co2_tonnes']);
        $this->assertNull(collect($february['departments'])->firstWhere('code', '02')['reading']);
    }

    public function test_map_has_a_selector_and_summary_without_farms(): void
    {
        $this->withoutVite();
        $this->get('/map')->assertOk()->assertSee('territory-select', false)->assertSee('territory-reading', false);
    }
}
