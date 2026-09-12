<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Department;
use App\Models\SolarFarm;
use App\Services\AnalyticsService;

class DashboardController extends Controller
{
    public function __construct(private AnalyticsService $analytics) {}

    private function data(FilterRequest $request): array
    {
        $filters = array_filter($request->validated(), fn ($value) => $value !== null && $value !== '');

        return ['dashboard' => $this->analytics->dashboard($filters), 'filters' => $filters,
            'departments' => Department::orderBy('name')->get(), 'farms' => SolarFarm::orderBy('name')->get()];
    }

    public function index(FilterRequest $request)
    {
        $data = $this->data($request);
        $readiness = app(\App\Services\ProjectionReadiness::class);
        $months = $readiness->requiredMonths();
        $data['missingHistory'] = $this->analytics->filterFarms(SolarFarm::query(), $data['filters'])
            ->where('is_active', true)
            ->with(['generations' => fn ($query) => $query->whereIn('period', array_map(fn ($month) => $month.'-01', $months))])
            ->orderBy('name')->get()
            ->map(fn ($farm) => [
                'id' => $farm->id,
                'name' => $farm->name,
                'months' => $readiness->missingMonths($farm->generations->map(fn ($record) => $record->period->format('Y-m'))->all()),
            ])->filter(fn ($farm) => count($farm['months']) > 0)->values();

        return view('dashboard', $data);
    }

    public function map(FilterRequest $request)
    {
        return view('map', $this->data($request));
    }

    public function reports(FilterRequest $request)
    {
        return view('reports', $this->data($request));
    }

    public function reportPrint(FilterRequest $request)
    {
        return view('reports-print', $this->data($request));
    }

    public function alerts(FilterRequest $request)
    {
        $filters = array_filter($request->validated());
        if (($filters['status'] ?? '') === 'inactive') {
            $filters['status'] = 'active';
        }

        return view('alerts.index', ['alerts' => $this->analytics->alerts($filters)->paginate(15)->withQueryString(),
            'departments' => Department::orderBy('name')->get(), 'farms' => SolarFarm::orderBy('name')->get(), 'filters' => $filters]);
    }

    public function export(FilterRequest $request)
    {
        $rows = $this->analytics->dashboard($request->validated())['ranking'];

        $csv = app(\App\Services\ReportCsv::class);

        return response()->streamDownload(fn () => print($csv->content($rows)), $csv->filename(), ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
