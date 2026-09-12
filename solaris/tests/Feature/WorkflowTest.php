<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\Department;
use App\Models\GenerationRecord;
use App\Models\Projection;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use App\Services\EnergyService;
use App\Services\GenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo(CarbonImmutable::parse('2026-09-12 12:00:00'));
    }

    private function department(string $code = '01'): Department
    {
        return Department::firstOrCreate(['code' => $code], [
            'name' => $code === '01' ? 'Guatemala' : 'Izabal', 'latitude' => 14.63,
            'longitude' => -90.50, 'color' => '#10b981',
        ]);
    }

    private function panel(float $power = 0.5): SolarPanel
    {
        return SolarPanel::create([
            'brand' => 'Panel de prueba', 'model' => 'Modelo '.(SolarPanel::count() + 1),
            'nominal_power_kw' => $power, 'is_active' => true,
        ]);
    }

    private function farm(?Department $department = null, int $quantity = 10, int $families = 10): SolarFarm
    {
        $farm = SolarFarm::create($this->farmPayload(($department ?? $this->department())->id) + []);
        $farm->update(['families_count' => $families]);
        $farm->panels()->attach($this->panel(), ['quantity' => $quantity]);

        return $farm;
    }

    private function farmPayload(int $departmentId): array
    {
        return [
            'name' => 'Instalación '.(SolarFarm::count() + 1), 'department_id' => $departmentId,
            'location_name' => 'Zona de prueba', 'latitude' => 14.63, 'longitude' => -90.50,
            'families_count' => 10, 'is_active' => true, 'commissioned_at' => '2025-01-01',
        ];
    }

    private function generation(SolarFarm $farm, string $period, float $real, float $expected): GenerationRecord
    {
        return app(GenerationService::class)->save([
            'solar_farm_id' => $farm->id, 'period' => $period, 'real_kwh' => $real, 'expected_kwh' => $expected,
        ]);
    }

    public function test_authenticated_asset_workflow_recalculates_capacity_and_preserves_history_on_deactivation(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('/panels', [
            'brand' => 'Solar', 'model' => '550', 'nominal_power_kw' => 0.55, 'is_active' => true,
        ])->assertRedirect(route('panels.index'));
        $first = SolarPanel::firstOrFail();
        $payload = $this->farmPayload($this->department()->id) + [
            'panels' => [['solar_panel_id' => $first->id, 'quantity' => 100]], 'capacity_kw' => 999999,
        ];
        $this->postJson('/farms', $payload)->assertRedirect();
        $farm = SolarFarm::firstOrFail();
        $this->assertSame(55.0, app(EnergyService::class)->capacity($farm));
        $this->getJson('/api/v1/farms/'.$farm->id)->assertOk()->assertJsonPath('data.capacity_kw', 55)->assertJsonPath('data.panel_count', 100);

        $second = $this->panel(0.45);
        $payload['name'] = 'Instalación ampliada';
        $payload['panels'] = [
            ['solar_panel_id' => $first->id, 'quantity' => 80],
            ['solar_panel_id' => $second->id, 'quantity' => 50],
        ];
        $this->putJson('/farms/'.$farm->id, $payload)->assertRedirect(route('farms.show', $farm));
        $this->assertSame(66.5, app(EnergyService::class)->capacity($farm->fresh()));
        $this->assertDatabaseHas('farm_panel', ['solar_farm_id' => $farm->id, 'solar_panel_id' => $second->id, 'quantity' => 50]);
        $history = $this->generation($farm, '2026-08', 2000, 2500);

        $this->delete('/farms/'.$farm->id)->assertRedirect(route('farms.show', $farm));
        $this->assertFalse($farm->fresh()->is_active);
        $this->assertDatabaseHas('generation_records', ['id' => $history->id]);
        $this->assertDatabaseHas('alerts', ['generation_record_id' => $history->id, 'status' => 'active']);
        $this->assertSame(2, $farm->panels()->count());
        $this->getJson('/api/v1/farms/'.$farm->id)->assertOk()->assertJsonPath('data.is_active', false)->assertJsonPath('data.capacity_kw', 66.5);
    }

    public function test_guests_can_read_but_cannot_modify_any_assets_or_generation(): void
    {
        $farm = $this->farm();
        $record = $this->generation($farm, '2026-08', 100, 100);
        $this->getJson('/api/v1/farms/'.$farm->id)->assertOk();
        foreach (['/farms', '/panels', '/generations', '/projections'] as $url) {
            $this->post($url, [])->assertRedirect(route('login'));
        }
        $this->put('/farms/'.$farm->id, [])->assertRedirect(route('login'));
        $this->put('/generations/'.$record->id, [])->assertRedirect(route('login'));
        $this->delete('/farms/'.$farm->id)->assertRedirect(route('login'));
        $this->postJson('/farms', [])->assertUnauthorized();
        $this->assertTrue($farm->fresh()->is_active);
        $this->assertSame(1, GenerationRecord::count());
    }

    public function test_generation_http_workflow_evaluates_alerts_and_rejects_duplicate_future_and_impossible_output(): void
    {
        $this->actingAs(User::factory()->create());
        $farm = $this->farm();
        $data = ['solar_farm_id' => $farm->id, 'period' => '2026-08', 'real_kwh' => 800, 'expected_kwh' => 1000];
        $this->postJson('/generations', $data)->assertRedirect(route('generations.index'));
        $record = GenerationRecord::firstOrFail();
        $this->assertSame('active', $record->alert->status);
        $this->putJson('/generations/'.$record->id, array_replace($data, ['real_kwh' => 950]))->assertRedirect();
        $this->assertSame('resolved', $record->fresh()->alert->status);
        $this->assertSame(1, Alert::count());
        $this->postJson('/generations', $data)->assertUnprocessable()->assertJsonValidationErrors('period');
        foreach (['2026-09', '2026-10', '2026-07-15'] as $period) {
            $this->postJson('/generations', array_replace($data, ['period' => $period]))->assertUnprocessable()->assertJsonValidationErrors('period');
        }
        $this->postJson('/generations', array_replace($data, [
            'period' => '2026-07', 'real_kwh' => 4000, 'expected_kwh' => 4000,
        ]))->assertUnprocessable()->assertJsonValidationErrors(['real_kwh', 'expected_kwh']);
        $this->postJson('/generations', array_replace($data, [
            'period' => '2026-07', 'real_kwh' => -1,
        ]))->assertUnprocessable()->assertJsonValidationErrors('real_kwh');
        $this->assertSame(1, GenerationRecord::count());
    }

    public function test_asset_validation_rejects_coordinates_duplicates_invalid_quantities_and_inactive_catalogue_models(): void
    {
        $this->actingAs(User::factory()->create());
        $panel = $this->panel();
        $payload = $this->farmPayload($this->department()->id);
        $this->postJson('/farms', array_replace($payload, ['latitude' => 0, 'longitude' => 0]))
            ->assertUnprocessable()->assertJsonValidationErrors(['latitude', 'longitude']);
        $this->postJson('/farms', $payload + ['panels' => [
            ['solar_panel_id' => $panel->id, 'quantity' => 5], ['solar_panel_id' => $panel->id, 'quantity' => 10],
        ]])->assertUnprocessable()->assertJsonValidationErrors('panels.0.solar_panel_id');
        $this->postJson('/farms', $payload + ['panels' => [['solar_panel_id' => $panel->id, 'quantity' => -1]]])
            ->assertUnprocessable()->assertJsonValidationErrors('panels.0.quantity');
        $panel->update(['is_active' => false]);
        $this->postJson('/farms', $payload + ['panels' => [['solar_panel_id' => $panel->id, 'quantity' => 10]]])
            ->assertUnprocessable()->assertJsonValidationErrors('panels.0.solar_panel_id');
        $this->postJson('/panels', ['brand' => 'Solar', 'model' => 'Impossible', 'nominal_power_kw' => 0, 'is_active' => true])
            ->assertUnprocessable()->assertJsonValidationErrors('nominal_power_kw');
        $this->assertSame(0, SolarFarm::count());
    }

    public function test_optional_null_panel_list_is_accepted_for_an_empty_installation(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('/farms', $this->farmPayload($this->department()->id) + ['panels' => null])->assertRedirect();
        $farm = SolarFarm::firstOrFail();
        $this->assertSame(0.0, app(EnergyService::class)->capacity($farm));
    }

    public function test_api_statistics_and_generation_share_department_and_month_filters(): void
    {
        $department = $this->department();
        $first = $this->farm($department, 10, 10);
        $second = $this->farm($department, 20, 20);
        $other = $this->farm($this->department('18'), 10, 30);
        $this->generation($first, '2026-07', 1000, 1000);
        $included = $this->generation($first, '2026-08', 800, 1000);
        $this->generation($second, '2026-08', 1400, 1400);
        $this->generation($other, '2026-08', 3000, 3000);
        $query = '?department_id='.$department->id.'&from=2026-08&to=2026-08';

        $this->getJson('/api/v1/statistics'.$query)->assertOk()
            ->assertJsonPath('data.totals.farms', 2)->assertJsonPath('data.totals.panels', 30)
            ->assertJsonPath('data.totals.capacity_kw', 15)->assertJsonPath('data.totals.families', 30)
            ->assertJsonPath('data.totals.generation_kwh', 2200)->assertJsonPath('data.totals.expected_kwh', 2400)
            ->assertJsonPath('data.totals.co2_kg', 880)->assertJsonPath('data.totals.co2_tonnes', 0.88)
            ->assertJsonPath('data.totals.alerts', 1)->assertJsonCount(1, 'data.departments')
            ->assertJsonPath('data.departments.0.generation_kwh', 2200)->assertJsonCount(1, 'data.trend')
            ->assertJsonPath('data.trend.0.period', '2026-08')->assertJsonPath('data.trend.0.real_kwh', 2200)
            ->assertJsonPath('meta.co2_factor_kg_per_kwh', 0.4);
        $this->getJson('/api/v1/generations'.$query)->assertOk()->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)->assertJsonPath('data.0.id', $included->id)
            ->assertJsonPath('data.0.has_alert', true)->assertJsonPath('data.0.deviation_percent', 20)
            ->assertJsonPath('data.0.co2_kg', 320);
        $this->getJson('/api/v1/farms?department_id='.$department->id)->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_api_accepts_open_ended_to_filter_without_from(): void
    {
        $farm = $this->farm();
        $this->generation($farm, '2026-07', 100, 100);
        $this->generation($farm, '2026-08', 200, 200);
        $this->getJson('/api/v1/statistics?to=2026-07')->assertOk()
            ->assertJsonPath('data.totals.generation_kwh', 100)->assertJsonCount(1, 'data.trend');
        $this->getJson('/api/v1/generations?to=2026-07')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_api_zero_data_schemas_bad_filters_and_missing_farms_are_predictable(): void
    {
        $farm = $this->farm();
        $this->getJson('/api/v1/departments')->assertOk()->assertJsonStructure(['data' => [['id', 'code', 'name', 'latitude', 'longitude', 'color']]]);
        $this->getJson('/api/v1/farms/'.$farm->id)->assertOk()->assertJsonStructure(['data' => [
            'id', 'name', 'department' => ['id', 'name'], 'capacity_kw', 'panel_count', 'is_active',
            'panels' => [['id', 'brand', 'model', 'nominal_power_kw', 'quantity']],
        ]]);
        $this->getJson('/api/v1/statistics')->assertOk()->assertJsonPath('data.totals.generation_kwh', 0)
            ->assertJsonPath('data.totals.co2_kg', 0)->assertJsonPath('data.totals.performance', null)
            ->assertJsonPath('data.totals.alerts', 0)->assertJsonCount(0, 'data.trend');
        $this->getJson('/api/v1/generations')->assertOk()->assertJsonCount(0, 'data')->assertJsonPath('meta.total', 0);
        foreach ([
            'department_id=99999' => 'department_id', 'from=bad' => 'from',
            'from=2026-08&to=2026-07' => 'to', 'page=0' => 'page',
        ] as $query => $field) {
            $this->getJson('/api/v1/statistics?'.$query)->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->getJson('/api/v1/farms/99999')->assertNotFound()->assertJsonStructure(['message']);
        $this->generation($farm, '2026-08', 0, 0);
        $this->getJson('/api/v1/generations')->assertOk()->assertJsonPath('data.0.has_alert', false)
            ->assertJsonPath('data.0.deviation_percent', null);
    }

    public function test_projection_http_uses_only_closed_months_and_repeated_submission_preserves_original(): void
    {
        $this->actingAs(User::factory()->create());
        $farm = $this->farm();
        $this->generation($farm, '2026-06', 3000, 3000);
        $this->generation($farm, '2026-07', 3100, 3100);
        $this->generation($farm, '2026-08', 3100, 3100);
        // Imported partial current-month data must never influence a forecast.
        $this->generation($farm, '2026-09', 999999, 999999);
        $data = ['solar_farm_id' => $farm->id, 'horizon' => 3];
        $this->postJson('/projections', $data)->assertRedirect(route('projections.index', ['solar_farm_id' => $farm->id]));
        $forecast = Projection::orderBy('period')->firstOrFail();
        $this->assertSame('3000.00', $forecast->projected_kwh);
        $this->assertSame('2026-08-01', $forecast->training_through->toDateString());
        $farm->generations()->where('period', '2026-08-01')->update(['real_kwh' => 6200]);
        $this->postJson('/projections', $data)->assertRedirect();
        $this->assertSame('3000.00', $forecast->fresh()->projected_kwh);
        $this->assertSame(3, Projection::count());
        $this->postJson('/projections', array_replace($data, ['horizon' => 13]))->assertUnprocessable()->assertJsonValidationErrors('horizon');
        $this->postJson('/projections', ['solar_farm_id' => $this->farm()->id, 'horizon' => 3])
            ->assertUnprocessable()->assertJsonValidationErrors('solar_farm_id');
    }
}
