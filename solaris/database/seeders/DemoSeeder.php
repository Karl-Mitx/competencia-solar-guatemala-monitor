<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Services\EnergyService;
use App\Services\GenerationService;
use App\Services\ProjectionService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DepartmentSeeder::class);

        // Fictional demonstrative assets: the catalogue does not claim real installations.
        $panels = collect([
            ['brand' => 'Solaris', 'model' => 'Mono 450', 'nominal_power_kw' => 0.450],
            ['brand' => 'Solaris', 'model' => 'Bifacial 550', 'nominal_power_kw' => 0.550],
            ['brand' => 'Helio', 'model' => 'N-Type 600', 'nominal_power_kw' => 0.600],
            ['brand' => 'Helio', 'model' => 'Compact 400', 'nominal_power_kw' => 0.400],
            ['brand' => 'Aurora', 'model' => 'Legacy 330', 'nominal_power_kw' => 0.330, 'is_active' => false],
        ])->map(fn ($data) => SolarPanel::updateOrCreate(
            ['brand' => $data['brand'], 'model' => $data['model']],
            $data + ['is_active' => true],
        ));

        $names = [
            'Horizonte Metropolitano', 'Sol del Motagua', 'Luz de Antigua', 'Altiplano Vivo',
            'Costa del Sol', 'Amanecer de Oriente', 'Luz de Atitlán', 'Cumbres de Totonicapán',
            'Energía de Xelajú', 'Sol del Pacífico', 'Sendero de Retalhuleu', 'Luz de los Volcanes',
            'Horizonte Cuchumatán', 'Sol del Quiché', 'Valle de Salamá', 'Bosque de Luz',
            'Horizonte Maya', 'Caribe Solar', 'Valle del Sol', 'Luz de Chiquimula',
            'Montaña de Jalapa', 'Amanecer de Jutiapa',
        ];
        $energy = app(EnergyService::class);
        $generation = app(GenerationService::class);
        $projection = app(ProjectionService::class);
        $currentMonth = CarbonImmutable::now()->startOfMonth();
        $departments = Department::orderBy('code')->get();
        $farmIndex = 0;

        foreach ($departments as $departmentIndex => $department) {
            // Two locations in ten departments, one in each of the other twelve: 32 farms.
            $locations = $departmentIndex < 10 ? 2 : 1;
            for ($site = 0; $site < $locations; $site++, $farmIndex++) {
                $name = $names[$departmentIndex].($site === 1 ? ' · Comunidad' : '');
                $farm = SolarFarm::updateOrCreate(['name' => $name], [
                    'department_id' => $department->id,
                    'location_name' => 'Sector '.($site + 1).', '.$department->name,
                    'latitude' => $department->latitude + ($site === 1 ? 0.036 : -0.018),
                    'longitude' => $department->longitude + ($site === 1 ? -0.026 : 0.022),
                    'families_count' => 85 + ($farmIndex * 47 % 520),
                    'is_active' => $farmIndex !== 31,
                    'commissioned_at' => $currentMonth->subMonths(18 + ($farmIndex % 20))->toDateString(),
                    'notes' => 'Instalación ficticia para demostración. Datos sintéticos; no representan mediciones oficiales.'
                        .($farmIndex === 31 ? ' Desactivada después del último mes registrado.' : ''),
                ]);

                $farm->panels()->sync([
                    $panels[$farmIndex % 4]->id => ['quantity' => 240 + ($farmIndex * 83 % 1500)],
                    $panels[($farmIndex + 1) % 4]->id => ['quantity' => 60 + ($farmIndex * 19 % 280)],
                ]);
                $farm->unsetRelation('panels');
                $capacity = $energy->capacity($farm);

                for ($monthsAgo = 12; $monthsAgo >= 1; $monthsAgo--) {
                    $period = $currentMonth->subMonths($monthsAgo);
                    // Illustrative dry/rainy season variation, not a meteorological model.
                    $season = [1 => 1.08, 1.12, 1.16, 1.10, 0.94, 0.87, 0.93, 0.89, 0.85, 0.90, 1.00, 1.06][$period->month];
                    $expected = round($capacity * 4.35 * $period->daysInMonth * $season, 2);
                    $performance = 0.91 + (($farmIndex * 7 + $monthsAgo * 3) % 18) / 100;
                    if (($farmIndex + $monthsAgo) % 9 === 0 || ($monthsAgo === 1 && $farmIndex % 7 === 0)) {
                        $performance = 0.64 + ($farmIndex % 10) / 100;
                    }
                    $data = [
                        'solar_farm_id' => $farm->id,
                        'period' => $period->toDateString(),
                        'real_kwh' => round($expected * $performance, 2),
                        'expected_kwh' => $expected,
                    ];
                    $existing = $farm->generations()->where('period', $period->toDateString())->first();
                    $generation->save($data, $existing);
                }

                // Backtest predictions use only older observations and are labelled as retrospective.
                $projection->generate($farm, 3, $currentMonth->subMonths(3));
                if ($farm->is_active) {
                    $projection->generate($farm);
                }
            }
        }
    }
}
