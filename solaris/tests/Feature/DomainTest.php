<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\Department;
use App\Models\GenerationRecord;
use App\Models\Projection;
use App\Models\SolarFarm;
use App\Services\GenerationService;
use App\Services\ProjectionService;
use Carbon\CarbonImmutable;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DepartmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(CarbonImmutable::parse('2026-09-12 12:00:00'));
    }

    private function farm(): SolarFarm
    {
        $department = Department::firstOrCreate(['code' => '01'], [
            'name' => 'Guatemala', 'latitude' => 14.63, 'longitude' => -90.50, 'color' => '#10b981',
        ]);

        return SolarFarm::create([
            'name' => 'Granja de prueba', 'department_id' => $department->id,
            'location_name' => 'Guatemala', 'latitude' => 14.63, 'longitude' => -90.50,
            'families_count' => 10, 'is_active' => true,
        ]);
    }

    private function generation(SolarFarm $farm, string $period, float $real = 100, float $expected = 100): GenerationRecord
    {
        return app(GenerationService::class)->save([
            'solar_farm_id' => $farm->id, 'period' => $period, 'real_kwh' => $real, 'expected_kwh' => $expected,
        ]);
    }

    private function history(SolarFarm $farm): void
    {
        $this->generation($farm, '2026-06', 3000, 3000);
        $this->generation($farm, '2026-07', 3100, 3100);
        $this->generation($farm, '2026-08', 3100, 3100);
    }

    public function test_generation_automatically_creates_resolves_and_reopens_one_alert(): void
    {
        $farm = $this->farm();
        $record = $this->generation($farm, '2026-08', 80);
        $this->assertSame('active', $record->alert->status);
        $this->assertSame('2026-08-01', $record->period->toDateString());
        $service = app(GenerationService::class);
        $record = $service->save([
            'solar_farm_id' => $farm->id, 'period' => '2026-08', 'real_kwh' => 95, 'expected_kwh' => 100,
        ], $record);
        $this->assertSame('resolved', $record->alert->status);
        $this->assertNotNull($record->alert->resolved_at);
        $record = $service->save([
            'solar_farm_id' => $farm->id, 'period' => '2026-08', 'real_kwh' => 60, 'expected_kwh' => 100,
        ], $record);
        $this->assertSame('active', $record->alert->status);
        $this->assertNull($record->alert->resolved_at);
        $this->assertSame(1, Alert::count());
    }

    public function test_zero_expectation_does_not_create_an_alert_and_duplicates_are_rejected(): void
    {
        $farm = $this->farm();
        $this->generation($farm, '2026-08', 0, 0);
        $this->assertSame(0, Alert::count());
        $this->expectException(ValidationException::class);
        $this->generation($farm, '2026-08', 50, 100);
    }

    public function test_generation_persists_two_decimal_precision_consistently_on_sqlite(): void
    {
        $record = $this->generation($this->farm(), '2026-08', 80.006, 100);

        $this->assertSame('80.01', $record->real_kwh);
        $this->assertEquals(80.01, $record->getRawOriginal('real_kwh'));
        $this->assertNull($record->alert);
    }

    public function test_future_generation_and_negative_quantities_are_rejected_without_persisting(): void
    {
        $farm = $this->farm();
        foreach ([['2026-10', 100], ['2026-08', -1], ['2026-08-15', 100]] as [$period, $real]) {
            try {
                $this->generation($farm, $period, $real);
                $this->fail('Invalid generation was accepted.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
        $this->assertSame(0, GenerationRecord::count());
    }

    public function test_projection_uses_closed_month_daily_rates_and_never_current_month_actuals(): void
    {
        $farm = $this->farm();
        $this->history($farm);
        $this->generation($farm, '2026-09', 999999, 999999);
        $projections = app(ProjectionService::class)->generate($farm);

        $this->assertCount(3, $projections);
        $this->assertSame(['2026-09-01', '2026-10-01', '2026-11-01'], $projections->map(fn ($p) => $p->period->toDateString())->all());
        $this->assertSame([3000.0, 3100.0, 3000.0], $projections->map(fn ($p) => (float) $p->projected_kwh)->all());
        $this->assertSame('2026-08-01', $projections->first()->training_through->toDateString());
        $this->assertSame(3, $projections->first()->sample_size);
    }

    public function test_saved_projection_is_not_rewritten_when_actuals_or_history_change(): void
    {
        $farm = $this->farm();
        $this->history($farm);
        $service = app(ProjectionService::class);
        $forecast = $service->generate($farm)->first();
        $farm->generations()->where('period', '2026-08-01')->update(['real_kwh' => 6200]);
        $actual = $this->generation($farm, '2026-09', 2400, 3000);
        $regenerated = $service->generate($farm)->first();

        $this->assertSame($forecast->id, $regenerated->id);
        $this->assertSame('3000.00', $regenerated->projected_kwh);
        $this->assertSame(600.0, (float) $regenerated->projected_kwh - (float) $actual->real_kwh);
        $this->assertSame(3, Projection::count());
    }

    public function test_projection_rejects_insufficient_gapped_or_stale_history(): void
    {
        $farm = $this->farm();
        $this->generation($farm, '2026-04', 3000, 3000);
        $this->generation($farm, '2026-06', 3000, 3000);
        $this->generation($farm, '2026-08', 3100, 3100);
        $this->expectException(ValidationException::class);
        app(ProjectionService::class)->generate($farm);
    }

    public function test_retrospective_projection_excludes_later_actuals_and_is_labelled(): void
    {
        $farm = $this->farm();
        $this->history($farm);
        $this->generation($farm, '2026-05', 3100, 3100);
        $forecast = app(ProjectionService::class)->generate($farm, 1, CarbonImmutable::parse('2026-08-01'))->first();

        $this->assertSame('3100.00', $forecast->projected_kwh);
        $this->assertSame('2026-07-01', $forecast->training_through->toDateString());
        $this->assertStringContainsString('retrospectiva', $forecast->method);
    }

    public function test_demo_has_all_departments_diverse_assets_alerts_and_comparable_projections(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertSame(22, Department::count());
        $this->assertSame(34, SolarFarm::count());
        $this->assertSame(408, GenerationRecord::count());
        $this->assertSame(2, SolarFarm::where('is_active', false)->count());
        $this->assertGreaterThan(0, Alert::where('status', 'active')->count());
        $this->assertSame(198, Projection::count());
        $this->assertSame(102, Projection::where('period', '<', '2026-09-01')->count());
        $this->seed(DepartmentSeeder::class);
        $this->assertSame(22, Department::count());
    }
}
