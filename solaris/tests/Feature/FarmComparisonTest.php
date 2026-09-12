<?php

namespace Tests\Feature;

use App\Models\SolarFarm;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_comparison_filters_records_preserves_order_and_retains_dates(): void
    {
        $this->seed(DemoSeeder::class);
        $ids = SolarFarm::orderBy('id')->limit(2)->pluck('id')->reverse()->values()->all();
        $month = \App\Models\GenerationRecord::where('solar_farm_id', $ids[0])->orderBy('period')->first()->period->format('Y-m');
        $response = $this->get('/farms/compare?'.http_build_query(['farm_ids' => $ids, 'from' => $month, 'to' => $month]));
        $response->assertOk()->assertSee('Cumplimiento y cobertura')->assertSee('name="from" value="'.$month.'"', false);
        $farms = $response->viewData('farms');
        $this->assertSame($ids, $farms->pluck('id')->all());
        foreach ($farms as $farm) {
            $this->assertCount(1, $farm->generations);
            $this->assertSame($month, $farm->generations->first()->period->format('Y-m'));
        }
    }

    public function test_empty_period_has_no_expected_baseline(): void
    {
        $this->seed(DemoSeeder::class);
        $ids = SolarFarm::orderBy('id')->limit(2)->pluck('id')->all();
        $response = $this->get('/farms/compare?'.http_build_query(['farm_ids' => $ids, 'from' => '1900-01', 'to' => '1900-02']));
        $response->assertOk()->assertSee('Sin base esperada')->assertSee('Sin registros');
        foreach ($response->viewData('farms') as $farm) {
            $this->assertCount(0, $farm->generations);
            $this->assertNotEmpty($farm->panels);
        }
    }

    public function test_farms_can_be_compared_with_core_metrics(): void
    {
        $this->seed(DemoSeeder::class);
        $ids = SolarFarm::query()->limit(2)->pluck('id')->all();

        $this->getJson('/api/v1/farms/compare?farm_ids[]='.$ids[0].'&farm_ids[]='.$ids[1])
            ->assertOk()->assertJsonPath('meta.compared', 2)
            ->assertJsonStructure(['data' => [['id', 'name', 'capacity_kw', 'generation_kwh', 'performance', 'co2_tonnes']]]);
    }

    public function test_farm_comparison_requires_at_least_two_farms(): void
    {
        $this->seed(DemoSeeder::class);
        $id = SolarFarm::query()->value('id');

        $this->getJson('/api/v1/farms/compare?farm_ids[]='.$id)->assertStatus(422)->assertJsonValidationErrors('farm_ids');
    }
}
