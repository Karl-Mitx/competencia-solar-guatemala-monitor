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
        return view('dashboard', $this->data($request));
    }

    public function map(FilterRequest $request)
    {
        return view('map', $this->data($request));
    }

    public function reports(FilterRequest $request)
    {
        return view('reports', $this->data($request));
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
