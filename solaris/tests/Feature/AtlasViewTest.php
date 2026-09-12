<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\SolarFarm;
use App\Models\User;
use App\Services\GenerationService;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtlasViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_atlas_does_not_present_missing_expectation_as_zero_performance(): void
    {
        $this->withoutVite();
        $this->get('/')->assertOk()->assertSee('Sin base esperada')->assertSee('Sin alertas para estos filtros');
    }

    public function test_atlas_renders_real_alert_deviation_and_keeps_period_filters(): void
    {
        $this->withoutVite();
        $department = Department::create(['code' => '01', 'name' => 'Guatemala', 'latitude' => 14.63, 'longitude' => -90.5, 'color' => '#267456']);
        $farm = SolarFarm::create(['department_id' => $department->id, 'name' => 'Granja del atlas', 'location_name' => 'Guatemala', 'latitude' => 14.63, 'longitude' => -90.5, 'families_count' => 10, 'is_active' => true]);
        $period = now()->subMonthNoOverflow()->format('Y-m');
        app(GenerationService::class)->save(['solar_farm_id' => $farm->id, 'period' => $period, 'real_kwh' => 70, 'expected_kwh' => 100]);

        $this->get('/?from='.$period.'&to='.$period)->assertOk()
            ->assertSee('70.0%')->assertSee('−30.0%')->assertSee('Granja del atlas')
            ->assertSee(route('reports', ['from' => $period, 'to' => $period, 'department_id' => $department->id]));
    }

    public function test_projection_view_distinguishes_saved_forecasts_and_retrospective_comparisons(): void
    {
        $this->withoutVite();
        $this->seed(DemoSeeder::class);
        $farm = SolarFarm::where('is_active', true)->firstOrFail();
        $this->get('/projections?solar_farm_id='.$farm->id)->assertOk()
            ->assertSee('PRÓXIMO PERÍODO GUARDADO')->assertSee('Retrospectiva')
            ->assertSee('Bitácora de proyecciones')->assertSee('Pendiente');
        $this->actingAs(User::factory()->create());
        $farm->update(['is_active' => false]);
        $this->get('/projections?solar_farm_id='.$farm->id)->assertOk()
            ->assertSee('Reactiva la granja')->assertSee('disabled', false);
    }

    public function test_projection_view_supports_an_empty_inventory(): void
    {
        $this->withoutVite();
        $this->get('/projections')->assertOk()->assertSee('Primero, una granja solar');
    }
}
